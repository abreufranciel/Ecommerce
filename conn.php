<?php 

try {

	$host = 'localhost';
	$dbName = 'u838248944_pet'; 
	$userName = 'u838248944_pet';
	$password = 'petplace123';

	$stringCon = 'mysql:host=' . $host . ';dbname=' . $dbName .';charset=utf8';  
	$con = new PDO($stringCon, $userName, $password);

	$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
}

catch(PDOException $e) {
	echo 'Erro: ' . $e->getMessage();
}