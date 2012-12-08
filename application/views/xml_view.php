<?php defined('BASEPATH') OR exit('No direct script access allowed');

// I need to actually see the xml code, not the browser processed xml
header('Content-Type: text/plain; charset=utf-8');

// Encode data
if(isset($response)){
	echo $response;
}else{
	echo 'No xml data';
}