<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mission_editor extends CI_Controller {

	private $_view_data = array();

	public function __construct(){
	
		parent::__construct();
		$this->_view_data = $this->config->item('php_bounce');
		$this->load->model('Mission_model'); //models are capitalised in classes, not in files, whereas libraries are capitalised in files but not in classes... weird
		
	}
	
	//function for updating the mission parameters for any mission
	//password protect please
	public function index(){
	
		//first lets get all the missions
		//$missions is returned as an array
		$missions = $this->Mission_model->get_all_missions();
		
		$this->_view_data += array(
			'page_title'	=> 'Missions Editor PHP Bounce',
			'missions'		=> $missions,
		);
	
		$this->_load_views('mission_editor_view');
	
	}
	
	public function add(){
		
		$status = false;
		
		$validation_rules = array(
			array(
				'field'   => 'title',
				'label'   => 'Mission Title',
				'rules'   => 'trim|required|alpha_dash',
			),
			array(
				'field'   => 'description',
				'label'   => 'Mission Description',
				'rules'   => 'trim|required',
			),   
			array(
				'field'   => 'number',
				'label'   => 'Mission Number',
				'rules'   => 'trim|required|integer|callback_mission_number_check',
			),
			array(
				'field'   => 'parameters',
				'label'   => 'Mission Parameters',
				'rules'   => 'required',
			)
		);
		
		$this->form_validation->set_rules($validation_rules);
		
		if($this->form_validation->run() == true){		
		
			//YES AWESOME... (insert to database)
			$new_mission = array(
				'title'			=> $this->input->post('title', true),
				'description'	=> $this->input->post('description'),
				'mission_number'=> $this->input->post('number', true),
				'parameters'	=> $this->input->post('parameters'),
			);
			
			if(!$this->Mission_model->add_mission($new_mission)){
				$status = '<li>Was unable to add the mission to the database.</li>';
			}else{
				$status = '<li>Entered information into the database! Thanks!</li>';
			}
		
		}else{
		
			$status = validation_errors('<li>', '</li>');
			
		}
		
		$this->_view_data += array(
			'page_title'	=> 'Missions Editor Add PHP Bounce',
			'type'			=> 'add',
			'editor_submit'	=> $this->router->fetch_class() . '/' . $this->router->fetch_method(),
			'status'		=> $status,
		);
	
		$this->_load_views('mission_editor_addupdate_view');
	
	}
	
	public function mission_number_check($num){
	
		$check = $this->Mission_model->mission_number_check($num);
		if(!empty($check)){
			$this->form_validation->set_message('mission_number_check', 'You cannot use the same mission number. Edit the other one first!');
			return false;
		}
		
		return true;
		
	}
	
	//UPDATE can DELETE aswell, so we have A DELETE BUTTON added in with the type
	public function update($id){
	
		$this->_view_data += array(
			'page_title'	=> 'Missions Editor Update PHP Bounce',
			'type'			=> 'update',
			'editor_submit'	=> $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $id,
		);
	
		$this->_load_views('mission_editor_addupdate_view');
	
	}
	
	private function _load_views($main){
	
		$this->load->view('header_view', $this->_view_data);
		$this->load->view($main, $this->_view_data);
		$this->load->view('footer_view', $this->_view_data);
	
	}
	
}