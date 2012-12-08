<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bounce extends CI_Controller {

	private $_view_data = array();

	public function __construct(){
	
		parent::__construct();
		$this->_view_data = $this->config->item('php_bounce');
		$this->load->model('Mission_model');
		$this->load->model('Execute_model');
		
	}
	
	public function index(){
	
		//show all missions here
		//$missions is returned as an array
		$missions = $this->Mission_model->get_all_missions();
		
		$this->_view_data += array(
			'page_title'	=> 'Mission List PHP Bounce',
			'missions'		=> $missions,
		);
	
		$this->_load_views('bounce_view');
		
	}
	
	public function mission($id){
	
		$mission_data = $this->Mission_model->get_mission($id);
	
		$this->_view_data += array(
			'page_title'	=> 'Missions PHP Bounce',
			'code_submit'	=> $this->router->fetch_class() . '/ajax_execute/' . $id . '/false/true',
			'mission_data'	=> $mission_data,
		);
	
		$this->_load_views('mission_view');
	
	}
	
	/**
	 * Controller for AJAX execution of code
	 *
	 * Takes ID for the mission, allows options to run whitelist or run parameters. However this run_whitelist is insecure and should be determined
	 * on a database mission level. This will have to be changed in the next version.
	 * 
	 * @param int $id
	 * @param string $run_whitelist
	 * @param string $run_parameters
	 * @return json output
	 */
	public function ajax_execute($id, $run_whitelist = 'false', $run_parameters = 'true'){
	
		//get the mission data, code and parameters
		$test_code = $this->input->post('code');
		$mission_data = $this->Mission_model->get_mission($id);
		$php_binary = $this->config->item('php_binary');
		
		if(empty($mission_data)){
			$this->_ajax_execute_error('There is no data at mission #' . $id);
			return false;
		}
		
		if(empty($test_code)){
			$this->_ajax_execute_error('No code to execute!');
			return false;
		}
		
		//building up the options in order of execution...
		
		$options[] = 'lint';
		
		$whitelist = false;
		if($run_whitelist != 'false'){
			$options[] = 'whitelist';
			if(!empty($mission_data['whitelist'])){
				$whitelist = explode(',', $mission_data['whitelist']);
			}
		}
		
		$mission_parameters = false;
		if($run_parameters != 'false'){
			$options[] = 'parse';
			$options[] = 'mission_check';
			$mission_parameters = $mission_data['parameters'];
		}
		
		$options[] = 'execute';
		
		//initating the options for execute model, then running the exection
		$this->Execute_model->init_options($test_code, $php_binary, $mission_parameters, $whitelist);
		
		//we are trying to catch an exception if any exceptional errors arise (errors due to program flow)
		try{
			$output = $this->Execute_model->run($options);
		} catch (Exception $e) {
			$this->_ajax_execute_error($e->getMessage());
		}
		
		//this captures any non-exceptional errors, that is errors that the user put into the code
		if(!$output){
			$this->_ajax_execute_error();
		}
		
		$output = array(
			0	=> array(
				'line'		=> false,
				'message'	=> $output,
			),
		);
		
		//response gets passed to the view as $response
		//$output is put into an array
		$this->_view_data += array(
			'response'	=> $output,
		);
		
		$this->load->view('json_view', $this->_view_data);
		
		return true;
	
	}
	
	/**
	 * Returns AJAX error messages for the bounce REPL
	 *
	 * Makes sure that the error messages are in a JSON format, also allows custom error messages.
	 * If custom error is empty, then it grab the errors from the Execute_model
	 * It is important to understand the error model, because it is complicated.
	 * $errors is always an array.
	 * The first dimension contains a plurality of error arrays
	 * The second dimension is a single array of the details of that particular error
	 * array(
	 * 		0 => array(
	 *			'line'	=> bool/int
	 *			'message'	=> 'string',
	 *		),
	 * );
	 * Therefore it always specified like this $errors[] = array('line'=>INT, 'message'=>STRING);
	 * 
	 * @param string $custom_error
	 * @return boolean
	 */
	private function _ajax_execute_error($custom_error = false){
	
		//if we have a custom error, then lets use it instead
		$errors = (!empty($custom_errors)) ? $custom_errors : $this->Execute_model->get_errors();
		
		//well if it is a string then it can simply be outputted
		if(is_string($errors)){
		
			$errors = array(
				0 = array(
					'line'		=> false,
					'message'	=> $errors,
				)
			);
			
		}
		
		$this->_view_data += array(
			'response'	=> $errors,
		);
		
		$this->load->view('json_view', $this->_view_data);
		
		return true;
		
	}
	
	private function _load_views($main){
	
		$this->load->view('header_view', $this->_view_data);
		$this->load->view($main, $this->_view_data);
		$this->load->view('footer_view', $this->_view_data);
	
	}
	
}