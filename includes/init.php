<?php
#init.php
$_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);            
$_COOKIE  = filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_STRING);            
$_SERVER  = filter_input_array(INPUT_SERVER, FILTER_SANITIZE_STRING);            

define('DS',DIRECTORY_SEPARATOR);
#setlocale(LC_ALL,'pt_BR');
setlocale(LC_ALL, "pt_BR", "ptb");
$dadosEsp = localeconv();
#var_dump($dadosEsp);
#A função strftime() formata uma hora/datade acordo com as configurações locais definidas
#em setlocale(). No nosso caso, %B trará o nome do mês completo.
ini_set('log_errors',TRUE);
error_reporting(E_ALL | E_STRICT);





		#if($server=='localhost/'){
			define ('ENV','DEVELOPMENT');
		#} else {
			#define ('ENV','PRODUCTION');
		#}


function setReporting() {
	if (ENV==='DEVELOPMENT') {
		ini_set('display_startup_errors',TRUE);
		ini_set('display_errors',TRUE);
		$server = 'localhost/'; #localhost
		$http = 'http'; #http
		$uri = 'mestrado/public/'; #/effort/ex4/public ------- varia, melhor deixar engessado
		#$http = $_SERVER['REQUEST_SCHEME']?:'http'; #http
		#$script = $_SERVER['SCRIPT_FILENAME']?:''; #C:/wamp/www/effort/ex4/lib/index.php

	} else {
		ini_set('display_errors','Off');
		#ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
		$server = 'www.luisgustavoalmeida.com/'; #localhost
		$http = 'http'; #http
		#$script = ''; #C:/wamp/www/effort/ex4/lib/index.php
		#$uri = ''; #/effort/ex4/public ------- varia, melhor deixar engessado
		$uri = 'public/';
	}

	$url_parcial = $http .'://'.$server;#http://localhost 
	$url = $http .'://'.$server.$uri;#http://localhost/effort/ex4/public
	#define ('URI', $uri);
	define ('URL', $url_parcial.$uri);

}

setReporting();




#$root = $_SERVER['DOCUMENT_ROOT']?:''; #C:/wamp/www/



#echo $uri;die;

#define ('UPLOADS', 'C:/wamp/www/mestrado/uploads/'); 
#define ('LOGS', URI . '../logs/'); 

if(!session_id()) session_start();

