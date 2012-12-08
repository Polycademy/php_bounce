<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Execute_model extends CI_Model {

	private $_errors = array();
	private $_test_code = '';
	private $_binary = '';
	private $_custom_whitelist = '';
	private $_parameters = array();
	private $_mission_graph = false;
	public $execution_timespan;

	public function __construct(){
	
		parent::__construct();
		
	}
	
	//init options
	//$parameters must be an array!!
	public function init_options($test_code, $binary, $parameters = array(), $custom_whitelist = ''){
	
		if(empty($test_code) OR empty($binary)){
			$this->_errors[] = 'Requires test code and binary location.';
			return false;
		}
		
		if(!file_exists($binary) || !is_executable($binary)) {
			$this->_errors[] = 'Binary not found!';
			return false;
		}
		
		//ADD <?php infront CANNOT USE short open tags for binary execution
		if(!preg_match('/\A^(<\?php)(\s)+?/m', $test_code)){
			$test_code = '<?php ' . $test_code;
		}
		
		$this->_test_code = $test_code;
		$this->_binary = $binary;
		$this->_custom_whitelist = $custom_whitelist;
		$this->_parameters = $parameters;
		
		return true;
	
	}
	
	/** (SINGLE RUN)
	* $options is an array of which checks to run
		array('lint', 'whitelist', 'parse', 'mission_check', 'execute');
	* returns false or string output
	*/
	
	/**
	 * Runs execution routine checks
	 *
	 * Take an array of options containing the names of all the checks to be done, then runs through each check.
	 * If any of the checks fail, then returns false. Otherwise returns the execution output.
	 * 
	 * @param array $options
	 * @return string/boolean
	 * @throws Exception If one of the defined check methods don't exist.
	 */
	public function run(array $options){
		
		foreach($options as $check_method){
			if(!method_exists($this, $check_method)){
				throw new Exception('The method ' . $check_method . ' does not exist in Execute_model.');
				return false;
			}
			if(!$output = $this->$check_method){
				return false;
			}
		}
		
		return $output;
		
		/*
		
		if(isset($options['lint'])){
			if(!$this->lint()){
				return false;
			}
		}
		
		if(isset($options['whitelist'])){
			if(!$this->whitelist()){
				return false;
			}
		}
		
		//you can do a parse without a mission check
		if(isset($options['parse'])){
			if(!$this->parse()){
				return false;
			}
		}
		
		//but you can't do a mission_check without a parse
		if(isset($options['mission_check']) AND isset($options['parse'])){
			if(!$this->mission_check()){
				return false;
			}
		}
		
		if(isset($options['execute'])){
			if(!$output = $this->execute()){
				return false;
			}
		}
		
		return $output;
		*/
		
	}
	
	//get the mission_graph
	public function get_mission_graph(){
		if(empty($this->_mission_graph)){
			return false;
		}
		return $this->_mission_graph;
	}
	
	//get errors
	public function get_errors(){
		
		if(!empty($this->_errors)){
			return $this->_errors;
		}
		return false;
		
	}
	
	//lint
	public function lint(){
	
		$this->phplint->init_binary($this->_binary);
		
		//if failed lint check
		if(!$this->phplint->lint_string($this->_test_code, 'PHP Bounce')){
		
			//lint return an array of multidimensions with line and messages
			$this->_errors[] = $this->phplint->get_parse_error();
			return false;
		
		}
		
		return true;
	
	}
	
	//whitelist
	public function whitelist(){
	
		$this->phpwhitelist->init_options($this->_test_code, $this->_custom_whitelist);
		
		if(!$this->phpwhitelist->run_whitelist()){

			//whitelist returns an array of errors
			foreach($this->phpwhitelist->get_errors() as $whitelist_error){
				$this->_errors[] = $whitelist_error;
			}
						
			return false;
		}
		
		return true;
	
	}
	
	//parse
	public function parse(){
	
		//init the parser
		$php_parser = new PHPParser_Parser(new PHPParser_Lexer);
		
		//resolve namespaces (using visitor pattern)
		$visitor_pattern = new PHPParser_NodeTraverser;
		$visitor_pattern->addVisitor(new PHPParser_NodeVisitor_NameResolver);
		
		//init the seraliser so we can query it later
		$php_xml_serialiser = new PHPParser_Serializer_XML;
		
		//produce a graph for analysis		
		try{
		
			$mission_graph = $php_parser->parse($this->_test_code);
			$mission_graph = $visitor_pattern->traverse($mission_graph);
			$mission_graph = $php_xml_serialiser->serialize($mission_graph);
			
			//we now have the mission_graph
			$this->_mission_graph = $mission_graph;
			return true;
			
		}catch(PHPParser_Error $e){
		
			//single one line error here
			//unlikely to happen if lint has passed and whitelist passed...
			$this->_errors[] = 'Parse Error: ' . $e->getMessage();
			
			return false;
		
		}
	
	}
	
	//mission_check
	public function mission_check(){
	
		if(empty($this->_mission_graph) OR empty($this->_parameters)){
		
			$this->_errors[] = 'The mission graph or parameters has not been set. Cannot do a mission check.';
			return false;
			
		}
		
		$this->missionchecker->init_options($this->_mission_graph, $this->_parameters);
		
		if(!$this->missionchecker->graph_check()){
		
			//Simple line error (there is no line number here)
			//Will return an array of messages
			foreach($this->missionchecker->get_error_messages() as $mission_error){
				$this->_errors[] = $mission_error;
			}
			return false;
			
		}
		
		return true;
		
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
		/*EXAMPLE ONLY
		$parameters = array(
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
		*/
	
	}
	
	//execute
	public function execute($options = false){
	
		$fake_server_env_prepend_file = APPPATH . 'helpers/phpsandbox_prepend_helper.php';
		
		$sandbox_options = array(
			'directory_protection'	=> array(
				$fake_server_env_prepend_file,
			),
		);
		
		if($options){
			$sandbox_options = array_merge($sandbox_options, $options);
		}
		
		$this->phpsandboxer->init_options($sandbox_options);
		$this->phpsandboxer->init_binary($this->_binary);
		$this->phpsandboxer->init_env($fake_server_env_prepend_file);
		$this->phpsandboxer->build_cli_options();
		
		//if true, then it worked!
		if($execution_output = $this->phpsandboxer->execute_code($this->_test_code, 'PHP Bounce')){
			$this->execution_timespan = $this->phpsandboxer->get_time_span();		
			return $execution_output;
		}else{
		
			$this->_errors[] = $this->phpsandboxer->get_parse_error();
			
			return false;
		}
			
	}
	
}