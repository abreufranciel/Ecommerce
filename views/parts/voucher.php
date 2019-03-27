<?php 

$id = $_POST['id-pedido'];
	$query = "UPDATE pedido set valido = 0 where id = :idPedido";

	$select = $con->prepare($query);
	$result = $select->execute(array (
		':idPedido' => $id
		)	
	);

	if($result)
	{
		header('Location: painel.php?p=pedidos');
	}
	else
	{
		
	}
