<?php 

//verificar se o botão salvar foi pressionado
if( isset($_POST['salva-dados-usuario']) )
{
	$cpfUsuario = $_POST['cpf-usuario'];
	$nomeUsuario = $_POST['nome-usuario'];
	$emailUsuario = $_POST['email-usuario'];
	$cidadeUsuario = $_POST['cidade-usuario'];
	$ufUsuario = $_POST['uf-usuario'];
	$ruaUsuario = $_POST['rua-usuario'];
	$numeroUsuario = $_POST['numero-usuario'];
	$bairroUsuario = $_POST['bairro-usuario'];

	//verifica se o modo vendedor está ligado
	$vendedor = (isset( $_POST['vendedor-ativo'] ) ) ? true :  false;


	if($vendedor)
	{
		$tipo = $_POST['tipo-vendedor'];
		$h_inicio = $_POST['hora-inicio'];
		$h_fim = $_POST['hora-fim'];
		$whats = $_POST['whats-vendedor'];
		$insta = $_POST['insta-vendedor'];
		$face = $_POST['face-vendedor'];
		$maps = $_POST['apikey-vendedor'];
		$descricao = $_POST['descricao-vendedor'];
	}

	$error['email'] = 0;
	$sucess['dados'] = 1;

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
		$modoVendedor = ($vendedor) ? 1 : 0;

		$mudaDados = $con->prepare("Update usuario set cpf = :cpf, nome = :nome, email = :email, cidade = :cidade, uf = :uf, vendedor = :vendedor, bairro = :bairro, rua = :rua, numero = :numero where id = :id");
		$resultadoDados = $mudaDados->execute( array (
			':id' => $_SESSION['id'],
			':cpf' => $cpfUsuario,
			':nome' => $nomeUsuario,
			':email' => $emailUsuario,
			':cidade' => $cidadeUsuario,
			':uf' => $ufUsuario,
			':vendedor' => $modoVendedor,
			':bairro' => $bairroUsuario,
			':rua' => $ruaUsuario,
			':numero' => $numeroUsuario
			)
		);

		if (!$resultadoDados)
			$sucess['dados'] = 0;
			//print_r($mudaDados->errorInfo());

		
		if ($vendedor)
		{
			$selectV = $con->prepare("Select * from vendedor where id_usuario = :id");

			$selectV->execute( array ( ':id' => $_SESSION['id'] ) );

			$return = $selectV->fetch(PDO::FETCH_ASSOC);

			$array = array (
				':id' => $_SESSION['id'],
				':tipo' => $tipo,
				':h_inicio' => $h_inicio,
				':h_fim' => $h_fim,
				':face' => $face,
				':insta' => $insta,
				':whats' => $whats,
				':desc' => $descricao,
				':maps' => $maps
				);

			if($return)
			{
				$updateV = $con->prepare('Update vendedor set tipo= :tipo, hora_inicio= :h_inicio, hora_fim= :h_fim, facebook= :face, whatsapp= :whats, instagram= :insta, descricao= :desc, maps= :maps where id_usuario = :id');
				$resultadoDadosV = $updateV->execute( $array );

				if(!$resultadoDadosV)
					$sucess['dados'] = 0;
			}
			else
			{
				$insertV = $con->prepare('Insert into vendedor (id_usuario, tipo, hora_inicio, hora_fim, facebook, whatsapp, instagram, descricao, maps) VALUES (:id, :tipo, :h_inicio, :h_fim, :face, :insta, :whats, :desc, :maps)');
				$resultadoDadosV = $insertV->execute( $array );
				if(!$resultadoDadosV)
					$sucess['dados'] = 0;
			}
		}


		if($sucess['dados'])
		{
			
			//var_dump($_SERVER['REQUEST_URI']);
			echo '<p class="sucess">Dados alterados com sucesso!</p>';

		}
		else
		{
			echo '<p class="error">Erro ao alterar dados!</p>';
			var_dump($mudaDadosV->errorInfo());
		}
	}

	else
	{
		echo 'Algo de errado aconteceu, tente novamente';
	}

}

//resgata os dados do usuário
$consultaDados = $con->prepare("Select * from usuario where id = :id");
$consultaDados->execute( array (':id' => $_SESSION['id']) );
$dadosUsuario = $consultaDados->fetch(PDO::FETCH_ASSOC);

//resgata os dados do modo vendedor
$consultaDadosVendedor = $con->prepare("Select * from vendedor where id_usuario = :id");
$consultaDadosVendedor->execute( array (':id' => $_SESSION['id']) );
$dadosVendedor = $consultaDadosVendedor->fetch(PDO::FETCH_ASSOC);


//resgata nome da cidade usuario
// $consultaCidade = $con->prepare("Select Nome from cidades where Id = :id");
// $consultaCidade->execute( array (':id' => $dadosUsuario['cidade']) );
// $nomeCidade = $consultaCidade->fetch(PDO::FETCH_ASSOC);

$_SESSION['nome'] = $dadosUsuario['nome'];
$_SESSION['cidade'] = $dadosUsuario['cidade'];
$_SESSION['email'] = $dadosUsuario['email'];
$_SESSION['uf'] = $dadosUsuario['uf'];

$uf = $con->prepare('Select Nome, UF from estados');
$uf->execute();
$ufAll =  $uf->fetchAll(PDO::FETCH_ASSOC);

?>

<form method="post" enctype="multipart/form-data" action="" class="form-profile">
	<label>CPF</label>

	<input type="text" class="input-light" name="cpf-usuario" value="<?php echo $dadosUsuario['cpf']; ?>">

	<br>
	<label>Nome</label>

	<input type="text" class="input-light input-medium" name="nome-usuario" value="<?php echo $dadosUsuario['nome']; ?>">

	<br>
	<label>Email</label>	

	<input type="text" class="input-light input-medium" name="email-usuario" value="<?php echo $dadosUsuario['email']; ?>">


	<br>
	<label>UF</label>

	<select name="uf-usuario" class="input-light input-small">
		<?php
			foreach ($ufAll as $key => $uf) 
			{
				if ($dadosUsuario['uf'] == $uf['UF'])
				{
					echo '<option value="' . $uf['UF'] . '" selected="">' . $uf['Nome'] . '</option>';
				}
				else
				{
					echo '<option value="' . $uf['UF'] . '">' . $uf['Nome'] . '</option>';
				}
			}
		?>
	</select>

	<label class="label-right">Cidade</label>


	<input type="text" class="input-light input-small" name="cidade-usuario" value="<?php echo $dadosUsuario['cidade']; ?>">

	<label class="label-right">Bairro</label>

	<input type="text" class="input-light input-small" name="bairro-usuario" value="<?php echo $dadosUsuario['bairro']; ?>">

	<br>
	<label>Número</label>

	<input type="text" class="input-light input-small" name="numero-usuario" value="<?php echo $dadosUsuario['numero'] ?>">

	<label class="label-right">Rua</label>

	<input type="text" class="input-light input-small" name="rua-usuario" value="<?php echo $dadosUsuario['rua']; ?>">

	<br>
	<input type="checkbox" name="vendedor-ativo" id="ativar-modo-ven" <?php if($dadosUsuario['vendedor']) echo 'checked=""'; ?> > <span>Ativar Modo Vendedor</span>
	<?php if(!$dadosUsuario['vendedor']) $disabled = 'disabled=""'; else $disabled = ""; ?>

	<br><br>
	<label>Tipo</label>

	<select name="tipo-vendedor" class="input-light input-small">
		<option value="1">Autonômo</option>
		<option value="2">Clínica Veterinária</option>
		<option value="3">Pet Shop</option>
	</select>

	<label class="label-right">Abre às</label>

	<input type="time" class="input-light" name="hora-inicio" <?php echo $disabled; ?> value="<?php echo $dadosVendedor['hora_inicio']; ?>">

	<label class="label-right">Fecha às</label>

	<input type="time" class="input-light" name="hora-fim" <?php echo $disabled; ?> value="<?php echo $dadosVendedor['hora_fim']; ?>">

	<br>
	<label>Facebook</label>

	<input type="text" class="input-light input-small" name="face-vendedor" <?php echo $disabled; ?> placeholder="/petplace" value="<?php echo $dadosVendedor['facebook']; ?>">

	<label class="label-right">Instagram</label>

	<input type="text" class="input-light input-small" name="insta-vendedor" <?php echo $disabled; ?> placeholder="/petplace" value="<?php echo $dadosVendedor['instagram']; ?>">

	<label class="label-right">WhatsApp</label>

	<input type="text" class="input-light input-small" name="whats-vendedor" <?php echo $disabled; ?> placeholder="(24) 9 9900 - 0099" value="<?php echo $dadosVendedor['whatsapp']; ?> ">

	<br/>

	<label>Google Maps</label>

	<input type="text" class="input-light input-medium" name="apikey-vendedor" <?php echo $disabled; ?> placeholder="API Key" value="<?php echo $dadosVendedor['maps']; ?>">

	<br>
	<label class="ali-top">Descrição</label>
	<textarea name="descricao-vendedor" class="input-light editor-wyg" rows="5" <?php echo $disabled; ?> placeholder="Uma breve descrição aqui..."><?php echo $dadosVendedor['descricao']; ?></textarea>

	<input type="submit" name="salva-dados-usuario" value="Salvar" class="btn-pp-p">
	
</form>

<?php

