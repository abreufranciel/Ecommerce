<?php 

require "../conn.php";

function geraVoucher($tamanho = 5, $maiusculas = true, $numeros = true, $simbolos = false)
{
	$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$num = '1234567890';
	$simb = '!@#$%*-';
	$retorno = '';
	$caracteres = '';
	$caracteres .= $lmai;
	if ($numeros) $caracteres .= $num;
	if ($simbolos) $caracteres .= $simb;
	$len = strlen($caracteres);
	for ($n = 1; $n <= $tamanho; $n++) 
	{
		$rand = mt_rand(1, $len);
		$retorno .= $caracteres[$rand-1];
	}
	return $retorno;
}
$id_comprador = $_POST['idComprador'];
$id_prestador = $_POST['idPrestador'];
$id_servico = $_POST['idServico'];
$voucher = 'V' . $id_comprador . geraVoucher();

$insert = $con->prepare("Insert into pedido (voucher, id_servico, id_comprador, id_prestador, status) VALUES (:voucher, :servico, :comprador, :prestador, :status)");
$result = $insert->execute(  array (
	':voucher' => $voucher,
	':servico' => $id_servico,
	':comprador' => $id_comprador,
	':prestador' => $id_prestador,
	':status' => 1
	)
);

if($result)
{
	$return['cod'] = 1;
	$return['idPedido'] = $con->lastInsertId();
	echo json_encode($return);
}
else
{
	$return['cod'] = 0;
	echo json_encode($return);
}