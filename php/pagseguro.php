<?php 

require "../conn.php";

function geraVoucher($tamanho = 10, $maiusculas = true, $numeros = true, $simbolos = false)
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
	$idPedido = $con->lastInsertId();
}
else
{
	$idPedido = null;
}

$id = filter_input(INPUT_POST, 'idServico', FILTER_SANITIZE_SPECIAL_CHARS);
$titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
$valor = filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_SPECIAL_CHARS);

$valor = number_format($valor, 2, '.', '.');

$data['token'] = '101A7C68C433488E8EEA277A7BB2EE30';
$data['email'] = 'gislanefabiano@gmail.com';
$data['currency'] = 'BRL';
$data['itemId1'] = $id;
$data['itemDescription1'] = $titulo;
$data['itemAmount1'] = $valor;
$data['itemQuantity1'] = '1';
$data['reference'] = $idPedido;

$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout';

$data = http_build_query($data);

$curl = curl_init($url);

curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);

$xml = curl_exec($curl);

curl_close($curl);

$xml = simplexml_load_string($xml);

echo $xml->code;