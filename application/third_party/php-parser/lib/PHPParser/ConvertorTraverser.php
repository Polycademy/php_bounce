<?php

class PHPParser_ConvertorTraverser{

    /**
     * Traverses an array of nodes using the registered visitors.
     */
    public function traverse(array $nodes) {
	
		#echo '<pre><h2>Normal</h2>';
		#var_dump($nodes);
		#echo '</pre>';
		
		$nodes = $this->_object_to_array($nodes);
	
        return $nodes;
		
    }
	
	//recursive
	protected function _object_to_array($obj){
	
		//we want to preserve the object name to the array
		//so we get the object name in case it is an object before we convert to an array (which we lose the object name)
		$is_object = false;
		if(is_object($obj)){
		
			$is_object = true;
			$obj_type = $obj->getType();
			$obj_type = strtolower($obj_type);
			$obj = (array) $obj;
		
		}
		
		//if obj is now an array, we do a recursion (AND we also know that it was a former object, because that's why PHP Parser does)
		//if obj is not, just return the value
		if(is_array($obj)) {
			
			$new = array();
			
			//creating the new array by walking through the obj
			//recursion method of creating
			//$key remains the same (string) with of course scoping attached (*)
			//value of array is another array, which is sent to go through the process again
			foreach($obj as $key => $val) {
			
				$key = strtolower($key);
				if($is_object){
					$new[$obj_type][$key] = self::_object_to_array($val);
				}else{
					$new[$key] = self::_object_to_array($val);
				}
				
			}
			
		}else{
		
			$new = $obj;
		
		}
		
		return $new;
	
	}
	
}