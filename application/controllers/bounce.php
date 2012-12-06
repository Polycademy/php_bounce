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
	}
	
	public function mission($id){
	
		$this->_view_data += array(
			'page_title'	=> 'Missions PHP Bounce',
			'code_submit'	=> $this->router->fetch_class(),
		);
	
		$this->_load_views('bounce_view');
	
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