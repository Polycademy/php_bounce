<?php

class PHPParser_NodeVisitor_Corrector extends PHPParser_NodeVisitorAbstract{

	//multidimensional array of mission parameters
	//this will represent a loose representation of the AST graph
	protected $_parameters;
	#protected $_errors = false;
	
	public function __construct($parameters){
		$this->_parameters = $parameters;
	}
	
	//we are going to convert any objects to arrays here
	//instanceof means if $node is an instantiated object of PHPParser_Node_Name
	//PHPParser_Node_Name corresponds to PHPParser/Node/Name.php (check the lib)
    public function leaveNode(PHPParser_Node $node) {
	
		//If I return false, it removes the node
		//If I don't return or "null" it doesn't do anything
		//If I return a value, it replaces the node with the new value
		
		//ALL NODES ARE OBJECTS.. at the start
		
		//GET the name of the object BEFORE you convert, you'll need it.
		$name_of_object = get_class($node);
		if(is_object($node)){
			echo 'IS AN OBJECT<br />';
		}
		echo '<pre>';
		var_dump($node);
		echo '</pre>';
		

		/*
		array_walk_recursive(
			$a,
			function($item, $key){
				if(is_object( $item ) ) { 
					$item = $item->output(); 
				}
			}
		);
		*/
		
		$node = $this->_object_to_array($node);
		
		echo '<pre>';
		var_dump($node);
		echo '</pre>';
		
		return $node;
		
		#if ($node instanceof PHPParser_Node_Name) {
		#}
		
    }
	
	//recursive
	protected function _object_to_array($obj){
	
		if(is_object($obj)) $obj = (array) $obj;
		
		if(is_array($obj)) {
		
			$new = array();
			foreach($obj as $key => $val) {
				$new[$key] = self::_object_to_array($val);
			}
			
		}else{
		
			$new = $obj;
		
		}
		
		return $new;
	
	}
	
	//access the list of errors
	/*
	public function get_errors(){
		return $this->_errors;
	
	}
	*/
	
}