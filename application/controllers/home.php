<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	private $_view_data = array();

	public function __construct(){
	
		parent::__construct();
		$this->_view_data = $this->config->item('php_bounce');
		
	}

	public function index(){
	
		//always begin with <?php code
		//only allows php code
		//ALSO this only tests code which has <?php on top of it.
		//we shall assume that the text input has no <?php
		//there for, you should always add <?php in front of it
		/*
		TEST THIS
		$y = str_replace('z', 'e', 'zxzc');
		$y("malicious code");
		APPARENTLY, WHITELIST DOES NOT WORK ON THIS
		*/
		
		$test_code = 'echo \'ll\';';
		
		//THE PROCESS: LINT CHECK (LINE ERROR) => PARSE CHECK (ERROR MSG) => WHITELIST (ERROR MSG) => EXECUTE (LINE ERROR & ERROR MSG)
		//MAKE SURE TO CHANGE THE PHP BINARY FOR the DESKTOP DEVELOPMENT WHEN CHANGING...
		if(!preg_match('/\A^(<\?|<\?php)(\s)+?/m', $test_code)){
			$lint_code = '<?php ' . $test_code;
		}else{
			$lint_code = $test_code;
		}
		
		//Remember to htmlentities the code when it is being displayed back into the browser's textarea or whatever
		echo '<pre>';
		var_dump(htmlentities($lint_code));
		echo '</pre>';
		
		$this->phplint->init_binary($this->config->item('php_binary'));
		$lint_checked_code = $this->phplint->lint_string($lint_code); // false or true
		$syntax_error = $this->phplint->get_parse_error(); //gives error type and also error number line which works
		
		var_dump($lint_checked_code);
		echo '<pre>';
		var_dump($syntax_error);
		echo '</pre>';
		
		//placeholder for PARSE CHECK and WHITELIST
		
		
		//time to execute
		$sandbox_options = array(
			#'chroot'			=> '/',
			'display_errors'	=> 'stderr',
			'directory_protection'	=> array(), //array of paths to be restricted by open_basedir
		);
		$this->phpsandboxer->init_options($sandbox_options);
		$this->phpsandboxer->init_binary($this->config->item('php_binary'));
		$this->phpsandboxer->init_env(APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'phpsandbox_prepend_helper.php');
		$this->phpsandboxer->build_cli_options();
		$this->phpsandboxer->execute_code($lint_code); //we don't run the lint code unless we need it to, first test.
		
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