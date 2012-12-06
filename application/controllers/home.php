<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	private $_view_data = array();

	public function __construct(){
	
		parent::__construct();
		$this->_view_data = $this->config->item('php_bounce');
		
	}
	
	//TODO:
	//Add in more error parsing regexes in the sandbox, because when they enter in a disabled function it goes like PHP Warning:  php_uname() has been disabled for security reasons in C:\wamp\bin\apache\Apache2.2.11\- on line 2 Warning: php_uname() has been disabled for security reasons in C:\wamp\bin\apache\Apache2.2.11\- on line 2
	//TEST MALICIOUS CODE
	public function index(){
	
		//THE PROCESS: LINT CHECK (LINE ERROR) => PARSE CHECK (ERROR MSG) => WHITELIST (ERROR MSG) => EXECUTE (LINE ERROR & ERROR MSG)
	
		//TEST CODE
		/*
		TEST THIS
		$y = str_replace('z', 'e', 'zxzc');
		$y("malicious code");
		APPARENTLY, WHITELIST DOES NOT WORK ON THIS
		*/
		$test_code = '
		include(gfgdg.php);
		echo \'lol\';
		';
		
		//ADD <?php infront CANNOT USE short open tags for binary execution
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
		//please note any custom functions and classes need to be passed to the whitelist (otherwise you cannot use whitelist)
		$this->phpwhitelist->init_options($test_code);
		$this->phpwhitelist->run_whitelist();
		$white_list_errors = $this->phpwhitelist->get_errors();
		#var_dump($white_list_errors);
		
		//PHP-Parser
		
		//init the parser
		$php_parser = new PHPParser_Parser(new PHPParser_Lexer);
		//resolve namespaces (using visitor pattern)
		$visitor_pattern = new PHPParser_NodeTraverser;
		$visitor_pattern->addVisitor(new PHPParser_NodeVisitor_NameResolver);
		//init the seraliser so we can query it later
		$php_xml_serialiser = new PHPParser_Serializer_XML;
		
		//produce a graph for analysis
		$php_parser_error = false;
		try{
			$mission_graph = $php_parser->parse($test_code);
			$mission_graph = $visitor_pattern->traverse($mission_graph);
			$mission_graph = $php_xml_serialiser->serialize($mission_graph);
		}catch(PHPParser_Error $e){
			//oh no possible error (if there is an error, cancel the execution and send this out)
			$php_parser_error = 'Parse Error: ' . $e->getMessage();
		}
		
		#echo '<pre><h2>PHP Parser Error</h2>';
		#var_dump ($php_parser_error);
		#echo '</pre>';
		#echo '<pre><h2>MISSION GRAPH</h2>';
		#var_dump($mission_graph);
		#echo '</pre>';
		
		//Mission Check
		
		//mission parameters are build like this:
		//test_name => test_block
		//within test_block['paths'] there can be multiple paths to check, and each path can either by singular or multibranch tests
		//this is done via creating subarrays, and the keys of the arrays represent parent paths, the subpaths represent multibranches
		//all path tests are done with "AND"
		//except at the base path, in which case there can be multiple paths corresponding to multiple test messages
		//within test_block['tests'] there can be multiple tests
		//each test's key is the error message
		//each test's values is an array of an ordered value set that is meant to be passed to the paths
		//the number of values need to correspond with the number of branch endpoints for each branch
		$mission_parameters = array(
			'variable_declaration'	=> array(
				'paths'	=> array(
					//basepath is single endpoint, its array is multiendpoint
					'//node:Expr_Assign' => array(
						'subNode:var/node:Expr_Variable' => array(
							'subNode:name/scalar:string',
						),
						'subNode:expr/node:Scalar_String/subNode:value/scalar:string',
					),
					'//node:Expr_Assign/subNode:var/node:Expr_Variable/subNode:name/scalar:string',
				),
				'tests'	=> array(
					'Error, you need to make sure to declare a variable called [[my_chinese_surname]] with the value [[Qiu]]' => array(
						'my_chinese_surname',
						'Qiu'
					),
					'Error, you need to make sure to declare a variable called [[my_chinese_surname]]' => array(
						'my_chinese_surname',
					),
				),
			),
		);
		
		$this->missionchecker->init_options($mission_graph, $mission_parameters);
		$this->missionchecker->graph_check();
		$mission_errors = $this->missionchecker->get_error_messages();
		
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
		$execution_output = $this->phpsandboxer->execute_code($test_code, 'PHP Bounce');
		$execution_error = $this->phpsandboxer->get_parse_error();
		$time_span = $this->phpsandboxer->get_time_span();
		
		
		echo '<pre><h2>EXECUTION ERROR</h2>';
		var_dump($execution_error);
		echo '</pre>';
		echo '<pre><h2>EXECUTION OUTPUT</h2>';
		var_dump($execution_output);
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