<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Token sanitiser for php code
*/
class Phpwhitelist{

	protected $_CI;
	//default whitelist of functions
	protected $_whitelist = array();
	
	public function __construct(){
	
		$this->_CI =& get_instance();
	
	}

}