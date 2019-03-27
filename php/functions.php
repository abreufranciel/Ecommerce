<?php 

require "./conn.php";

function validation_image($img, $width, $height)
{
	$size_imagem =  getimagesize($img);
							
	if($size_imagem[0] <= $width && $size_imagem[1] <= $height)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function exibeCidade ($id)
{
	global $con;
	$selecionaCidade = $con->prepare('Select Nome from cidades where id = :id');
	$resultado = $selecionaCidade->execute( array ( ':id' => $id ) );

	if ($resultado)
	{
		$cidade = $selecionaCidade->fetch(PDO::FETCH_ASSOC);

		return $cidade['Nome'];
	}
}