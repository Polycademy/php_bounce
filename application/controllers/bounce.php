<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bounce extends CI_Controller {

	private $_view_data = array();

	public function __construct(){
	
		parent::__construct();
		$this->_view_data = $this->config->item('php_bounce');
		$this->load->model('Mission_model');
		
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
			'code_submit'	=> $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $id,
			'mission_data'	=> $mission_data,
		);
	
		$this->_load_views('mission_view');
	
	}
	
	//do some ajax loaded stuff...
	public function mission_ajax_execute($id){
	
		//get the id(check if the mission exists), return false (if no data)
		//get the mission data and parameters
		//start executing the code->
		//pass everything into execute_model
		//lint, whitelist, parse, missionchecker, execute (THESE should be in a model)
	
		//returns a json encoded array
		$this->load->view('mission_ajax_execute_view'); //should be used as data and not output
		
		//array with 2 values, line number & error
		//or array with output
	
	}
	
	//(json returned output)
	//needs id as get
	//needs code as json post
	public function execute($id){
	
	}
	
	private function _load_views($main){
	
		$this->load->view('header_view', $this->_view_data);
		$this->load->view($main, $this->_view_data);
		$this->load->view('footer_view', $this->_view_data);
	
	}
	
}