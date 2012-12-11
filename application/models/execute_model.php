<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Execute_model extends CI_Model {

	private $_errors = array();
	private $_test_code = '';
	private $_binary = '';
	private $_custom_whitelist = '';
	private $_parameters = array();
	private $_mission_graph = false;
	private $_execution_timespan = '';
	private $_execution_output = '';
	private $_output = false;

	public function __construct(){
	
		parent::__construct();
		
	}
	
	/**
	 * Initiate all the necessary variables in order to run an execution routine
	 *
	 * Adds <?php to the front of test_Code if it doesn't have it. Short open tags are not allowed.
	 * 
	 * @param string $test_code
	 * @param string $binary
	 * @param array $parameters
	 * @param array $custom_whitelist
	 * @param string $output - for output checking
	 * @return boolean
	 */
	public function init_options($test_code, $binary, $parameters = array(), $custom_whitelist = '', $output = false){
		
		//ADD <?php infront CANNOT USE short open tags for binary execution
		if(!preg_match('/\A^(<\?php)(\s)+?/m', $test_code)){
			$test_code = '<?php ' . $test_code;
		}
		
		$this->_test_code = $test_code;
		$this->_binary = $binary;
		$this->_custom_whitelist = $custom_whitelist;
		$this->_parameters = $parameters;
		$this->_output = $output;
		
		return true;
	
	}
	
	/**
	 * Runs execution routine checks
	 *
	 * Take an array of options containing the names of all the checks to be done, then runs through each check.
	 * If any of the checks fail, then returns false. Otherwise returns the execution output.
	 * Checks need to be done in order of execution (array('lint', 'whitelist', 'parse', 'mission_check', 'execute');)
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
			
			$output = $this->$check_method();
			
			//if output is STRICTLY false
			//allows empty strings to pass through
			if($output === false){
				return false;
			}
		}
		
		return $output;
		
	}
	
	/**
	 * Get Mission Graph in case you want to see it directly or for helping create the xpath rules
	 * 
	 * @return xml/boolean
	 */
	public function get_mission_graph(){
	
		if(empty($this->_mission_graph)){
			return false;
		}
		return $this->_mission_graph;
		
	}
	
	/**
	 * Get errors from all the routines in the model, this model aggregates all the errors
	 * 
	 * @return xml/boolean
	 */
	public function get_errors(){
		
		//$this->firephp->log($this->_errors, 'At get_errors from Execute_model');
		
		if(!empty($this->_errors)){
			return $this->_errors;
		}
		return false;
		
	}
	
	/**
	 * Get timespan of execution for benchmarking of user input
	 * 
	 * @return string
	 */
	public function get_timespan(){
		
		if(!empty($this->_execution_timespan)){
			return $this->_execution_timespan;
		}
		return false;
		
	}
	
	/**
	 * Only used when there was an output check, this gets the execution output, so it can be combined with the error messages
	 * that is produced by the output check, otherwise we wouldn't be able to see what the execution output was when the output
	 * check returned false
	 * 
	 * @return string
	 */
	public function get_execution_output(){
	
		if($this->_output){
			return $this->_execution_output;
		}else{
			return false;
		}
		
	}
	
	/**
	 * Lints the code, can be executed independently of run
	 * 
	 * @return boolean
	 */
	public function lint(){
	
		$this->phplint->init_binary($this->_binary);
		
		if(!$this->phplint->lint_string($this->_test_code, 'PHP Bounce')){
			//there will be only 1 parse error
			$this->_errors[] = $this->phplint->get_parse_error();
			return false;
		}
		
		return true;
	
	}
	
	/**
	 * Whitelist the code, can be executed independently of run
	 * 
	 * @return boolean
	 */
	public function whitelist(){
	
		$this->phpwhitelist->init_options($this->_test_code, $this->_custom_whitelist);
		
		if(!$this->phpwhitelist->run_whitelist()){
			//there can be multiple whitelist errors so we pass directly
			$this->_errors = $this->phpwhitelist->get_errors();
			return false;
		}
		
		return true;
	
	}
	
	/**
	 * Parses the code into an abstract syntax tree so we can do mission checks, can be ran independently
	 * 
	 * @return boolean
	 */
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
		
			//unlikely to happen if lint has passed and whitelist passed...
			//there will be only 1 parse error, so just declare the array here
			$this->_errors = array(
				0	=> array(
					'line'		=> false,
					'message'	=> 'XML Parsing Error: ' . $e->getMessage(),
				),
			);
			
			return false;
		
		}
	
	}
	
	/**
	 * Does the mission check on the code, requires parameters and mission graph from parse, can be ran independently
	 * 
	 * @return boolean
	 */
	public function mission_check(){
		
		$this->missionchecker->init_options($this->_mission_graph, $this->_parameters);
		
		if(!$this->missionchecker->graph_check()){
			//there can be multiple missionchecker errors so pass directly
			$this->_errors = $this->missionchecker->get_error_messages();
			
			#$this->firephp->log($this->_errors);
			
			return false;
		}
		
		return true;
	
	}
	
	/**
	 * Finally executes the code, returns either the output with execution timespan or false on error, can be ran independently
	 *
	 * Has the ability to setup custom options, unlikely to be used as everything has been setup already...
	 * 
	 * @return boolean
	 */
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
		
		$execution_output = $this->phpsandboxer->execute_code($this->_test_code, 'PHP Bounce');
		
		//basically ANYTHING but false because empty strings are good to go aswell!
		if($execution_output !== false){
			#var_dump($execution_output);
			$this->_execution_timespan = $this->phpsandboxer->get_time_span();
			$this->_execution_output = $execution_output;
			return $execution_output;
		}else{
			//there is only 1 error from parse error, so we can declare it here
			$this->_errors[] = $this->phpsandboxer->get_parse_error();
			return false;
		}
		
	}
	
	/**
	 * Checks the final output against the output check. If the check passes, it will pass & return the full output.
	 * 
	 * @return boolean/passes output on if it works
	 */
	public function output_check(){
	
		if($this->_output){
		
			if($this->_output == $this->_execution_output){
				return $this->_execution_output;
			}
			
			$this->_errors = array(
				0	=> array(
					'line'		=> false,
					'message'	=> 'Sorry incorrect output, try again!',
				),
			);
			
			return false;
			
		}
	
	}
	
}