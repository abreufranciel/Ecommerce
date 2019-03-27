<?php

if(!isset($_GET['v'])) :

	?>
	<div class="box-admin-content">
		<a href="?p=pets&v=cadastrar" class="btn-pp">Adicionar</a>
		<br>
		<form method="post" class="search-pet">	
			<input type="text" name="pesquisa-pet-txt" placeholder="Ex: Thor" class="input-light input-w-duo">
			<input type="submit" name="pesquisar-pet" value="Pesquisar" class="btn-pp-p">
		</form>
		<br>
		<?php

		if(isset($_POST['pesquisar-pet']))
		{
			$pesquisa = $_POST['pesquisa-pet-txt'];
			$sql = "Select * from animal where id_dono = :dono  and nome like :pesquisa";
			$array = array ( 
				':dono' => $_SESSION['id'],
				':pesquisa' => '%' . $pesquisa . '%'
			);
		}
		else
		{
			$sql = "Select * from animal where id_dono = :dono";
			$array = array ( 
				':dono' => $_SESSION['id']
			);
		}
		$consult = $con->prepare( $sql );
		$consult->execute( $array );
		$result = $consult->fetchAll(PDO::FETCH_ASSOC);

		foreach ($result as $pet) 
		{
			$html = '<div class="box-info-pet">';
			if(empty($pet['imagem']))
			{
				$html .= '<img src="views/imgs/pet.jpg">';
			}		
			else
			{
				$html .= '<img src="' . $pet['imagem'] . '">';
			}
			$html .= '<p class="name-pet">' . $pet['nome'] . '</p>';
			$html .= '</div>';

			echo $html;
		}

		//var_dump($result);
		?>

	</div>
<?php

elseif( isset($_GET['v']) && $_GET['v'] == 'cadastrar') :

	if (isset($_POST['cadastrar-pet'])) :

		$nomePet  = $_POST['nome-pet'];
		$idadePet = $_POST['idade-pet'];
		$tipoPet = $_POST['tipo-pet'];
		$racaPet = $_POST['raca-pet'];
		$kgPet = $_POST['kg-pet'];

		$imagemPet = ( $_FILES['imagem-pet']['error'] != 4 ) ? $_FILES['imagem-pet'] : false;

		if($imagemPet)
		{			
			$extensoes = array ('.jpg', '.png');
			$extensao = strrchr($_FILES['imagem-pet']['name'], '.');

			//verifica se é png ou jpg
			if(in_array($extensao, $extensoes) === true)
			{

				require_once ('php/wideimage/lib/wideimage.php');

				$uploaddir = 'upload/pets/';
				$uploadfile = $uploaddir . 'user-' . $_SESSION['id'] . rand(0, 99999) . $_FILES['imagem-pet']['name'];

				if (move_uploaded_file($_FILES['imagem-pet']['tmp_name'], $uploadfile))
				{

					$sql = "Insert into animal (id_dono, nome, idade, tipo, raca, kg, imagem) values (:dono, :nome, :idade, :tipo, :raca, :kg, :imagem)";
					$array = array (
						':dono' => $_SESSION['id'],
						':nome' => $nomePet,
						':idade' => $idadePet,
						':tipo' => $tipoPet,
						':raca' => $racaPet,
						':kg' => $kgPet,
						':imagem' =>  $uploadfile
					);

					WideImage::load($uploadfile)->resize(210, 210, 'fill')->saveToFile($uploadfile);
	
				}

			}
			else
			{
				echo '<div class="error-pp"><p>Por favor, envie arquivos com as seguintes extensões: jpg ou png.</p></div>';
			}
		}

		else
		{

			$sql = "Insert into animal (id_dono, nome, idade, tipo, raca, kg) values (:dono, :nome, :idade, :tipo, :raca, :kg)";
			$array = array (
				':dono' => $_SESSION['id'],
				':nome' => $nomePet,
				':idade' => $idadePet,
				':tipo' => $tipoPet,
				':raca' => $racaPet,
				':kg' => $kgPet
			);
		}

		if(isset($sql) && isset($array))
		{

			$insert = $con->prepare($sql);
			$result = $insert->execute($array);

			if ($result) 
			{
				// echo '<p class="sucess">Pet cadastrado com sucesso!</p>';
				// echo '<a href="painel?p=pets" >Voltar para lista</a>';

				exit('<p class="sucess">Pet cadastrado com sucesso!</p><br><a href="painel?p=pets">Voltar para lista</a>');
			}
		}
	endif;
?>

	<!-- <img class="photo-pet" src="views/imgs/user.png" id="imagem-pet"> -->
	<form method="post" enctype="multipart/form-data" class="form-pet" action="">
		<label>Nome</label>
		<input type="text" name="nome-pet" required="" class="input-light input-w-duo">
			<br>
		<label>Idade</label>
		<input type="number" name="idade-pet" min="1" required="" class="input-light">
			<br>
		<label>Tipo</label>
		<select name="tipo-pet" required="" class="input-light">
			<option value="1">Cão</option>
			<option value="0">Gato</option>
		</select>
		<label>Raça</label>
		<select name="raca-pet" required="" class="input-light dog">
			<option value="none">Selecione..</option>
			<option value="beagle">Beagle</option>
			<option value="buldogue">Buldogue</option>
			<option value="chihuahua">Chihuahua</option>
			<option value="pug">Pug</option>
			<option value="pastor-alemao">Pastor-Alemão</option>
			<option value="golden-retriever">Golden Retriever</option>
			<option value="pitBull">PitBull</option>
			<option value="yorkshire">Yorkshire</option>
			<option value="outros">Outros</option>
		</select>

		<select name="raca-pet" required="" class="input-light cat" style="display: none;">
			<option value="none">Selecione..</option>
			<option value="persa">Persa</option>
			<option value="siames">Siamês</option>
			<option value="ragdoll">Ragdoll</option>
			<option value="birmanes">Birmanês</option>
			<option value="siberiano">Siberiano</option>
			<option value="himalaio">Himalaio</option>
			<option value="outros">Outros</option>
		</select>
			<br>
		<label>KG</label>
		<input type="number" name="kg-pet" min="1" required="" class="input-light">
		<br>
		<label>Foto</label>
		<input type="file" name="imagem-pet" onchange="mudaFotoPet('#imagem-pet')" class="input-light">

		<br>
		<a href="?p=pets" class="btn-pp">&#8617; Voltar</a>
		<input type="submit" name="cadastrar-pet" value="Cadastrar" class="btn-pp-p">
	</form>
<?php 
endif;