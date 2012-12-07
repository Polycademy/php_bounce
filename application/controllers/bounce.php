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
			'code_submit'	=> $this->router->fetch_class() . '/mission_ajax_execute/' . $id,
			'mission_data'	=> $mission_data,
		);
	
		$this->_load_views('mission_view');
	
	}
	
	//do some ajax loaded stuff...
	public function mission_ajax_execute($id){
	
		//get the mission data, code and parameters
		$test_code = $this->input->post('code');
		$mission_data = $this->Mission_model->get_mission($id);
		if(empty($mission_data)){
		
			$this->_mission_error_processing('There is no data at mission #' . $id);
			return false;
			
		}
		
		if(empty($test_code)){
		
			$this->_mission_error_processing('No code to execute!');
			return false;
			
		}
		
		//THIS IS GOING TO BE DETECTED FROM THE DATABASE!
		//whitelist of custom function names + class names to allow to be used
		$custom_whitelist = array(
			'my_function',
			'my_class',
		);
		
		$this->firephp->log($test_code, 'TEST CODE');
		
		//initiate the options
		if(!$this->Execute_model->init_options($test_code, $this->config->item('php_binary'), $mission_data['parameters'], $custom_whitelist)){
		
			$this->_mission_error_processing();
			return false;
			
		}
		
		$this->firephp->log('Passed Initiating Options');
		
		if(!$this->Execute_model->lint()){
		
			$this->_mission_error_processing();
			return false;
			
		}
		
		$this->firephp->log('Passed Lint');
		
		if(!$this->Execute_model->whitelist()){
		
			$this->_mission_error_processing();
			return false;
		
		}
		
		$this->firephp->log('Passed Whitelist');
		
		if(!$this->Execute_model->parse()){
		
			$this->_mission_error_processing();
			return false;
		
		}
		
		$this->firephp->log('Passed Parse');
		
		//do mission check
		if(!$this->Execute_model->mission_check()){
		
			$this->_mission_error_processing();
			return false;
			
		}
		
		$this->firephp->log('Passed Mission Check');
		
		$output = $this->Execute_model->execute();
		
		if(!$output){
		
			$this->_mission_error_processing();
			return false;
		
		}
		
		$this->firephp->log('Passed Output');
		
		//output is just that output, but errors will require more processing
		//errors may be just line errors, array of many errors or line error + line number to highlight
		
		//TODOOOOO!O!!!
		//THEREFORE we need to json encode the data and process them in javascript, not return them a template!!!
		
		//errors need to be like this
		/*
			=> these output to just the line
			array(
				0 => 'error message',
				1 => 'error message',
			);
			
			=>these need to be encoded and decoded
			
[0] =>
array(

['raw'] =>
'PHP Parse error: syntax error, unexpected end of file in - on line 1'
['type'] =>
'PHP Parse error'
['message'] =>
'syntax error, unexpected end of file'
['file'] =>
'PHP Bounce'
['line'] =>
1
) 
			
			array(
				0 => array(
					'type'		=> PHP Parse Error,
					'message'	=> 'Blah',
					'line'		=> 1,
				);
			);
		*/
		
		
		$this->_view_data += array(
			'response'	=> $output,
		);
		$this->load->view('json_view', $this->_view_data);
		
		return true;
	
	}
	
	//$errors is for passing in custom errors
	protected function _mission_error_processing($errors = false){
	
		$errors = (!empty($errors)) ? $errors : $this->Execute_model->get_errors();
	
		#$this->firephp->log($errors);
		
		if(is_string($errors)){
			$errors = array($errors);
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