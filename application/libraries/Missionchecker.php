<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Takes two arrays, and searches if one array is in the other
* If not pump out an error msg
* MULTIPLE ERRORS (multiple array)
*/
class Missionchecker{

	protected $_CI;
	protected $_graph = array();
	protected $_parameters = array();
	protected $_errors = array();
	
	public function __construct(){
	
		$this->_CI =& get_instance();
	
	}
	
	public function init_options($graph, $parameters){
	
		$this->_graph = $graph;
		
		$this->_parameters = $this->_build_xpaths($parameters);
			
	}
	
	protected function _build_xpaths($parameters){
		
		//passing by reference so we can edit the value
		foreach($parameters as $test_name => &$test_block){
		
			foreach($test_block['paths'] as $base_path => $children){
			
			
				//if the children is an array, then we need to a recursive loop to build up all the children
				if(is_array($children)){
				
					$child_paths = $this->_build_child_paths($children);
					$full_path = $base_path . '[' . $child_paths . ']';
					$test_block['paths'][$base_path] = $full_path;			
				
				//if children is not an array, then just the children as the path (the base_path is more likely to be just a key, and the children should be the full path
				}else{
				
					//the full path would be just the children + the the first value (because there would only be 1 value to test against)
					//what happens when you don't want to test the value? can there be a character for any value?
					$test_block['paths'][$base_path] = $children . '[text() =%s]';
				
				}
				
				
			}
			

			//we're wrapping any non * tests with apostrophes! Like 'testvalue'
			foreach($test_block['tests'] as &$check_values){
				foreach($check_values as &$value){
					$value = '\'' . $value . '\'';
				}
			}
			
			//here we are doing 2 things
			//the first is combine the error messages with the xpath queries into one single new array called "map"
			//we are also converting the type identifiers into their correct test values
			$map = array_combine(array_keys($test_block['tests']), $test_block['paths']);
			foreach($map as $key => &$value){
				$value = vsprintf($value, $test_block['tests'][$key]);
			}
			$test_block['map'] = $map;
			
		}
		
		return $parameters;
	
	}
	
	//we know $paths at this point has to be an array
	protected function _build_child_paths($paths = array()){
		
		//final_path has to be initialised, as it will be built up...
		$final_path = '';
		
		foreach($paths as $child => $subchild){
		
			//we're adding " ANDs " inbetween the subchilds, but not at the start
			reset($paths);
			//compares the current key (child) to the resetted key (key($paths))
			if($child !== key($paths)){
				$final_path .= ' and ';
			}
			
			if(is_array($subchild)){
			
				$final_path .= $child;
				//does the subchild contain only one element?
				//if so we need to be recursive
				if(count($subchild, COUNT_RECURSIVE) > 1){
				
					$final_path .= '[' . $this->_build_child_paths($subchild) . ']';
				
				}else{
				
					$final_path .= '/' . implode($subchild) . '[text() =%s]';
				
				}
			
			}else{
			
				$final_path .= $subchild . '[text() =%s]';
			
			}
		
		}
		
		return $final_path;
	
	}
	
	
	public function graph_check(){
	
		if(empty($this->_graph) OR empty($this->_parameters)){
			throw new Exception('Please setup the graph and parameters for the Missionchecker');
			return false;
		}
		
		$xml_doc = new DOMDocument;
		$xml_doc->preserveWhiteSpace = false;
		$xml_doc->loadXML($this->_graph);
		$xpath = new DOMXPath($xml_doc);
		
		//$error_index matches with the errors
		//query is the correct path (need to check if can be a relative path)
		//if it is an absolute path, will require recursion...
		//also not sure how to check value, but will require it from the paths
		foreach($this->_parameters as $test_name => $test_block){
		
			foreach($test_block['map'] as $error_msg => $path_query){
			
				$find_code = $xpath->query($path_query);
				
				//if no match, then put out the errors
				//$find_code is part of the DOMNode class (you need foreach to loop across them) //check doc if you want to query out some stuff
				if(empty($find_code->length)){
				
					$this->_errors[] = array(
						'line'		=> false,
						'message'	=> $error_msg,
					);
					
				}
			
			}
			
		}
		
		//if errors, then return false
		if(!empty($this->_errors)){
			return false;
		}
		
		return true;
	
	}
	
	public function get_error_messages(){
	
		if(empty($this->_errors)){
			return false;
		}
		
		return $this->_errors;
	
	}

}