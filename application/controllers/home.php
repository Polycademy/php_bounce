<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	private $_view_data = array();

	public function __construct(){
	
		parent::__construct();
		$this->_view_data = $this->config->item('php_bounce');
		
	}

	public function index(){
	
		//MAKE SURE TO CHANGE THE PHP BINARY FOR the DESKTOP DEVELOPMENT WHEN CHANGING...
		#var_dump($this->config->item('php_binary'));
		$this->phplint->init_binary($this->config->item('php_binary'));
		
		//always begin with <?php code
		//only allows php code
		//ALSO this only tests code which has <?php on top of it.
		//we shall assume that the text input has no <?php
		//there for, you should always add <?php in front of it
		$test_code = '';
		
		
		$lint_checked_code = $this->phplint->lint_string($test_code); // false or true
		$syntax_error = $this->phplint->get_parse_error(); //gives error type and also error number line which works
		
		//need to test multiline
		
		var_dump($lint_checked_code);
		echo '<pre>';
		var_dump($syntax_error);
		echo '</pre>';


		if(ENVIRONMENT == 'development'){
		
		}
		
		$this->_view_data += array(
			'page_title'	=> $this->config->item('site_name', 'php_bounce'),
			'code_submit'	=> $this->router->fetch_class(),
		);
	
		$this->_load_views('home_view');
	
	}
	
	private function _load_views($main){
	
		$this->load->view('header_view', $this->_view_data);
		$this->load->view($main, $this->_view_data);
		$this->load->view('footer_view', $this->_view_data);
	
	}
	
}