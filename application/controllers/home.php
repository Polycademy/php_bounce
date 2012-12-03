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
		
		$test_code = '
		$my_name = \'Roge\' . \'Q\';
		';
		
		/*
		$my_chinese_surname = \'Qiu\';
		$my_chinese_firstname = \'Yulong\';
		
		if($my_chinese_surname == \'Qiu\'){
			echo true;
		}else{
			return false;
		}
		*/
		
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
		
		/*
		echo '<pre><h2>LINT CHECKED CODE</h2>';
		var_dump($lint_checked_code);
		echo '</pre>';
		echo '<pre><h2>SYNTAX ERROR</h2>';
		var_dump($syntax_error);
		echo '</pre>';
		*/
		
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
		
		//MISSION PARAMETERS CAN GET VERY VERY COMPLEX
		//THESE HAS TO CORRESPOND TO THE ARRAY/OBJECT
		//you should create a builder for this
		//kind of like parameters for each expression/statement
		//function $mission->parameter_expr_assign(NAME, TYPE OF VALUE, VALUES);
		//GO FROM MISSION, then test
		//YOU CANNOT GO FROM TEST to MISSION, there is too many possibilities...
		//in the mission corrector, all nodes have to converted to an array...
		$mission_parameters = array(
		);
		
		//This will be matched to the returned mission errors, and a final array of error messages will be outputted
		$mission_error_msgs = array(
		);
		
		//init the parser
		//init the visitor pattern (because the array has protected methods)
		//init the mission corrector as a visitor
		$php_parser = new PHPParser_Parser(new PHPParser_Lexer);
		//init custom traverser
		$php_traverser = new PHPParser_CustomTraverser;		
		
		
		#$visitor_pattern = new PHPParser_NodeTraverser;
		#$mission_corrector = new PHPParser_NodeVisitor_Corrector($mission_parameters);
		
		//add the namespace_resolver visitor
		//add the mission corrector as a visitor
		#$visitor_pattern->addVisitor(new PHPParser_NodeVisitor_NameResolver);
		#$visitor_pattern->addVisitor($mission_corrector);
		
		//init the errors, this can either be a string, or an array
		$php_parser_error = false;
		try{
			//produce a graph for analysis
			//AST is a abstract syntax tree the graph is an AST
			$mission_graph = $php_parser->parse($test_code);
			$mission_graph = $php_traverser->traverse($mission_graph);
			
			
			
			//the visitor_pattern can overwrite the mission_graph
			//we are going to use it to overwrite the object properties as array key and values instead
			#$mission_graph = $visitor_pattern->traverse($mission_graph);
			
			//PLACEHOLDER for ERROR parsing
			
		}catch(PHPParser_Error $e){
			//oh no possible error
			$php_parser_error = 'Parse Error: ' . $e->getMessage();
		}
		
		#echo '<pre><h2>PHP Parser Error</h2>';
		#var_dump ($php_parser_error);
		#echo '</pre>';
		echo '<pre><h2>MISSION GRAPH</h2>';
		var_dump ($mission_graph);
		echo '</pre>';
		#echo '<pre><h2>MISSION ERRORS</h2>';
		#var_dump($mission_corrector->get_errors());
		#echo '</pre>';
		
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
		
		/*
		echo '<pre><h2>EXECUTION ERROR</h2>';
		var_dump($execution_error);
		echo '</pre>';
		echo '<pre><h2>EXECUTION TIME</h2>';
		var_dump($time_span);
		echo '</pre>';
		*/
		
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