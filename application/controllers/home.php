<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	private $_view_data = array();

	public function __construct(){
	
		parent::__construct();
		$this->_view_data = $this->config->item('php_bounce');
		
	}
	
	//TODO:
	//Parse Check
	//Parse Check parameters and friendly error messages
	//Add in Whitelist
	//Add in more error parsing regexes, because when they enter in a disabled function it goes like PHP Warning:  php_uname() has been disabled for security reasons in C:\wamp\bin\apache\Apache2.2.11\- on line 2 Warning: php_uname() has been disabled for security reasons in C:\wamp\bin\apache\Apache2.2.11\- on line 2
	//TEST MALICIOUS CODE
	public function index(){
	
		//always add <?php in front of it
		/*
		TEST THIS
		$y = str_replace('z', 'e', 'zxzc');
		$y("malicious code");
		APPARENTLY, WHITELIST DOES NOT WORK ON THIS
		*/
		
		$test_code = ' echo \'lol\';
		
		#var_dump(php_uname(\'n\'));
		var_dump($_ENV);
		
		var_dump($_SERVER);
		
		
		getenv(\'SERVER_NAME\');';
		
		//THE PROCESS: LINT CHECK (LINE ERROR) => PARSE CHECK (ERROR MSG) => WHITELIST (ERROR MSG) => EXECUTE (LINE ERROR & ERROR MSG)
		//MAKE SURE TO CHANGE THE PHP BINARY FOR the DESKTOP DEVELOPMENT WHEN CHANGING...
		
		//All Test Code, whether it is in phplint, phpsandboxer, phpparser, phpwhitelist will require <?php ahead of it.
		//short open tags cannot be used here in execution, it will fail. It require <?php
		if(!preg_match('/\A^(<\?php)(\s)+?/m', $test_code)){
			$test_code = '<?php ' . $test_code;
		}else{
			$test_code = $test_code;
		}
		
		//Remember to htmlentities the code when it is being displayed back into the browser's textarea or whatever
		#echo '<pre><h2>CODE</h2>';
		#var_dump(htmlentities($test_code));
		#echo '</pre>';
		
		$this->phplint->init_binary($this->config->item('php_binary'));
		$lint_checked_code = $this->phplint->lint_string($test_code, 'PHP Bounce'); // false or true
		$syntax_error = $this->phplint->get_parse_error(); //gives error type and also error number line which works
		
		echo '<pre><h2>LINT CHECKED CODE</h2>';
		var_dump($lint_checked_code);
		echo '</pre>';
		echo '<pre><h2>SYNTAX ERROR</h2>';
		var_dump($syntax_error);
		echo '</pre>';
		
		//WHITELIST PLACEHOLDER
		
		//PHP-Parser AKA Mission Check
		//The whole point of this parser is to CHECK for:
		/*
			variable declarations
			function declarations
			constant declarations
			output
		*/
		//Therefore each mission challenge, will have its own parameters for success
		//do a fizzbuzz challenge lol
		
		$mission_parameters = array(
			'variables'	=> array(
				'VARIABLE_NAME'	=> 'VARIABLE_VALUE',
			),
			'functions'	=> array(
				'FUNCTION_NAME'	=> 'FUNCTION_OUTPUT'
			),
			'constants'	=> array(
				'CONSTANT_NAME'	=> 'CONSTANT_VALUE',
			),
		);
		
		//This will be matched to the returned mission errors, and a final array of error messages will be outputted
		$mission_error_msgs = array(
		);
		
		//init the parser
		$mission_parser = new PHPParser_Parser(new PHPParser_Lexer);
		//init the visitor
		$mission_visitor = new PHPParser_NodeTraverser;
		//add the namespace_resolver visitor
		$mission_visitor->addVisitor(new PHPParser_NodeVisitor_NameResolver);
		//mission_corrector
		$mission_visitor->addVisitor(new PHPParser_NodeVisitor_Corrector($mission_parameters));
		
		//init the errors, this can either be a string, or an array
		$mission_error = false;
		try{
			//produce a graph for analysis
			//AST is a abstract syntax tree the graph is an AST
			$mission_graph = $mission_parser->parse($test_code);
			//the mission_visitor can overwrite the mission_graph, we are not going to do that
			//instead we are going to check based on parameters and return an array or false of mission errors including line numbers
			$mission_error = $mission_visitor->traverse($mission_graph);
		}catch(PHPParser_Error $e){
			//oh no possible error
			$mission_error = 'Parse Error: ' . $e->getMessage();
		}
		
		echo '<pre><h2>MISSION GRAPH</h2>';
		var_dump($mission_graph);
		echo '</pre>';
		
		//time to execute
		$fake_server_env_prepend_file = APPPATH . 'helpers/phpsandbox_prepend_helper.php';
		$sandbox_options = array(
			'directory_protection'	=> array(
				$fake_server_env_prepend_file,
			),
		);
		$this->phpsandboxer->init_options($sandbox_options);
		$this->phpsandboxer->init_binary($this->config->item('php_binary'));
		$this->phpsandboxer->init_env($fake_server_env_prepend_file);
		$this->phpsandboxer->build_cli_options();
		$this->phpsandboxer->execute_code($test_code, 'PHP Bounce');
		$execution_error = $this->phpsandboxer->get_parse_error();
		$time_span = $this->phpsandboxer->get_time_span();
		
		echo '<pre><h2>EXECUTION ERROR</h2>';
		var_dump($execution_error);
		echo '</pre>';
		echo '<pre><h2>EXECUTION TIME</h2>';
		var_dump($time_span);
		echo '</pre>';
		
		$this->_view_data += array(
			'page_title'	=> $this->config->item('site_name', 'php_bounce'),
			'code_submit'	=> $this->router->fetch_class(),
		);
	
		$this->_load_views('home_view');
	
	}
	
	public function see(){
		phpinfo();
	}
	
	private function _load_views($main){
	
		$this->load->view('header_view', $this->_view_data);
		$this->load->view($main, $this->_view_data);
		$this->load->view('footer_view', $this->_view_data);
	
	}
	
}