<?php
if(ENV=="DEVELOPMENT"){
    require("../config/config.php");
}else{
    require("../config/config.prod.php");
}

$cn = new mysqli(HOST,USER,PASS,NAME);
if($cn->connect_error) {
  	echo $cn->connect_error;die;
  	#trigger_error('Cannot connect to database. ' . $cn->connect_error);
}

$dsn = sprintf("mysql:host=%s;dbname=%s",HOST,NAME);
$cn = new PDO($dsn,USER,PASS);
