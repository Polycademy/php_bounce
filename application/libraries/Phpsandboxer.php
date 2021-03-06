<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Sandbox environment for evaluating php
	//to use this library
	//call init_binary
	//call init_env
	//call init_options (optional)
	//call enable_function (optional)
	//call build_cli_options
	//call execute_code
	//call execution_time
	//call errors...
* SINGULAR ERROR (single array)
*/
class Phpsandboxer{

	//options for phpsandbox
	//Always check here if you are using a function
	protected $_options = array(
		#'chroot'				=> '/', //NO CHROOT because I don't know what it really does
		'directory_protection'	=> array(),
		'display_errors'		=> 'on', //don't use stderr, as it will repeat the error
		'auto_prepend_file'		=> false,
		'max_execution_time'	=> 1,
		'memory_limit'			=> '2M',
		'upload_max_filesize'	=> '0M',
		'file_uploads'			=> 'off',
		'allow_url_fopen'		=> 'off',
		'disable_functions'		=> 'apache_child_terminate,apache_setenv,assert_options,chdir,chgrp,chmod,chown,closedir,copy,dbase_open,define_syslog_variables,dl,escapeshellarg,escapeshellcmd,exec,curl_exec,curl_multi_exec,curl_multi_init,curl_init,parse_ini_file,show_source,pcntl_fork,pcntl_exec,phpinfo,ini_set,include,require,include_once,require_once,file,file_exists,file_get_contents,finfo_file,finfo_open,fopen,fp,fpassthru,fput,fputcsv,fputs,fread,ftp_connect,ftp_exec,ftp_get,ftp_login,ftp_nb_fput,ftp_put,ftp_raw,ftp_rawlist,ftruncate,fwrite,getcwd,getopt,header,highlight_file,ini_alter,ini_get,ini_get_all,ini_restore,link,mail,mkdir,move_uploaded_file,mysql_connect,mysql_pconnect,mysqli_connect,opendir,openlog,passthru,pathinfo,php_uname,phpversion,popen,posix_getpwuid,posix_kill,posix_mkfifo,posix_setpgid,posix_setsid,posix_setuid,posix_uname,proc_close,proc_get_status,proc_nice,proc_open,proc_terminate,putenv,readdir,readfile,readlink,rename,restore_include_path,rewinddir,rmdir,scandir,session_start,set_include_path,set_magic_quotes_runtime,set_time_limit,shell_exec,symlink,assert,file_put_contents,sys_get_temp_dir,syslog,system,tempnam,tmpfile,unlink,xmlrpc_entity_decode,zend_version,imap_mail,imap_open,dbmopen,filepro,filepro_rowcount,filepro_retrieve,apache_request_headers,posix_getlogin,posix_ttyname,get_current_user,get_cfg_var,getlastmod,getmypid,parse_str,pfsockopen,fsockopen,bzopen,gzopen,ftp_nb_get,imagepng,imagewbmp,image2wbmp,imagejpeg,imagexbm,imagegif,imagegd,imagegd2,iptcembed,gzfile,readgzfile,imagecreatefromgif,imagecreatefromjpeg,imagecreatefrompng,imagecreatefromwbmp,imagecreatefromxbm,imagecreatefromxpm,exif_read_data,read_exif_data,pcntl_signal,pcntl_alarm,register_tick_function,register_shutdown_function,chroot,dir,php_sapi_name,dio_open,dio_read,dio_write',
		'disable_classes'		=> 'SplFileObject',
	);
	//cli options built as a string to pass into cli
	protected $_cli_options;
	//path to binary
	protected $_php_binary = 'php';
	//how long script took
	protected $_run_start_time = 0;
	protected $_run_end_time = 0;
	//operating system
	protected $_operating_system;
	//code to be run
	protected $_code;
	//error array
	protected $_error = false;
	//CI super object
	protected $_CI;

	public function __construct(){
	
		$this->_CI =& get_instance();
		
	}
	
	//initiate custom options to be passed to CLI
	//merges it so, will replace only some of the variables
	public function init_options($options = array()){
		$this->_options = array_merge($this->_options, $options);
		return true;
	}
	
	public function init_binary($php_binary = false){
	
		if(stripos(PHP_OS, 'win') !== false){
			$this->_operating_system = 'WIN';
		}else{
			$this->_operating_system = 'UNIX';
		}
	
		if (!empty($php_binary) && (!file_exists($php_binary) || !is_executable($php_binary))) {
			throw new Exception('Specified PHP binary in PHPSandbox is not valid. Check if it is the right path.');
		}
		
		$this->_php_binary = $php_binary ? $php_binary : $this->_find_binary();
	
	}
	
	private function _find_binary() {
		
		if ($this->_operating_system == 'WIN') {
			return 'c:\wamp\bin\php\php5.3.0\php.exe';
		}else{
		
			//this will work on unix computers
			$php_binary = trim(shell_exec('which php'));
			
			if(!empty($php_binary)){
				return $php_binary;
			}else{
				throw new Exception('PHPSandbox cannot find PHP Binary automatically.');
			}
			
		}
		
	}
	
	//for the cli environment
	//should be phpsandbox_prepend_helper
	public function init_env($path_to_file){
	
		//if no helper, then just move on with no environmental variables
		if(!empty($path_to_file)){
			if(file_exists($path_to_file)){
				$this->_options['auto_prepend_file'] = $path_to_file;
				return true;
			}else{
				throw new Exception('PHPSandbox cannot find the auto_prepend_file for CLI. Check your filepath sorry.');
			}
		}
		
		return false;
	
	}
	
	/**
	* Enable a function from the disallowed function list
	* @param string $function
	* @param bool $force_rebuild (this is so that if build_cli_options was called before this, this rebuilds it)
	*/
	public function enable_function($function, $force_rebuild = true){
	
		$functions = explode(',', $this->_options['disable_functions']);
		
		$functions = array_flip($functions);
		
		if(!empty($function) && isset($functions[$function])){
			unset($functions[$function]);
		}
		
		$functions = array_flip($functions);
		
		$this->_options['disable_functions'] = implode(',', $functions);
		
		if($force_rebuild){
			$this->build_cli_options();
		}
		
	}
	
	//this should include directory protection such as open_basedir and chroot
	//should test what basedir permissions are available
	//also disable classes
	public function build_cli_options(){
		
		$this->_cli_options = '';
		//standard parameters
		$this->_cli_options .= '-d display_errors=' . $this->_options['display_errors'] . ' ';
		$this->_cli_options .= '-d memory_limit=' . $this->_options['memory_limit'] . ' ';
		$this->_cli_options .= '-d max_execution_time=' . $this->_options['max_execution_time'] . ' ';
		$this->_cli_options .= '-d file_uploads=' . $this->_options['file_uploads'] . ' ';
		$this->_cli_options .= '-d upload_max_filesize=' . $this->_options['upload_max_filesize'] . ' ';
		$this->_cli_options .= '-d allow_url_fopen=' . $this->_options['allow_url_fopen'] . ' ';
		//disable functions
		if(!empty($this->_options['disable_functions'])){
			$this->_cli_options .= '-d disable_functions=' . $this->_options['disable_functions'] . ' ';
		}
		//disable classes
		if(!empty($this->_options['disable_classes'])){
			$this->_cli_options .= '-d disable_classes=' . $this->_options['disable_classes'] . ' ';
		}
		//auto prepend file
		if(!empty($this->_options['auto_prepend_file'])){
			$this->_cli_options .= '-d auto_prepend_file="' . $this->_options['auto_prepend_file'] . '" ';
		}
		//open_basedir
		#need to test where this should be?
		#directory protection has to be an array
		#the array is basically, the list of file paths
		#build the directory protection via open_basedir
		#include executed code location (possibly the library, possibly controller)
		#include prepended file
		#cascading order
		if(!empty($this->_options['directory_protection']) AND is_array($this->_options['directory_protection'])){
		
			//determine the dir separator for open_basedir
			if($this->_operating_system == 'WIN'){
				$open_basedir_separator = ';';
			}else{
				$open_basedir_separator = ':';
			}
			
			//all directory paths should end in a slash unless it is a file name...
			foreach($this->_options['directory_protection'] as $key => $dir){
				//if dir does not end in '/' AND the file extension is not php then add a DIRECTORY_SEPARATOR
				//without this, the dir would become a prefix
				if(substr($dir, -1) != DIRECTORY_SEPARATOR AND pathinfo($dir, PATHINFO_EXTENSION) != 'php'){
					$this->_options['directory_protection'][$key] = $dir . DIRECTORY_SEPARATOR;
				}
			}
			
			$open_basedir_str = implode($open_basedir_separator, $this->_options['directory_protection']);
			
			/*
			echo '<pre><h2>DIRECTORY PROTECTION PATHS</h2>';
			var_dump($this->_options['directory_protection']);
			echo '</pre>';
			echo '<pre><h2>DIRECTORY PROTECTION STR</h2>';
			var_dump($open_basedir_str);
			echo '</pre>';
			*/
			
			
			$this->_cli_options .= '-d open_basedir="' . $open_basedir_str . '" ';
			
		}
		
		//trim off total whitespace on edges
		$this->_cli_options = trim($this->_cli_options);
	
	}
	
	public function execute_code($code, $fname = false){
	
		if(empty($code)){
			return false;
		}
		if(empty($this->_cli_options)){
			throw new Exception('CLI options have not been built yet, you cannot run the shell, it would be dangerous!');
		}
		$this->_code = $code;
		
		//0 is stdin, 1 is stdout, 2 is stderr
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("pipe", "w")
		);
		
		//start the race
		$this->_run_start_time = $this->_time_stamp();
		
		//GOGOGOGO!
		//ITS CURRENTLY USING <?php this does not distort the lines, because it is adding on in the same line...
		$process = proc_open($this->_php_binary . ' ' . $this->_cli_options, $descriptorspec, $pipes);
		
		if(!is_resource($process)){
			throw new Exception('PHPSandbox could not open up a process protocol to PHP binary.');
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
		
		//finish the race
		$this->_run_end_time = $this->_time_stamp();
		
		if(!empty($stderr)){
			$this->_error = $this->_parse_error($stderr, $fname);
			return false;
		}
		
		//yes no errors! syntax is all good
		return $stdout;
		
	}
	
	/**
	* parse_error
	*
	* @param string $error_line
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
	
	public function get_parse_error() {
		return $this->_error;
	}
	
	//gets it in seconds
	public function get_time_span(){
		return round($this->_run_end_time - $this->_run_start_time, 5);
	}
	
	protected function _time_stamp(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

}