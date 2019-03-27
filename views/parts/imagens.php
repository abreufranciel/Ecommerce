<?php 

if( isset ( $_POST['troca-capa'] ) )

{
	$imagemCapa = ( $_FILES['imagem-capa']['error'] != 4 ) ? $_FILES['imagem-capa'] : false;

	if($imagemCapa)
	{			
		$extensoes = array ('.jpg', '.png');
		$extensao = strrchr($_FILES['imagem-capa']['name'], '.');

		//verifica se é png ou jpg
		if(in_array($extensao, $extensoes) === true)
		{

			$uploaddir = 'upload/perfil/';
			$uploadfile = $uploaddir . 'capa-user-' . $_SESSION['id'] . rand(0, 99999) . $_FILES['imagem-capa']['name'];

			if (move_uploaded_file($_FILES['imagem-capa']['tmp_name'], $uploadfile))
			{
				
				if(!empty($_SESSION['capa']))
				{
					unlink($_SESSION['capa']);
				}

				$mudaCapa = $con->prepare("Update usuario set capa = :capa where id = :id_usuario ");
				$resultCapa = $mudaCapa->execute(array ( ':capa' => $uploadfile, ':id_usuario' => $_SESSION['id'] ));
				$_SESSION['capa'] = $uploadfile;

			}
		}	
	}	
}

?>

<h2 class="subtitle">Foto de Capa</h2>
<div class="box-capa" style="background-image: url(<?php echo (isset($_SESSION['capa'])) ? $_SESSION['capa'] : ''; ?>);">
	
</div>
<form action="" method="post" enctype="multipart/form-data">
	<input type="file" name="imagem-capa">
	<input type="submit" name="troca-capa" value="Salvar" class="btn-pp-p">
</form>

<div style="border-bottom: 1px solid #c5c5c5; margin: 20px 0;"></div>

<?php 
	$galeriaFotos = array ();
	if(isset($_POST['salva-galeria']))
	{
		if(isset($_POST['imagem-galeria-salva']))
		{
			$fotosSalvas = $_POST['imagem-galeria-salva'];
			foreach ($fotosSalvas as $key => $fotoSalva) 
			{
				$galeriaFotos[] = $fotoSalva;
			}
		}
		if(isset($_FILES['imagem-galeria']))
		{
			$fotos = $_FILES['imagem-galeria'];

			$qtd = count($fotos['name']);

			for ($i=0; $i < $qtd ; $i++) 
			{ 
				$extensoes = array ('.jpg', '.png');
				$extensao = strrchr($fotos['name'][$i], '.');

				//verifica se é png ou jpg
				if(in_array($extensao, $extensoes) === true)
				{

					$uploaddir = 'upload/galeria/';
					$uploadfile = $uploaddir . 'f' . $_SESSION['id'] . rand(0, 99999) . $fotos['name'][$i];

					if (move_uploaded_file($fotos['tmp_name'][$i], $uploadfile))
					{
						$galeriaFotos[] = $uploadfile;
					}
				}
			}



			// for ($i=0; $i < $qtd ; $i++) 
			// { 
			// 	# code...
			// 	$extensoes = array ('.jpg', '.png');
			// 	$extensao = strrchr($fotos['name'][$i], '.');

			// 	//verifica se é png ou jpg
			// 	if(in_array($extensao, $extensoes) === true)
			// 	{

			// 		$uploaddir = 'upload/galeria/';
			// 		$uploadfile = $uploaddir . 'f' . $_SESSION['id'] . rand(0, 99999) . $fotos['name'][$i];

			// 		if (move_uploaded_file($fotos['tmp_name'][$i], $uploadfile))
			// 		{

			// 			$adicionaFoto = $con->prepare("Insert into galeria (id_usuario, imagem) values (:id, :imagem)");
			// 			$resultGaleria = $adicionaFoto->execute(array ( ':imagem' => $uploadfile, ':id' => $_SESSION['id'] ));
			// 		}
			// 	}
			// }
		}
		if($galeriaFotos)
		{
			$selecionaGaleria = $con->prepare("Select * from galeria where id_usuario = :id");

			$selecionaGaleria->execute( array ( ':id' => $_SESSION['id'] ) );

			$return = $selecionaGaleria->fetch(PDO::FETCH_ASSOC);

			$array = array (
				':id' => $_SESSION['id'],
				':imagem' => serialize($galeriaFotos)
				);

			if($return)
			{
				$atualizaGaleria = $con->prepare('Update galeria set imagem = :imagem where id_usuario = :id');
				$resultadoAtualizacao = $atualizaGaleria->execute( $array );

				if(!$resultadoAtualizacao)
				{

					$msg = '<p class="error">Erro ao salvar galeria!</p>';
				}
			}
			else
			{
				$insereGaleria = $con->prepare("Insert into galeria (id_usuario, imagem) values (:id, :imagem)");
				$resultadoInsercao = $insereGaleria->execute( $array );

				if(!$resultadoInsercao)
				{
					$msg = '<p class="error">Erro ao salvar galeria!</p>';
				}
			}
		}
	}

?>

<h2 class="subtitle"> Galeria </h2>

<form action="" name="galeria" method="post" enctype="multipart/form-data">
<?php

	$selecionaFotos = $con->prepare('Select * from galeria where id_usuario = :id');
	$selecionaFotos->execute( array (':id' => $_SESSION['id']) );

	$galeria = $selecionaFotos->fetch(PDO::FETCH_ASSOC);

	if($galeria && !empty(unserialize($galeria['imagem'])))
	{

		if($galeria['imagem'])
		{
			$imagens = unserialize($galeria['imagem']);
		}
		foreach ($imagens as $key => $imagem) :
		?>
			<div class="preview-img">
				<i class="fas fa-minus-circle delete-img" title="Excluir"></i>
				<img width="100px" height="100px" src="<?php echo $imagem; ?>">
				<input type="hidden" name="imagem-galeria-salva[]" value="<?php echo $imagem; ?>">
			</div>
		<?php

		endforeach;
	}

?>

	<button class="btn-pp-p" id="add-foto">+</button>
	<input type="submit" name="salva-galeria" value="Salvar" class="btn-pp-p">
	
</form>
