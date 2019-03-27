<?php 

//verificar se o botão salvar foi pressionado
if( isset($_POST['salva-dados-usuario']) )
{
	$nomeUsuario = $_POST['nome-usuario'];
	$emailUsuario = $_POST['email-usuario'];
	$cidadeUsuario = $_POST['cidade-usuario'];
	$ufUsuario = $_POST['uf-usuario'];
	$imagemUsuario = ( $_FILES['imagem-usuario']['error'] != 4 ) ? $_FILES['imagem-usuario'] : false;

	//verifica se o modo vendedor está ligado
	$tipoUsuario = (isset( $_POST['vendedor-ativo'] ) ) ? true :  false;

	$error['foto'] = 0;
	$error['email'] = 0;
	$sucess['imagem'] = 1;
	$sucess['dados'] = 1;

	if($imagemUsuario)
	{			
		$extensoes = array ('.jpg', '.png');
		$extensao = strrchr($_FILES['imagem-usuario']['name'], '.');

		//verifica se é png ou jpg
		if(in_array($extensao, $extensoes) === true)
		{
			//verifica tamanho da imagem
			if( validation_image($imagemUsuario['tmp_name'], 1200, 1200) )
			{

				$uploaddir = 'upload/perfil/';
				$uploadfile = $uploaddir . rand(0, 99999) . $_FILES['imagem-usuario']['name'];

				if (!move_uploaded_file($_FILES['imagem-usuario']['tmp_name'], $uploadfile))
				{
					
					$error['foto'] = 1;
					exit();

				}
			}
			else
			{
				echo '<div class="error-pp"><p>Tamanho de imagem inválido, insira uma imagem até 1200x1200</p></div>';
				exit();
			}
		
		}
		else
		{
			echo 'Por favor, envie arquivos com as seguintes extensões: jpg ou png.';
			exit();
		}
	}

	if(!$error['foto'])
	{
		//se não tiver gerado erro ao mudar foto
		if($emailUsuario != $_SESSION['email'])
		{
			//verifica no banco se o novo email já se encontra cadastrado
			$consultaEmail = $con->prepare("Select * from usuario where email = :email");
			$consultaEmail->execute( array (':email' => $emailUsuario) );

			if ( $consultaEmail->rowCount() > 0 )
			{
				echo '<div class="error-pp"><p>Email já cadastrado na base de dados</p></div>';
				$error['email'] = 1;
			}
		}

		if(!$error['email'])
		{
			//se não tiver gerado erro ao mudar email

			if(!$tipoUsuario)
			{
				//update para quando o oerfil de vendedor não estiver ativado
				//echo "normal";
				$mudaDados = $con->prepare("Update usuario set nome = :nome, email = :email, cidade = :cidade, uf = :uf where id = :id");
				$resultadoDados = $mudaDados->execute( array (
					':nome' => $nomeUsuario,
					':email' => $emailUsuario,
					':cidade' => $cidadeUsuario,
					':uf' => $ufUsuario,
					':id' => $_SESSION['id']
					)
				);

				if (!$resultadoDados)
					$sucess['dados'] = 0;


				if ($imagemUsuario)
				{
					$mudaFoto = $con->prepare("Update usuario set imagem = :imagem where id = :id_usuario ");
					$resultFoto = $mudaFoto->execute(array ( ':imagem' => $uploadfile, ':id_usuario' => $_SESSION['id'] ));

					if(!$resultFoto)
						$sucess['imagem'] = 0;
				}
			
			}
			else
			{
				//update para quando o perfil de vendedor estiver ativado
			}


			if($sucess['imagem'] && $sucess['dados'])
			{
				
				//var_dump($_SERVER['REQUEST_URI']);
				echo '<div class="sucess-pp"><p>Dados alterados com sucesso<p></div>';
	
			}
		}
	}

	else
	{
		echo 'Algo de errado aconteceu, tente novamente';
	}

}

$consultaDados = $con->prepare("Select * from usuario where id = :id");
$consultaDados->execute( array (':id' => $_SESSION['id']) );
$dadosUsuario = $consultaDados->fetch(PDO::FETCH_ASSOC);

$_SESSION['nome'] = $dadosUsuario['nome'];
$_SESSION['cidade'] = $dadosUsuario['cidade'];
$_SESSION['email'] = $dadosUsuario['email'];
$_SESSION['uf'] = $dadosUsuario['uf'];
$_SESSION['imagem'] = $dadosUsuario['imagem'];	

?>

<form method="post" enctype="multipart/form-data" action="">
	<?php if ( !empty($dadosUsuario['imagem']) ) : ?>
		<img class="photo-user-edit" src="<?php echo $dadosUsuario['imagem']; ?>">
	<?php else: ?>
		<img class="photo-user-edit" src="views/imgs/user.png">
	<?php endif; ?>

	<!-- <input type="file" name="imagem-usuario" value="Alterar" id="envia-foto-usuario"> -->
	<input type="text" class="input-light" name="nome-usuario" value="<?php echo $dadosUsuario['nome']; ?>">
	<input type="text" class="input-light" name="email-usuario" value="<?php echo $dadosUsuario['email']; ?>">
	<input type="text" class="input-light" name="cidade-usuario" value="<?php echo $dadosUsuario['cidade']; ?>">
	<select name="uf-usuario" class="input-light">
		<option>Rio</option>
	</select>

	<input type="checkbox" name="vendedor-ativo">Ativar Modo Vendedor

	<input type="submit" name="salva-dados-usuario" value="Salvar">
</form>

<?php