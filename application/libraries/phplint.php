<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Helper class to run a lint check on php code and get possible errors in useable form
* Use lint_file or lint_string and then get_parse_error
*/
class Phplint{

	/**
	* CI Super object
	*
	* @var string
	*/
	private $_CI;

	/**
	* Path to php binary to be used
	*
	* @var string
	*/
	protected $_php_binary = 'php';

	/**
	* Reference to parseError object in case of lint error
	*
	* @var parseError
	*/
	protected $_error;
	
	/**
	* OS, Windows or Unix
	*
	* @var parseError
	*/
	protected $_operating_system;

	/**
	* PHPLint Constructor method
	*
	* @param string $php Path to php binary to use
	*
	*/
	public function __construct(){
	
		$this->_CI =& get_instance();		
		
	}
	
	public function init_binary($php_binary = false){
	
		if(stripos(PHP_OS, 'win') !== false){
		
			$this->_operating_system = 'WIN';
			
		}else{
		
			$this->_operating_system = 'UNIX';
		
		}
	
		if (!empty($php_binary) && (!file_exists($php_binary) || !is_executable($php_binary))) {
		
			show_error('Specified PHP binary is not valid. Check if it is the right path.', 500);
			
		}
		
		$this->_php_binary = $php_binary ? $php_binary : $this->_find_binary();
	
	}
	
	//will try to find binary for unix, or some default position for windows
	private function _find_binary() {
	
		#var_dump($this->_operating_system);
	
		if ($this->_operating_system == 'WIN') {
		
			return 'c:\wamp\bin\php\php5.3.0\php.exe';
			
		}else{
		
			//this will work on unix computers
			$php_binary = trim(shell_exec('which php'));
			
			if(!empty($php_binary)){
			
				return $php_binary;
			
			}else{
				
				show_error('PHP cannot find PHP Binary... sorry.', 500);
				
			}
			
		}
		
	}

	public function get_parse_error() {
	
		return $this->_error;
		
	}

	/**
	* Run lint check on a file
	*
	* @param string $fname File to run lint check on
	*
	* @return boolean  True for no lint errors, false otherwise
	*/
	public function lint_file($fname) {
	
		if(!file_exists($fname)) {
		
			show_error("Trying to lint check on file $fname it was not found.");
			
		}
		
		$code = file_get_contents($fname);
		
		$code = $this->lint_string($code, $fname);
		
		return $code;
		
	}

	/**
	* Run lint check on a given string of php code
	*
	* @param string $code  String containg php code to run lint on
	* @param string $fname Optional Filename to use in error object
	*
	* @return boolean  True for no lint errors, false otherwise
	*/
	public function lint_string($code, $fname = false) {

		if(empty($code)){
			return false;
		}
		
		//0 is stdin, 1 is stdout, 2 is stderr
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("pipe", "w")
		);
		
		//hope binary works!
		$process = proc_open($this->_php_binary . ' -l', $descriptorspec, $pipes);
		
		if(!is_resource($process)) {
			show_error('Could not open PHP Binary for linting');
		}
		
		//pump in the code!
		fwrite($pipes[0], $code);
		fclose($pipes[0]);
		
		//scoop out the output
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		//the stdout will actually be different for Windows or Unix, best not to rely on it
		
		//oh no errors?
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);
		
		$return_value = proc_close($process);
		
		/*
		echo '<pre><h2>STDOUT FROM LINT</h2>';
		var_dump($stdout);
		echo '</pre>';
		echo '<pre><h2>STDERR FROM LINT</h2>';
		var_dump($stderr);
		echo '</pre>';
		echo '<pre><h2>RETURN_VALUE FROM LINT</h2>';
		var_dump($return_value);
		echo '</pre>';
		*/
		
		//if we get an error
		//On windows computers, return_value will be -1 on error, on UNIX, 255 on error
		if($return_value == -1 OR $return_value == 255){
			
			$this->_error = $this->_parse_error($stderr, $fname);
			return false;
			
		}
		
		//yes no errors! syntax is all good
		$this->_error = null;
		return true;
		
	}
	
	/**
	* parse_error
	*
	* @param string $error_line  Unparsed PHP -l output line
	* @param string $fname      Overwrite filename from output with this filename
	*/
	protected function _parse_error($error_line, $fname = false){
		
		preg_match('/^(.*):(.*) in (.*) on line (.*[0-9])/u', $error_line, $matches);
		
		$error_properties = array(
			'raw'		=> trim($error_line),
			'type'		=> $matches[1],
			'message'	=> trim($matches[2]),
			'file'		=> (!empty($fname)) ? $fname : $matches[3],
			'line'		=> $matches[4],
		);
		
		return $error_properties;
	
	}

}