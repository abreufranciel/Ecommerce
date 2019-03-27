<?php if ( isset($_GET['v']) && ( $_GET['v'] == 'cadastrar' || $_GET['v'] == 'editar' ) ) : ?>
	<?php

		if($_POST)
		{		
			$data = date('d/m/Y');
			$id_prestador = $_SESSION['id'];
			$titulo_servico = $_POST['titulo-servico'];
			$descricao_servico = $_POST['descricao-servico'];
			$tipo_valor = $_POST['tipo-valor'];
			$valor_servico = ($_POST['valor-servico']) ? $_POST['valor-servico'] : null;
			$kg_acrescimo = null;

			if($_POST['tipo-valor'] == 0)
			{
				$kg_acrescimo = serialize($_POST['kg-acrescimo']);
			}

			$categoria_servico = intval($_POST['categoria-servico']);
			$status_servico = $_POST['status-servico'];
		}

		if( isset($_POST['add-servico']) )
		{
			$insert = $con->prepare('Insert into servico (id_prest_serv, id_categoria, titulo, descricao, valor, tipo_valor, kg_acresc, publicado, data) values (:prest_serv, :cat, :titulo, :desc, :valor, :tipo_valor, :kg, :publicado, :data)');
			$result = $insert->execute( array  (
				':prest_serv' => $id_prestador,
				':cat' => $categoria_servico,
				':titulo' => $titulo_servico,
				':desc' => $descricao_servico,
				':valor' =>  $valor_servico,
				':tipo_valor' => $tipo_valor,
				':kg' => $kg_acrescimo,
				':publicado' => $status_servico,
				':data' => $data
				) 
			);

			if ($result)
			{
				exit('<p class="sucess">Serviço cadastrado com sucesso!</p><br><a href="painel.php?p=servicos">Voltar para lista</a>');
			}
			else
			{
				exit('<p class="error">Um erro inesperado aconteceu!</p><br><a href="painel.php?p=servicos">Voltar para lista</a>');
			}
		}
		else if ( isset($_POST['edit-servico']) )
		{
			$id = $_POST['id-servico'];
			$update = $con->prepare('Update servico set id_prest_serv = :prest_serv, id_categoria = :cat, titulo = :titulo, descricao = :desc, valor = :valor, tipo_valor = :tipo_valor, kg_acresc = :kg, publicado = :publicado, data = :data where id = :id');
			$result = $update->execute( array  (
				':id' => $id,
				':prest_serv' => $id_prestador,
				':cat' => $categoria_servico,
				':titulo' => $titulo_servico,
				':desc' => $descricao_servico,
				':valor' =>  $valor_servico,
				':tipo_valor' => $tipo_valor,
				':kg' => $kg_acrescimo,
				':publicado' => $status_servico,
				':data' => $data
				) 
			);

			if ($result)
			{
				echo '<p class="sucess">Serviço editado com sucesso!</p>';
			}
		}
	?>

<?php endif; ?>

<?php if (!isset($_GET['v'])) : ?>

	<a href="?p=servicos&v=cadastrar" class="btn-pp">Adicionar Novo Serviço</a>
	
	<br/><br/>
	<label>Filtrar por:</label>
	<div class="box-filter">
	    <form action="" method="post">
	        <div class="row">
	            <div class="col-lg-4">
	                <label>Título</label>
        	        <input type="text" value="" name="titulo-servico" class="input-light" id="pesquisa-titulo-servico">   
	            </div>
	            <div class="col-lg-4">
	                <label>Categoria</label>
	                <select name="categoria-servico" class="input-light" id="pesquisa-categoria-servico">
            	        <option value="6">Selecione..</option>
            	        <option value="1">Hotelaria</option>
            	        <option value="2">PetShop</option>
            	        <option value="3">Veterinária</option>
            	        <option value="4">Day Care</option>
            	        <option value="5">Banho e Tosa</option>
        	        </select>
	            </div>
	        </div>
	    </form>
	</div>

	

	<?php 

		$qtdRegistros = 5;
		$numPagina = (isset($_GET['pp'])) ? intval( $_GET['pp'] ) : 0;
		$limite = $numPagina * $qtdRegistros;

		$select1 = $con->prepare('Select * from servico where id_prest_serv = :id');
		$select1->execute( array (':id' => $_SESSION['id'] ) );

		$servicos1 = $select1->fetchAll(PDO::FETCH_ASSOC);

		$qtdServicos = count($servicos1);
		
		$select = $con->prepare('Select * from servico where id_prest_serv = :id LIMIT :inicio, :fim');
		$select->execute( array (':id' => $_SESSION['id'], ':inicio' => $limite, ':fim' => $qtdRegistros ) );

		$servicos = $select->fetchAll(PDO::FETCH_ASSOC);

	?>
<div class="register-services">
	<table class="table-services" cellspacing="0">
		<tr>
			<th>Data</th>
			<th>Titulo</th>
			<th>Categoria</th>
			<th>Status</th>			
		</tr>
		<?php foreach ($servicos as $key => $servico) : ?>
			<?php $url = 'painel.php?p=servicos&v=editar&id=' . $servico['id']; ?>
			<?php $id = $servico['id']; ?>
			<tr onclick="editarServico('<?php echo $url ?>');" class="tr-hover">
				<?php 
					$categoria = $con->prepare('Select titulo from categoria where id = :id'); 
					$categoria->execute( array (':id' => $servico['id_categoria']) );
					$resCategoria = $categoria->fetch(PDO::FETCH_ASSOC);
				?>
				<td><?php echo $servico['data']; ?> </td>
				<td><?php echo $servico['titulo']; ?></td>
				<td><?php echo $resCategoria['titulo']; ?></td>
				<td><?php echo ($servico['publicado'] == 'on') ? 'Publicado' : 'Não Publicado' ; ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<div class="pagination-admin">
		<ul>
			<?php 

				if ($numPagina > 0 )
				{
					$paginaAnterior = $numPagina - 1;
					?>
						<li><a href="<?php echo 'painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$paginaAnterior) ) );  ?>"><</a></li>
					<?php
				}
			?>
			<?php

				if( $qtdServicos >= $qtdRegistros )
				{
					$total = $qtdServicos/$qtdRegistros;

					for ($i=0; $i < $total; $i++) 
					{ 
						$ur = 'painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$i) ) ); 
						echo '<li><a href="' . $ur . '">' . ($i + 1) . '</a></li>';
					}

				}
			?>
			<?php 
				$totalPaginas = $qtdServicos/$qtdRegistros; 

				if ( $numPagina < ($totalPaginas - 1) )
				{
					$paginaSeguinte = $numPagina + 1;
					?>
						<li><a href="<?php echo 'painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$paginaSeguinte) ) );  ?>">></a></li>
					<?php
				}
			?>
		</ul>
	</div>
</div>
<div class="clear"></div>

<?php elseif ( isset($_GET['v']) && $_GET['v'] == 'cadastrar' ) : ?>

	<form action="" method="post" class="form-servico" id="form-service">
		
		<h2 class="subtitle">Descrição</h2>
		<span style="font-size: 9pt">Título</span>
		<br>
		<input type="text" name="titulo-servico" class="input-light" required="" style="width: 99%;">
		<br>
		<textarea name="descricao-servico" class="input-light editor-wyg"></textarea>
		<br>

		<h2 class="subtitle">Valor</h2>

		<div class="obs">*Valor váriável: difere valor do serviço por kg</div>
		<label style="vertical-align: middle;">Tipo</label>
		<input type="radio" name="tipo-valor" value="1" class="input-light" checked=""> 
			<label style="width: 100px; text-align: left;"> Valor Fixo </label> 
		<input type="radio" name="tipo-valor" value="0"> 
			<label style="width: 100px; text-align: left;"> Valor Variável </label>
		<br>
		<div class="valor-servico">
			<label>Valor</label>	
				<input type="number" name="valor-servico" step="0.01" class="input-light">
		</div>
		<div class="acrescimo-servico" style="display: none;">
			<br>
			<label>até 5kg</label>
				<input type="number" name="kg-acrescimo[]" class="input-light" style="width: 10%;">
			<label>5 a 10kg</label>
				<input type="number" name="kg-acrescimo[]" class="input-light" style="width: 10%;">
			<label>10 a 20kg</label>
				<input type="number" name="kg-acrescimo[]" class="input-light" style="width: 10%;">
			<label>20 a 40kg</label>
				<input type="number" name="kg-acrescimo[]" class="input-light" style="width: 10%;">
			<label>+ 40kg</label>
				<input type="number" name="kg-acrescimo[]" class="input-light" style="width: 10%;">		
			<br>
		</div>

		<h2 class="subtitle">Categoria</h2>
		
		<i class="fas fa-home fa-1x cat-service" title="Hotelaria" onclick="atribuiCategoria(1, this);"></i>
		<i class="fas fa-shopping-cart fa-1x cat-service" title="Pet Shop" onclick="atribuiCategoria(2, this);"></i>
		<i class="fas fa-hospital fa-1x cat-service" title="Veterinária" onclick="atribuiCategoria(3, this);"></i>
		<i class="fas fa-heart fa-1x cat-service" title="Day Care" onclick="atribuiCategoria(4, this);"></i>
		<i class="fas fa-shower fa-1x cat-service" title="Banho e Tosa" onclick="atribuiCategoria(5, this);"></i>
		<i class="fas fa-ellipsis-h fa-1x cat-service" title="Outros" onclick="atribuiCategoria(6, this);"></i>

		<input type="hidden" name="categoria-servico">

		<br>

		<h2 class="subtitle">Publicado</h2>
		<i class="fas fa-toggle-off fa-2x toggle-service" onclick="toggle(this);"></i>

		<input type="hidden" name="status-servico" value="off">
		<br><br>
		<input type="submit" name="add-servico" value="Salvar" class="btn-pp-p">
	</form>

<?php elseif ( isset($_GET['v']) && $_GET['v'] == 'editar' ): ?>

<?php

	$idServico = (isset($_GET['id'])) ? intval($_GET['id']) : 0;

	if($idServico)
	{
		$consultaServico = $con->prepare('Select * from servico where id = :id and id_prest_serv = :id_prest');
		$consultaServico->execute( array (':id' => $idServico, ':id_prest' => $_SESSION['id'] ) );

		$resultadoServico = $consultaServico->fetch(PDO::FETCH_ASSOC);

		if($resultadoServico) :
			?>
			<div class="div-service">
			    <i class="fas fa-window-close btn-delete" title="Excluir Serviço" id="delete-servico"></i>
			    
				<form action="" method="post" class="form-servico" id="form-service">
					<h2 class="subtitle">Descrição</h2>
					<input type="hidden" name="id-servico" value="<?php echo $resultadoServico['id']; ?>">
						<span style="font-size: 9pt">Título</span>
					<input type="text" name="titulo-servico" class="input-light" required="" value="<?php echo $resultadoServico['titulo']; ?> " style="width: 99%;">
					<br>
					<textarea name="descricao-servico" class="input-light editor-wyg"><?php echo $resultadoServico['descricao']; ?></textarea>
					<br>
					<h2 class="subtitle">Valor</h2>

					<?php 
						//resgata o valor para verificar se o tipo do valor é fixo ou variável
						$tipoValor = $resultadoServico['tipo_valor'];   
					?>
					<div class="obs">*Valor váriável: difere valor do serviço por kg</div>
					<label style="vertical-align: middle;">Tipo</label>
					<input type="radio" name="tipo-valor" value="1" class="input-light" <?php echo ($tipoValor) ? 'checked' : ''; ?> > 
						<label style="width: 100px; text-align: left;"> Valor Fixo </label> 
					<input type="radio" name="tipo-valor" value="0" class="input-light" <?php echo (!$tipoValor) ? 'checked' : ''; ?>> 
						<label style="width: 100px; text-align: left;"> Valor Variável </label>
					<br>

					<div class="valor-servico" style="display: <?php echo ($tipoValor) ? 'block' : 'none' ; ?>;">
						
						<label>Valor</label>		
						<input type="number" name="valor-servico" step="0.01" class="input-light" value="<?php echo $resultadoServico['valor']; ?>">
					</div>

					<div class="acrescimo-servico" style="display: <?php echo ($tipoValor) ? 'none' : 'block' ; ?>;">
						<?php  
							if($resultadoServico['kg_acresc'] != null) :
						
								$valores = unserialize($resultadoServico['kg_acresc']);
						?>
							<br>
							<label>até 5kg</label>
								<input type="number" name="kg-acrescimo[]" class="input-light" value="<?php echo $valores[0]; ?>" style="width: 10%;">
							<label>5 a 10kg</label>
								<input type="number" name="kg-acrescimo[]" class="input-light" value="<?php echo $valores[1]; ?>" style="width: 10%;">
							<label>10 a 20kg</label>
								<input type="number" name="kg-acrescimo[]" class="input-light" value="<?php echo $valores[2]; ?>" style="width: 10%;">
							<label>20 a 40kg</label>
								<input type="number" name="kg-acrescimo[]" class="input-light" value="<?php echo $valores[3]; ?>" style="width: 10%;">
							<label>+ 40kg</label>
								<input type="number" name="kg-acrescimo[]" class="input-light" value="<?php echo $valores[4]; ?>" style="width: 10%;">		
							<br>
						<?php else: ?>

							<br>
							<label>até 5kg</label>
								<input type="number" name="kg-acrescimo[]" class="input-light" style="width: 10%;">
							<label>5 a 10kg</label>
								<input type="number" name="kg-acrescimo[]" class="input-light" style="width: 10%;">
							<label>10 a 20kg</label>
								<input type="number" name="kg-acrescimo[]" class="input-light" style="width: 10%;">
							<label>20 a 40kg</label>
								<input type="number" name="kg-acrescimo[]" class="input-light" style="width: 10%;">
							<label>+ 40kg</label>
								<input type="number" name="kg-acrescimo[]" class="input-light" style="width: 10%;">		
							<br>

						<?php endif; ?>
					</div>
					
					<?php 
						function confereCat ($valor, $dado)
						{
							if ($valor == $dado)
							{
								return 'cat-service-active';
							}
						}
					?>

					<h2 class="subtitle">Categoria</h2>

					<i class="fas fa-home fa-1x cat-service <?php echo confereCat(1, $resultadoServico['id_categoria']); ?>" title="Hotelaria" onclick="atribuiCategoria(1, this);"></i>

					<i class="fas fa-shopping-cart fa-1x cat-service <?php echo confereCat(2, $resultadoServico['id_categoria']); ?>" title="Pet Shop" onclick="atribuiCategoria(2, this);"></i>

					<i class="fas fa-hospital fa-1x cat-service <?php echo confereCat(3, $resultadoServico['id_categoria']); ?>" title="Veterinária" onclick="atribuiCategoria(3, this);"></i>

					<i class="fas fa-heart fa-1x cat-service <?php echo confereCat(4, $resultadoServico['id_categoria']); ?>" title="Day Care" onclick="atribuiCategoria(4, this);"></i>

					<i class="fas fa-shower fa-1x cat-service <?php echo confereCat(5, $resultadoServico['id_categoria']); ?>" title="Banho e Tosa" onclick="atribuiCategoria(5, this);"></i>

					<i class="fas fa-ellipsis-h fa-1x cat-service <?php echo confereCat(6, $resultadoServico['id_categoria']); ?>" title="Outros" onclick="atribuiCategoria(6, this);"></i>

					<input type="hidden" name="categoria-servico" value="<?php echo $resultadoServico['id_categoria']; ?>">

					<br>

					<?php 

						$classe = ( $resultadoServico['publicado'] == 'on' ) ? 'fa-toggle-on' : 'fa-toggle-off';
					?>
					<h2 class="subtitle">Publicado</h2>
					<i class="fas <?php echo $classe; ?> fa-2x toggle-service" onclick="toggle(this);"></i>

					<input type="hidden" name="status-servico" value="<?php echo $resultadoServico['publicado'] ?>">
					<br/><br/>
					<input type="submit" name="edit-servico" value="Salvar" class="btn-pp-p">
				</form>
			</div>
			<?php

		else:

			header('Location: painel.php?p=servicos');

		endif;
	}




?>

<?php endif; ?>