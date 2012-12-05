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
		$my_chinese_surname = \'Qiu\';
		$my_chinese_firstname = \'Yulong\';
		
		if($my_chinese_surname == \'Qiu\'){
			echo true;
		}else{
			return false;
		}
		';
		
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
		//mission parameters correspond to the mission_graph, you're doing a loose array search
		
		//init the parser
		$php_parser = new PHPParser_Parser(new PHPParser_Lexer);
		//resolve namespaces (using visitor pattern)
		$visitor_pattern = new PHPParser_NodeTraverser;
		$visitor_pattern->addVisitor(new PHPParser_NodeVisitor_NameResolver);
		//init custom convert traverser
		//note that I changed NodeAbstract to have public variables
		#$php_convertor = new PHPParser_ConvertorTraverser;
		
		//init the seraliser so we can query it later
		$php_xml_serialiser = new PHPParser_Serializer_XML;
		
		
		//produce a graph for analysis
		//AST is a abstract syntax tree, the graph is an AST
		$php_parser_error = false;
		try{
			$mission_graph = $php_parser->parse($test_code);
			$mission_graph = $visitor_pattern->traverse($mission_graph);
			#$mission_graph = $php_convertor->traverse($mission_graph);
			$mission_graph = $php_xml_serialiser->serialize($mission_graph);
		}catch(PHPParser_Error $e){
			//oh no possible error (if there is an error, cancel the execution and send this out)
			$php_parser_error = 'Parse Error: ' . $e->getMessage();
		}
		
		#echo '<pre><h2>PHP Parser Error</h2>';
		#var_dump ($php_parser_error);
		#echo '</pre>';
		#echo '<pre><h2>MISSION GRAPH</h2>';
		var_dump($mission_graph);
		#echo '</pre>';
		
		//begin mission checking
		
		//$mission_parameters:
		//ALWAYS START WITH THE ERROR INDEX (if no error, why bother checking?)
		//THEN TREE BRANCH
		//LAST TREE ELEMENT IS ALWAYS RESULT (if not using result, just put whatever in, you just need something)
		
		//testing: "echo true;"
		#$mission_parameters['echo_true_check']['stmt_echo']['subnodes']['exprs'][0]['expr_constfetch']['subnodes']['name']['name']['subnodes']['parts'][0]['true'] = '';
		
		//Mission parameters
		//error_index
		//value (of query) => query_path (value of query can be (string)'false' for no check)
		
		//This is filter by subnodes, it can get quite complicated 
		#/meadinkent/record[comp_div='MENSWEAR' and sty_ret_type='ACCESSORIES']
		//there is 2 final endpoints here
		$mission_parameters = array(
			'variable_declaration'	=> array(
				'paths'	=> array(
					//THIS GOES IN ORDER OF FINAL ENDPOINTS
					'//node:Expr_Assign' => array(
						'subNode:var/node:Expr_Variable' => array(
							'subNode:Name/scalar:string',
						),
						'subNode:expr/node:Scalar_String/subNode:value/scalar:string',
					),
				),
				'tests'	=> array(
					//THIS GOES IN ORDER OF FINAL ENDPOINTS
					'my_chinese_surname'	=> 'Error, you need to make sure to declare a variable called [[my_chinese_surname]]',
					'Qiu'	=> 'Error, you need to make sure the variable [[my_chinese_surname]] is equal to [[Qiu]]',
				),
			),
		);
		
		$this->missionchecker->init_options($mission_graph, $mission_parameters);
		#$this->missionchecker->graph_check();
		#$this->missionchecker->run_check();
		
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