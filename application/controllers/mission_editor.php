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
	
		if(!$this->ion_auth->logged_in() AND !$this->ion_auth->is_admin()){
			redirect('auth/login');
		}
	
		//first lets get all the missions
		//$missions is returned as an array
		$missions = $this->Mission_model->get_all_missions();
		//$missions['parameters'] returns as an array, so we need to export it to be shown
		if(!empty($missions)){
		
			foreach($missions as &$mission){
			
				$mission['parameters'] = (!empty($mission['parameters'])) ? var_export($mission['parameters'], true) : $mission['parameters'];
				
			}
			
		}
		
		$this->_view_data += array(
			'page_title'	=> 'Missions Editor PHP Bounce',
			'missions'		=> $missions,
		);
	
		$this->_load_views('mission_editor_view');
	
	}
	
	public function add(){
	
		if(!$this->ion_auth->logged_in() AND !$this->ion_auth->is_admin()){
			redirect('auth/login');
		}
		
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
				'field'   => 'output',
				'label'   => 'Mission Output',
				'rules'   => '',
			),
			array(
				'field'   => 'whitelist',
				'label'   => 'Mission Whitelist',
				'rules'   => '',
			),
			array(
				'field'   => 'parameters',
				'label'   => 'Mission Parameters',
				'rules'   => '',
			),
			array(
				'field'   => 'default',
				'label'   => 'Mission Default',
				'rules'   => '',
			),
		);
		
		$this->form_validation->set_rules($validation_rules);
		
		if($this->form_validation->run() == true){		
		
			//YES AWESOME... (insert to database)
			$new_mission = array(
				'title'			=> $this->input->post('title', true),
				'description'	=> $this->input->post('description'),
				'mission_number'=> $this->input->post('number', true),
				'whitelist'		=> trim($this->input->post('whitelist'), ','),
				'output'		=> $this->input->post('output'),
				'parameters'	=> $this->input->post('parameters'),
				'default'		=> $this->input->post('default'),
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
			'xml_submit'	=> $this->router->fetch_class() . '/ajax_xml_parse',
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
	public function update($num){
	
		if(!$this->ion_auth->logged_in() AND !$this->ion_auth->is_admin()){
			redirect('auth/login');
		}
		
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
				'field'   => 'output',
				'label'   => 'Mission Output',
				'rules'   => '',
			),
			array(
				'field'   => 'whitelist',
				'label'   => 'Mission Whitelist',
				'rules'   => '',
			),
			array(
				'field'   => 'parameters',
				'label'   => 'Mission Parameters',
				'rules'   => '',
			),
			array(
				'field'   => 'default',
				'label'   => 'Mission Default',
				'rules'   => '',
			)
		);
		
		$this->form_validation->set_rules($validation_rules);
		
		if($this->form_validation->run() == true){
		
			$updated_mission = array(
				'title'			=> $this->input->post('title', true),
				'description'	=> $this->input->post('description'),
				'mission_number'=> $this->input->post('number', true),
				'whitelist'		=> trim($this->input->post('whitelist'), ','),
				'output'		=> $this->input->post('output'),
				'parameters'	=> $this->input->post('parameters'),
				'default'		=> $this->input->post('default'),
			);
			
			if(!$this->Mission_model->update_mission($num, $updated_mission)){
				$status = '<li>No update was made to mission #' . $num . '</li>';
				if($this->Mission_model->eval_error){
					$status .= '<li>Eval Error: ' . $this->Mission_model->eval_error['message'] . ' on ' . $this->Mission_model->eval_error['line'] . '</li>';
				}
			}else{
				$status = '<li>Updated mission #' . $num . '! Thanks!</li>';
			}
		
		}else{
		
			$status = validation_errors('<li>', '</li>');
			
		}
		
		//get the data
		$mission_data = $this->Mission_model->get_mission($num);
		
		//$missions['parameters'] returns as an array, so we need to export it to be shown
		//codemirror takes the slashes that var_export has away, so we need to re add it back in
		$mission_data['parameters'] = (!empty($mission_data['parameters'])) ? addslashes(var_export($mission_data['parameters'], true)) : $mission_data['parameters'];
		
		//This is because textarea weirdly removes any backslashes
		$mission_data['description'] = addslashes($mission_data['description']);
		
		$this->_view_data += array(
			'page_title'	=> 'Missions Update Editor PHP Bounce',
			'type'			=> 'update',
			'mission_data'	=> $mission_data,
			'status'		=> (empty($mission_data)) ? '<li>There isn\'t any mission at number ' . $num . '. Go back, you cannot update here.</li>' : $status,
			'editor_submit'	=> $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $num,
			'xml_submit'	=> $this->router->fetch_class() . '/ajax_xml_parse',
		);
	
		$this->_load_views('mission_editor_addupdate_view');
	
	}
	
	//for ajax output of xml
	public function ajax_xml_parse(){
	
		$code = $this->input->post('code');
		
		#var_dump($code);
		
		$php_binary = $this->config->item('php_binary');
				
		if(empty($code)){
			$this->_ajax_xml_error('No code to execute!');
			return false;
		}
		
		$options[] = 'parse';
		
		//initating the options for execute model, then running the execution
		$this->Execute_model->init_options($code, $php_binary);
		
		//we are trying to catch an exception if any exceptional errors arise (errors due to program flow)
		try{
			$output = $this->Execute_model->run($options);
		} catch (Exception $e) {
			$this->_ajax_xml_error($e->getMessage());
			return false;
		}
		
		//this captures any non-exceptional errors, that is errors that the user put into the code
		if(!$output){
			$this->_ajax_xml_error();
			return false;
		}
		
		$mission_graph = htmlentities($this->Execute_model->get_mission_graph());
		
		$this->_view_data += array(
			'response'	=> $mission_graph,
		);
		
		$this->load->view('xml_view', $this->_view_data);
		
		return true;
		
	}
	
	//$errors is for passing in custom errors
	protected function _ajax_xml_error($custom_error = false){
	
		//if we have a custom error, then lets use it instead
		$errors = (!empty($custom_error)) ? $custom_error : $this->Execute_model->get_errors();
		
		//there would only be xml error message, and we're not doing any json parsing
		if(is_array($errors)){
			$errors = $errors[0]['message'];
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