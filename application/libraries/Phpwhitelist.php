<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Token sanitiser for php code
*/
class Phpwhitelist{

	protected $_CI;
	//default whitelist of functions
	protected $_whitelist = 'echo,var_dump';
	protected $_errors = array();
	protected $_test_code = '';
	
	public function __construct(){
	
		$this->_CI =& get_instance();
	
	}
	
	//whitelist can be an array or string (comma delimited)
	public function init_options($test_code, $whitelist = ''){
	
		if(!empty($whitelist)){
		
			if(is_array($whitelist)){
				foreach($whitelist as $enabled_function){
					$this->_whitelist = $this->_whitelist . ',' . $enabled_function;
				}
			}else{
				$this->_whitelist = $this->_whitelist . ',' . $whitelist;
			}
			
		}
		
		$this->_test_code = $test_code;
		
		var_dump($this->_whitelist);
	
		return true;
		
	}
	
	public function run_whitelist(){
	
		if(empty($this->_test_code) OR empty($this->_whitelist)){
			$this->_errors = array(
				'Please set up the options for whitelisting, we need testcode and whitelist.'
			);
			return false;
		}
		
		//get an array of all allowed functions
		$allowed_functions = explode(',', $this->_whitelist);
		//setup the tokens
		$tokens = token_get_all($this->_test_code); 
		
		var_dump($tokens);
		
		//setup parserErrors
		$parseErrors = array();
		$vcall = '';
		
		foreach($tokens as $token) {
			if(is_array($token)) {
				$id = $token[0];
				switch ($id) {
					case(T_VARIABLE): { $vcall .= 'v'; break; }
					case(T_CONSTANT_ENCAPSED_STRING): { $vcall .= 'e'; break; }
					
					case(T_STRING): { $vcall .= 's'; }
					
					case(T_REQUIRE_ONCE): case(T_REQUIRE): case(T_NEW): case(T_RETURN):
					case(T_BREAK): case(T_CATCH): case(T_CLONE): case(T_EXIT):
					case(T_PRINT): case(T_GLOBAL): case(T_ECHO): case(T_INCLUDE_ONCE):
					case(T_INCLUDE): case(T_EVAL): case(T_FUNCTION): case(T_GOTO):
					case(T_USE): case(T_DIR): {
						if (array_search($token[1], $allowedCalls) === false)
							$parseErrors[] = 'illegal call: '.$token[1];
					}
				}
			}
			else $vcall .= $token;
		}
		
		// check for dynamic functions
		if(stristr($vcall, 'v(')!='') $parseErrors[] = array('illegal dynamic function call');
		
		return $parseErrors;
	
	}
	
	public function get_errors(){
	
		if(empty($this->_errors)){
			return false;
		}
		
		return $this->_errors;
	
	}
	

}