<?php 


$host = 'localhost';
$dbname = 'u838248944_pet'; 
$user = 'u838248944_pet';
$pass = 'petplace123';


$con = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

$json = file_get_contents('php://input');

$obj = json_decode($json,true);

$email = $obj['email'];

$senha = md5($obj['password']);

$query = "SELECT * FROM usuario where email = :email and senha = :senha";

$select = $con->prepare($query);
$select->execute(array (
	':email' => $email,
	':senha' => $senha
	)	
);
$result = $select->fetch(PDO::FETCH_ASSOC);

if($result)
{
	$usuario = array (
		'id' => $result['id'],
		'nome' => $result['nome'],
		'cpf' => $result['cpf'],
		'imagem' => $result['imagem']
	);
	echo json_encode(
		array(
			'retorno' => 1,
			'usuario' => $usuario
		)
	);
}
else
{
	echo json_encode(array('retorno' => 0));	
}