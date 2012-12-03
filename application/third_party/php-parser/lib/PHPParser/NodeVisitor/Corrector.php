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
	//It msg to return should be like
	/*
		Key => 'Undeclared X'
		Key => 'Incorrect X' (incorrect output)
		It will be difficult to test control structures.. but that's for syntax checking
		The home.php will have their own custom error messages (friendly) to match to those
		Perhaps if there cannot be line numbers, you can just do 'Undeclared' => X.. etc It will make it easy to do array search and replace
	*/
	protected $_errors = false;
	
	public function __construct($parameters){
		$this->_parameters = $parameters;
	}
	
    public function leaveNode(PHPParser_Node $node) {
		//instanceof means if $node is an instantiated object of PHPParser_Node_Name
		//PHPParser_Node_Name corresponds to PHPParser/Node/Name.php (check the lib)
		
		//IF a particular array parameter exists
		//THEN FOREACH
		//THEN check the node for existence + value
		//THEN FIGURE OUT THE MESSAGE
        if ($node instanceof PHPParser_Node_Name) {
        }
    }

}