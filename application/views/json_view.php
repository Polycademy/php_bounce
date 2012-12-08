<?php defined('BASEPATH') OR exit('No direct script access allowed');

header('Content-type: application/json');

if(isset($response)) {

	//$this->firephp->log($response, 'At JSON');

	echo json_encode($response, JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT);
	
}else{

	echo json_encode(
		array(
			0	=> array(
				'line'		=> false,
				'message'	=> 'No response was passed to the json view file',
			),
		)
	);
	
}