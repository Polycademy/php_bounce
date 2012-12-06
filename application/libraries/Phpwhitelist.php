<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Token sanitiser for php code
* DOES NOT SANITISE AGAINST CUSTOM NAME FUNCTIONS.. which is good
* DOES NOT SANITISE AGAINST CONTROL STRUCTURES AND LANGUAGE CONSTRUCTS
* THIS DOES NOT SANITISE CLASSES
*/
class Phpwhitelist{

	protected $_CI;
	//default whitelist of php CORE functions (comma delimited)
	protected $_whitelist = 'true,false,abs,addcslashes,aggregate_info';
	protected $_errors = array();
	protected $_test_code = '';
	
	public function __construct(){
	
		$this->_CI =& get_instance();
	
	}
	
	//whitelist can be an array or string (comma delimited)
	public function init_options($test_code, $whitelist = ''){
		
		if(!empty($whitelist)){
		
			if(is_array($whitelist)){
				$whitelist = implode(',', $whitelist);
			}
			
			$this->_whitelist = (empty($this->_whitelist))? $whitelist: $this->_whitelist . ',' . $whitelist;
			
		}
		
		$this->_test_code = $test_code;
		
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
		
		#var_dump($tokens);
		
		//cycle through each token to do a check
		foreach($tokens as $token) {
		
		
			//not all tokens are arrays
			//the tokens that are arrays are the ones we can check against
			if(is_array($token)) {
				
				$function_id = $token[0];
				$function_name = $token[1];
				$line_number = $token[2];
				#var_dump($function_id);
				#var_dump($function_name);
				#var_dump($line_number);

				
				switch($function_id){
				
					//unwhitelisted custom functions will be caught here aswell
					case(T_CALLABLE):
					case(T_EVAL):
					case(T_FUNCTION):
					case(T_INCLUDE):
					case(T_INCLUDE_ONCE):
					case(T_REQUIRE):
					case(T_REQUIRE_ONCE):
					case(T_STRING):
					case(T_CLASS):
					{
					
						#var_dump($function_name);
						#var_dump($allowed_functions);
						
						//if a particular function_name cannot be found within the allowed functions...
						//becareful about array_search, it returns a key which could be zero, we need to be strict
						if(array_search($function_name, $allowed_functions) === false){
							$this->_errors[] = 'Sorry this function call [' . $function_name . '] is not allowed on PHP Bounce, it is on line ' . $line_number;
						}
					
					}
					
					
				}
				
			}
		
		}
		
		return true;
	
	}
	
	public function get_errors(){
	
		if(empty($this->_errors)){
			return false;
		}
		
		return $this->_errors;
	
	}
	

}