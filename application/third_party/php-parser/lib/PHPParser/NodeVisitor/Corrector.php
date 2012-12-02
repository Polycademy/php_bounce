<?php

class PHPParser_NodeVisitor_Corrector extends PHPParser_NodeVisitorAbstract{

	//multidimensional array of mission parameters
	/*
		$mission_parameters = array(
			'variables'	=> array(
				'VARIABLE_NAME'	=> 'VARIABLE_VALUE',
			),
			'functions'	=> array(
				'FUNCTION_NAME'	=> 'FUNCTION_OUTPUT'
			),
			'constants'	=> array(
				'CONSTANT_NAME'	=> 'CONSTANT_VALUE',
			),
		);
	*/
	protected $_parameters;
	
	//array KEY => VALUE
	//KEY could be line number (starting)
	//Value is the message to return
	protected $_errors = false;
	
	public function __construct($parameters){
		$this->_parameters = $parameters;
	}
	
    public function leaveNode(PHPParser_Node $node) {
		//instanceof means if $node is an instantiated object of PHPParser_Node_Name
		//PHPParser_Node_Name corresponds to PHPParser/Node/Name.php (check the lib)
        if ($node instanceof PHPParser_Node_Name) {
        }
    }

}