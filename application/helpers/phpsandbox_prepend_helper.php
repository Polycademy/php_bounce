<?php

//here we set all the environmental variables for the phpsandbox to use

//TIMEZONE
date_default_timezone_set('Australia/ACT');

//SERVER ENVIRONMENT VARS, this will replace the defaults
$server_environment_vars = array(
	'UNIQUE_ID'				=> 'TiW4xwozAK0AAB2hKeoAAAAE',
	'HTTP_HOST'				=> 'PHPBounce',
	'HTTP_USER_AGENT'		=> 'Mozilla/5.0 (Windows NT 6.0; rv:15.0) Gecko/20100101 Firefox/15.0.1',
	'HTTP_ACCEPT'			=> 'text/html,application/xhtml+xml,application/xml;q=0.9,*\/*;q=0.8',
	'HTTP_ACCEPT_LANGUAGE'	=> 'en-us,en;q=0.5',
	'HTTP_ACCEPT_ENCODING'	=> 'gzip, deflate',
	'HTTP_ACCEPT_CHARSET'	=> 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
	'HTTP_CONNECTION'		=> 'keep-alive',
	'HTTP_REFERER' 			=> 'http://polycademy.com/',
	'HTTP_COOKIE'			=> 'PHPSESSID=5epc0am69c4olckfrav2843gk4',
	'PATH'					=> '/usr/bin:/bin:/usr/sbin:/sbin',
	'SERVER_SIGNATURE'		=> '',
	'SERVER_SOFTWARE'		=> 'Apache/2.2.17 (Unix) mod_ssl/2.2.17 OpenSSL/1.0.0d DAV/2 PHP/5.3.5',
	'SERVER_NAME'			=> 'PHPBounce',
	'SERVER_ADDR'			=> '127.0.0.1',
	'SERVER_PORT'			=> '80',
	'REMOTE_ADDR'			=> '127.0.0.1',
	'DOCUMENT_ROOT'			=> '/',
	'SERVER_ADMIN'			=> 'root@PHPBounce',
	'SCRIPT_FILENAME'		=> 'awesomesauce.php',
	'REMOTE_PORT'			=> '49653',
	'GATEWAY_INTERFACE'		=> 'CGI/1.1',
	'SERVER_PROTOCOL'		=> 'HTTP/1.1',
	'REQUEST_METHOD'		=> 'GET',
	'QUERY_STRING'			=> '',
	'REQUEST_URI'			=> '/beingawesome.php',
	'SCRIPT_NAME'			=> '/beingawesome.php',
	'PHP_SELF'				=> '/beingawesome.php',
	'REQUEST_TIME'			=> 1311094983,
);

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

foreach($server_environment_vars as $env_key => $value){
	$_ENV[$env_key] = $value;
}