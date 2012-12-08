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
	
	public function mission($num){
	
		$mission_data = $this->Mission_model->get_mission($num);
	
		$this->_view_data += array(
			'page_title'	=> 'Missions PHP Bounce',
			'code_submit'	=> $this->router->fetch_class() . '/ajax_execute/' . $mission_data['mission_number'] . '/true',
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
	public function ajax_execute($num, $run_parameters = 'true'){
	
		//$this->firephp->log($run_whitelist);
		//$this->firephp->log($run_parameters);
	
		//get the mission data, code and parameters
		$test_code = $this->input->post('code');
		$mission_data = $this->Mission_model->get_mission($num);
		$php_binary = $this->config->item('php_binary');
		
		//if $run_parameters is false, no point getting mission data
		if($run_parameters != 'false'){
			if(empty($mission_data)){
				$this->_ajax_execute_error('There is no data at mission #' . $num);
				return false;
			}
		}
		
		$id = $mission_data['id'];
		
		#$this->firephp->log('Mission Data Exists');
		
		if(empty($test_code)){
			$this->_ajax_execute_error('No code to execute!');
			return false;
		}
		
		#$this->firephp->log('Test Code Exists');
		
		//building up the options in order of execution...
		
		$options[] = 'lint';
		
		$whitelist = false;
		if(!empty($mission_data['whitelist'])){
			$options[] = 'whitelist';
			$whitelist = explode(',', $mission_data['whitelist']);
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
			return false; //need to end execution here
		}
		
		//this captures any non-exceptional errors, that is errors that the user put into the code
		//if output is strictly false to allow empty strings to pass through (as they are valid output)
		if($output === false){
			$this->_ajax_execute_error();
			return false; //need to end execution here
		}
		
		//if we are running against parameters, then we are in a mission, then add a success message
		if($run_parameters != 'false'){
			$output = $output . '<br /><span class="success">Well done you succeeded! Move on to the next mission!</span>';
		}
		//if not, then keep going
		
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
		$errors = (!empty($custom_error)) ? $custom_error : $this->Execute_model->get_errors();
		
		//well if it is a string then it can simply be outputted
		if(is_string($errors)){
		
			$errors = array(
				0 => array(
					'line'		=> false,
					'message'	=> $errors,
				)
			);
			
		}
		
		//$this->firephp->log($errors, 'At Ajax_Execute_Error');
		
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