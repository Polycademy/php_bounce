<?php

//here we set all the environmental variables for the phpsandbox to use

//TIMEZONE
date_default_timezone_set('Australia/ACT');

//Hide the enviroment veriables to help provide obscurification
foreach($_ENV as $key => $value){
	putenv("$key=null");
	$_ENV[$key]=null;
	unset($_ENV[$key]);
}

//Hide the server veriables to help provide obscurification
foreach($_SERVER as $key => $value){
	$_SERVER[$key]=null;
	unset($_SERVER[$key]);
}

//SERVER ENVIRONMENT VARS, this will replace the defaults
$server_environment = array(
	'HTTP_HOST'				=> 'PHPBounce',
	'HTTP_USER_AGENT'		=> 'Mozilla/5.0 (Windows NT 6.0; rv:15.0) Gecko/20100101 Firefox/15.0.1',
	'HTTP_ACCEPT'			=> 'text/html,application/xhtml+xml,application/xml;q=0.9,*\/*;q=0.8',
	'HTTP_ACCEPT_LANGUAGE'	=> 'en-us,en;q=0.5',
	'HTTP_ACCEPT_ENCODING'	=> 'gzip, deflate',
	'HTTP_ACCEPT_CHARSET'	=> 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
	'HTTP_CONNECTION'		=> 'keep-alive',
	'HTTP_REFERER' 			=> 'http://polycademy.com/',
	'HTTP_COOKIE'			=> 'PHPSESSID=5epc0am69c4olckfrav2843gk4',
	'GATEWAY_INTERFACE'		=> 'CGI/1.1',
	'SERVER_ADDR'			=> '127.0.0.1',
	'SERVER_SOFTWARE'		=> 'Apache/1000 (Win99) PHP/1000',
	'SERVER_NAME'			=> 'PHPBounce',
	'SERVER_PORT'			=> '80',
	'REMOTE_ADDR'			=> '127.0.0.1',
	'PATH'					=> '/you/can/not/see/this',
	'DOCUMENT_ROOT'			=> '/waa/waa/waa',
	'SERVER_SIGNATURE'		=> '',
	'SERVER_ADMIN'			=> 'CMCDragonkai@PHPBounce',
	'REMOTE_PORT'			=> '12345',
	'REQUEST_URI'			=> '/PHPBounce.php',
	'SCRIPT_NAME'			=> '/PHPBounce.php',
	'PHP_SELF'				=> '/PHPBounce.php',
	'SCRIPT_FILENAME'		=> '/direct/to/your/mother/awesomesauce.php',
	'REQUEST_METHOD'		=> 'GET',
	'SERVER_PROTOCOL'		=> 'HTTP/1.1',
	'QUERY_STRING'			=> '',
	'REQUEST_TIME'			=> time(),
);

$env_environment = array(
	'UNIQUE_ID'				=> 'TiW4xwozAK0AAB2hKeoAAAAE',
);

foreach($server_environment as $env_key => $value){
	$_SERVER[$env_key] = $value;
}

foreach($env_environment as $env_key => $value){
	$_ENV[$env_key] = $value;
}