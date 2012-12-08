<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Helper class to run a lint check on php code and get possible errors in useable form
* Use lint_string/file and then get_parse_error
* SINGULAR ERROR (single array)
*/
class Phplint{

	private $_CI;
	protected $_php_binary = 'php';
	protected $_error = false;
	protected $_operating_system;

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
			throw new Exception('PHP Binary specified in Phplint is not valid, check it if is the right path.');
		}
		
		$this->_php_binary = $php_binary ? $php_binary : $this->_find_binary();
	
	}
	
	//will try to find binary for unix, or some default position for windows
	private function _find_binary() {
		
		if ($this->_operating_system == 'WIN') {
		
			return 'c:\wamp\bin\php\php5.3.0\php.exe';
			
		}else{
		
			//this will work on unix computers
			$php_binary = trim(shell_exec('which php'));
			
			if(!empty($php_binary)){
				return $php_binary;
			}else{
				throw new Exception('Phplint could not find a binary automatically.');
			}
			
		}
		
	}
	
	public function get_parse_error() {
		return $this->_error;
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
			throw new Exception('Phplint could not open up a process protocol to PHP binary.');
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
		
		//if we get an error
		//On windows computers, return_value will be -1 on error, on UNIX, 255 on error
		if($return_value == -1 OR $return_value == 255){
			
			$this->_error = $this->_parse_error($stderr, $fname);
			return false;
			
		}
		
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
		
		//only one error (explicitly set this)
		$error = array(
			'raw'		=> trim($error_line),
			'type'		=> $matches[1],
			'file'		=> (!empty($fname)) ? $fname : $matches[3],
			//THESE TWO are the ones we're going to use
			'line'		=> $matches[4],
			'message'	=> $matches[1] . ': ' . trim($matches[2]),
		);
		
		return $error;
	
	}

}