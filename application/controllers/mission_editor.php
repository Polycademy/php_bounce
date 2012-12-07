<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mission_editor extends CI_Controller {

	private $_view_data = array();

	public function __construct(){
	
		parent::__construct();
		$this->_view_data = $this->config->item('php_bounce');
		$this->load->model('Mission_model'); //models are capitalised in classes, not in files, whereas libraries are capitalised in files but not in classes... weird
		$this->load->model('Execute_model');
		
	}
	
	//function for updating the mission parameters for any mission
	//password protect please
	public function index(){
	
		//first lets get all the missions
		//$missions is returned as an array
		$missions = $this->Mission_model->get_all_missions();
		//$missions['parameters'] returns as an array, so we need to export it to be shown
		foreach($missions as &$mission){
			$mission['parameters'] = var_export($mission['parameters'], true);
		}
		
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
				'rules'   => 'trim|required',
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
				'field'   => 'whitelist',
				'label'   => 'Mission Whitelist',
				'rules'   => '',
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
				'whitelist'		=> trim($this->input->post('whitelist'), ','),
			);
			
			if(!$this->Mission_model->add_mission($new_mission)){
				$status = '<li>Was unable to add the mission to the database.</li>';
				if($this->Mission_model->eval_error){
					$status .= '<li>Eval Error: ' . $this->Mission_model->eval_error['message'] . ' on ' . $this->Mission_model->eval_error['line'] . '</li>';
				}
			}else{
				$status = '<li>Entered information into the database! Thanks!</li>';
			}
		
		}else{
		
			$status = validation_errors('<li>', '</li>');
			
		}
		
		$this->_view_data += array(
			'page_title'	=> 'Missions Add Editor PHP Bounce',
			'type'			=> 'add',
			'editor_submit'	=> $this->router->fetch_class() . '/' . $this->router->fetch_method(),
			'xml_submit'	=> $this->router->fetch_class() . '/mission_xml_parsing',
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
	
		$status = false;
		
		$validation_rules = array(
			array(
				'field'   => 'title',
				'label'   => 'Mission Title',
				'rules'   => 'trim|required',
			),
			array(
				'field'   => 'description',
				'label'   => 'Mission Description',
				'rules'   => 'trim|required',
			),   
			array(
				'field'   => 'number',
				'label'   => 'Mission Number',
				'rules'   => 'trim|required|integer',
			),
			array(
				'field'   => 'whitelist',
				'label'   => 'Mission Whitelist',
				'rules'   => '',
			),
			array(
				'field'   => 'parameters',
				'label'   => 'Mission Parameters',
				'rules'   => 'required',
			)
		);
		
		$this->form_validation->set_rules($validation_rules);
		
		if($this->form_validation->run() == true){
		
			$updated_mission = array(
				'title'			=> $this->input->post('title', true),
				'description'	=> $this->input->post('description'),
				'mission_number'=> $this->input->post('number', true),
				'parameters'	=> $this->input->post('parameters'),
				'whitelist'		=> trim($this->input->post('whitelist'), ','),
			);
			
			if(!$this->Mission_model->update_mission($id, $updated_mission)){
				$status = '<li>No update was made to mission #' . $id . '</li>';
				if($this->Mission_model->eval_error){
					$status .= '<li>Eval Error: ' . $this->Mission_model->eval_error['message'] . ' on ' . $this->Mission_model->eval_error['line'] . '</li>';
				}
			}else{
				$status = '<li>Updated mission #' . $id . '! Thanks!</li>';
			}
		
		}else{
		
			$status = validation_errors('<li>', '</li>');
			
		}
	
		//get the data
		$mission_data = $this->Mission_model->get_mission($id);
		//$missions['parameters'] returns as an array, so we need to export it to be shown
		$mission_data['parameters'] = var_export($mission_data['parameters'], true);
		
		$this->_view_data += array(
			'page_title'	=> 'Missions Update Editor PHP Bounce',
			'type'			=> 'update',
			'mission_data'	=> $mission_data,
			'status'		=> (empty($mission_data)) ? '<li>There isn\'t any mission with the id #' . $id . '. Go back, you cannot update here.</li>' : $status,
			'editor_submit'	=> $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $id,
			'xml_submit'	=> $this->router->fetch_class() . '/mission_xml_parsing',
		);
	
		$this->_load_views('mission_editor_addupdate_view');
	
	}
	
	//for ajax output of xml
	public function mission_xml_parsing(){
	
		$code = $this->input->post('code');
		
		if(empty($code)){
			$this->_execute_error_processing('No code was parsed into XML');
			return false;
		}
		
		if(!$this->Execute_model->init_options($code, $this->config->item('php_binary'))){
			$this->_execute_error_processing();
			return false;
		}
		
		if(!$this->Execute_model->parse()){
			$this->_execute_error_processing();
			return false;
		}
		
		$mission_graph = $this->Execute_model->get_parsed_mission_graph();
		
		$this->_view_data += array(
			'response'	=> $mission_graph,
		);
		$this->load->view('xml_view', $this->_view_data);
		
	}
	
	//$errors is for passing in custom errors
	protected function _execute_error_processing($errors = false){
	
		$errors = (!empty($errors)) ? $errors : $this->Execute_model->get_errors();
	
		#$this->firephp->log($errors);
		
		if(is_array($errors)){
			$errors = implode($errors);
		}
		
		$this->_view_data += array(
			'response'	=> $errors,
		);
		$this->load->view('xml_view', $this->_view_data);
		
		return true;
		
	}
	
	private function _load_views($main){
	
		$this->load->view('header_view', $this->_view_data);
		$this->load->view($main, $this->_view_data);
		$this->load->view('footer_view', $this->_view_data);
	
	}
	
}