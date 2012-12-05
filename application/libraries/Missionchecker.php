<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Takes two arrays, and searches if one array is in the other
* If not pump out an error msg
* Take care to compare error messages?
*/
class Missionchecker{

	protected $_CI;
	protected $_graph = array();
	protected $_parameters = array();
	
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
					$test_block['paths'][$base_path] = $children . '=%s';
				
				}
				
				
			}
			

			//we're wrapping any non * tests with apostrophes! Like 'testvalue'
			foreach($test_block['tests'] as &$check_values){
				foreach($check_values as &$value){
					if($value != '*') $value = '\'' . $value . '\'';
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
				
					$final_path .= '/' . implode($subchild) . '=%s';
				
				}
			
			}else{
			
				$final_path .= $subchild . '=%s';
			
			}
		
		}
		
		return $final_path;
	
	}
	
	
	public function graph_check(){
	
		if(empty($this->_graph) OR empty($this->_parameters)){
			$this->_errors = array(
				'Please set up the options for checking, we need a graph and parameters'
			);
		}
		
		$xml_doc = new DOMDocument;
		$xml_doc->preserveWhiteSpace = false;
		$xml_doc->loadXML($this->_graph);
		$xpath = new DOMXPath($xml_doc);
		
		//$error_index matches with the errors
		//query is the correct path (need to check if can be a relative path)
		//if it is an absolute path, will require recursion...
		//also not sure how to check value, but will require it from the paths
		foreach($this->_parameters as $error_index => $query){
			#var_dump($error_index); //outputs echo_true_check
			#var_dump($query); //outputs array
			#var_dump($query['xpath']); //outputs xpath
			#var_dump($query['value']); //outputs value to check
			
			$find_code = $xpath->query($query['xpath']);
			
			foreach($find_code as $code) {
				var_dump($code->nodeValue);
			}
			
			
		}
	
	}
	
	public function run_check(){
	
		if(empty($this->_graph) OR empty($this->_parameters) OR empty($this->_error_messages)){
			$this->_errors = array(
				'Please set up the options for checking, we need a graph, parameters and error messages'
			);
		}
		
		//cant use this because it lists by dimension first, also your key parameters may not be stacked in dimensional order anyway
		#$keys_from_graph = $this->_array_key_recursive($this->_graph);
		
		//have to use the old fashioned method, step through the graph array (while)
		//inside the below foreach, you are going to do a while list, and go through the graph array
		//using the keys from parameter do another foreach
		//now you're matching the FIRST key do the ANY key in the FIRST dimension of the GRAPH array
		//If matched, step through to the SECOND DIMENSION (then search SECOND KEY)
		//Keep going until you have all the keys
	
		//first take the $this->_parameters and pop out the first element called the error_index
		foreach($this->_parameters as $error_index => $parameter){
			//$error_index should be used in case there was an error
			//$parameter is to be matched to $this->_graph
			
			$keys_from_parameter = $this->_array_key_recursive($parameter);
			//popped the last element off $keys_from_parameter and put it in value_from_parameter
			$value_from_parameter = array_pop($keys_from_parameter);
			
			
			#echo '<pre>';
			#var_dump($error_index);
			#var_dump($parameter);
			var_dump($keys_from_parameter);
			var_dump($value_from_parameter);
			#echo '</pre>';
			
		}
		
		
	}
	
	
	protected function _end_of_array_recursive($multiarr){

		$listofkeys = array_keys($multiarr);
		$lastkey = end($listofkeys);
		
		$this->_CI->firephp->log($lastkey);
		
		if(is_array($multiarr[$lastkey])){
		
			$this->_end_of_array_recursive($multiarr[$lastkey]);
		
		}else{
			
			return $lastkey;
		
		}
		
		
	}

	
	protected function _array_key_recursive($array){
		
		$list_of_keys = array_keys($array);
		
		foreach($array as $value){
			if(is_array($value)){
				$list_of_keys = array_merge($list_of_keys, $this->_array_key_recursive($value));
			}
		}
		
		return $list_of_keys;
		
	}
	
	public function get_error_messages(){
	
		if(empty($this->_errors)){
			return false;
		}
	
		return $this->_build_error_messages($this->_errors, $this->_error_messages);
	
	}
	
	protected function _build_error_messages($errors, $error_messages){
		
		//do the building
		$output_errors = '';
		return $output_errors;
	
	}

}