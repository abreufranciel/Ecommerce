<?php 

$host = 'localhost';
$dbname = 'u838248944_pet'; 
$user = 'u838248944_pet';
$pass = 'petplace123';


$con = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

$json = file_get_contents('php://input');

$obj = json_decode($json,true);
$acao = $obj['acao'];

if ($acao == 'consulta-voucher')
{
	$voucher = $obj['voucher'];

	$query = "SELECT * FROM pedido where voucher = :voucher";

	$select = $con->prepare($query);
	$select->execute(array (
		':voucher' => $voucher
		)	
	);
	$result = $select->fetch(PDO::FETCH_ASSOC);

	if($result)
	{

		$query = "SELECT * FROM servico where id = :id";
		$select = $con->prepare($query);
		$select->execute(array (
			':id' => $result['id_servico']
			)	
		);
		$service = $select->fetch(PDO::FETCH_ASSOC);

		$query2 = "SELECT * FROM usuario where id = :idComprador";
		$select2 = $con->prepare($query2);
		$select2->execute(array (
			':idComprador' => $result['id_comprador']
			)	
		);
		$comprador = $select2->fetch(PDO::FETCH_ASSOC);

		$valor = number_format($result['valor_servico'], 2, ',', '.');
		$voucherR = array (
			'id' => $result['id'],
			'voucher' => $result['voucher'],
			'valor' => 'R$ ' . $valor,
			'titulo' => $service['titulo'],
			'status' => $result['status'],
			'valido' => $result['valido'],
			'data' => date('d/m/Y' , strtotime( $result['data'] ) ),
			'idComprador' => $result['id_comprador'],
			'idPrestador' => $result['id_prestador'],
			'cpfComprador' => $comprador['cpf']
		);
		echo json_encode(
			array(
				'retorno' => 1,
				'voucher' => $voucherR
			)
		);
	}

	else
	{
		echo json_encode(
			array(
				'retorno' => 0
			)
		);
	}

}

else if ($acao == 'valida-voucher')
{
	$idVoucher = $obj['idVoucher'];
	$query = "UPDATE pedido set valido = 0 where id = :idVoucher";

	$select = $con->prepare($query);
	$result = $select->execute(array (
		':idVoucher' => $idVoucher
		)	
	);

	if($result)
	{
		echo json_encode( array (
			'retorno' => 1
			)
		);	
	}
	else
	{
		echo json_encode( array (
			'retorno' => 0
			)
		);	
	}
}

else if ($acao == 'salvar-perfil')
{
	$id = $obj['id'];
	$nome = $obj['nome'];
	$cpf = $obj['cpf'];
	$query = "UPDATE usuario set nome = :nome, cpf = :cpf where id = :id";

	$update = $con->prepare($query);
	$result = $update->execute(array (
		':id' => $id,
		':nome' => $nome,
		':cpf' => $cpf
		)	
	);

	if($result)
	{
		echo json_encode( array (
			'retorno' => 1
			)
		);	
	}
	else
	{
		echo json_encode( array (
			'retorno' => 0
			)
		);	
	}
}