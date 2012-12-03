<?php

class PHPParser_CustomTraverser{

    /**
     * Traverses an array of nodes using the registered visitors.
     */
    public function traverse(array $nodes) {
	
		echo '<pre><h2>Normal</h2>';
		var_dump($nodes);
		echo '</pre>';
		
		$nodes = $this->_object_to_array($nodes);
	
        return $nodes;
		
		
    }
	
	//recursive
	protected function _object_to_array($obj){
	
		//we want to preserve the object name to the array
		//so we get the object name in case it is an object before we convert to an array (which we lose the object name)
		$obj_name = false;
		$is_object = false;
		if(is_object($obj)){
		
			$is_object = true; //oh its an object, this will be useful to check if we should contain it with the name
			
			$obj_name = get_class($obj);
			$obj_type = $obj->getType();
			$obj_subnodes = $obj->getSubNodeNames();
			foreach($obj_subnodes as $obj_subnode_name){
				$obj_subnodes_value[$obj_subnode_name] = $obj->__get($obj_subnode_name);
			}
			
			echo '<pre>';
			echo '<h5>NAME</h5>';
			var_dump($obj_name);
			echo '<h5>TYPE</h5>';
			var_dump($obj_type);
			echo '<h5>Subnodes</h5>';
			var_dump($obj_subnodes);
			echo '<h5>Subnode Name to Values</h5>';
			var_dump($obj_subnodes_value);
			echo '</pre>';
			
			$obj = (array) $obj;
		
		}
		
		//if obj is now an array, we do a recursion
		//if obj is not, just return the value
		if(is_array($obj)) {
			
			$new = array();
			
			//initiate the recursion
			foreach($obj as $key => $val) {
				//we don't want those * infront of our keys due to protected methods
				$new[$key] = self::_object_to_array($val);
			}
			
			//if is_object is true, then the array was previously an object
			//the new array that is produced at each stage should be prefixed with the object name
			//so we construct an array to contain the new array with the key being the object name
			if($is_object){
				$new = array(
					$obj_name => $new,
				);
			}
			
		}else{
		
			$new = $obj;
		
		}
		
		return $new;
	
	}
	
}