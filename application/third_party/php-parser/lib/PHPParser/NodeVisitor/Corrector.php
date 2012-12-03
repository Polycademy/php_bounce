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
    public function enterNode(PHPParser_Node $node) {
	
		//If I return false, it removes the node
		//If I don't return or "null" it doesn't do anything
		//If I return a value, it replaces the node with the new value
		
		
		$node_name = get_class($node);
		$method_array = get_class_methods($node);
		
		$array_based_node = (array) $node;
		
		foreach($array_based_node as $key => $value){
			
			//detect if key is 
			
		}
		
		
		
		
		
		//ALL NODES ARE OBJECTS.. at the start
		echo '<pre><h4>Normal Methods</h4>';
		var_dump($node);
		echo '</pre>';
		echo '<pre><h4>Converted</h4>';
		var_dump($array_based_node);
		echo '</pre>';
		
		//THE WHOLE POINT OF THIS IS TO:
		//CONSTRUCT A NEW ARRAY
		//WHICH PERSERVES THE OBJECT NAMES
		//CONVERTS METHODS TO ARRAYS
		
		//GET the name of the object BEFORE you convert, you'll need it.
		$node_name = get_class($node);
		
		//The first node is always an object
		$array_nodes = (array) $node;
		foreach($array_nodes as $key => $array_node){
			$array_nodes[$key] = $this->_object_to_array($array_node);
		}
		
		#$array_nodes = array(
		#	$node_name	=> $array_nodes,
		#);
		
		#return new PHPParser_Node_Name($array_nodes);
		
		

		
		#echo '<pre>';
		#var_dump($node);
		#echo '</pre>';
		
		#return $node;
		
		#if ($node instanceof PHPParser_Node_Name) {
		#}
		
    }
	
	//recursive
	protected function _object_to_array($obj){
	
		//if the obj is an object
		//then get the class name
		//turn it into an array
		$obj_name = '';
		if(is_object($obj)){
			$obj_name = get_class($obj); //get the class name before it converts
			$obj = (array) $obj;
		}
		
		//obj must be an array by now
		//methods are now array keys
		//values could be arrays or objects
		//need to run recursively
		foreach($obj as $key => $value){
			
			//if the value is an object
			//run this function again
			if(is_object($value)){
				$output = self::_object_to_array($value);
			}
			
			
			
			
			
			//first check if the value is an array
			if(is_array($value)) {
			
				//
			
			
				$new = array();
				foreach($value as $key => $val) {
					$new[$key] = self::_object_to_array($val);
				}
				
			}else{
			
				$new[$key] = $value;
			
			}
			
			$new[] = array(
				$obj_name = $new,
			);
			
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