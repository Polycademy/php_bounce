<?php

//Recursive Object to Array
//Beware of protected methods, they can only be accessed within their own class, and when outputted, will have *
if(!function_exists('object_to_array')){

	function object_to_array($obj){

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

}