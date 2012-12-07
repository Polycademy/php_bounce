<?php defined('BASEPATH') OR exit('No direct script access allowed');

// RFC4627-compliant header
header('Content-type: application/json');

// Encode data
if(isset($response)) {
	echo json_encode($response, JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT);
}else{
	echo json_encode(array('error' => 'No response was passed to the json view file.'));
}