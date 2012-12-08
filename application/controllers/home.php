<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	private $_view_data = array();

	public function __construct(){
	
		parent::__construct();
		$this->_view_data = $this->config->item('php_bounce');
		
	}
	
	public function index(){	
		
		$this->_view_data += array(
			'page_title'	=> $this->config->item('site_name', 'php_bounce'),
			'code_submit'	=> 'bounce/ajax_execute/false/false',
		);
	
		$this->_load_views('home_view');
	
	}
	
	private function _load_views($main){
	
		$this->load->view('header_view', $this->_view_data);
		$this->load->view($main, $this->_view_data);
		$this->load->view('footer_view', $this->_view_data);
	
	}
	
}