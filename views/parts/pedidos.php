<?php 

$qtdRegistros = 5;
$numPagina = (isset($_GET['pp'])) ? intval( $_GET['pp'] ) : 0;
$limite = $numPagina * $qtdRegistros;



	
$selecionaPedidos = $con->prepare("Select * from pedido where id_prestador = :id order by data DESC");
$selecionaPedidos->execute( array ( ':id' => $_SESSION['id'] ) );

$pedidosTotal = $selecionaPedidos->fetchAll(PDO::FETCH_ASSOC);

$qtdPedidos = count($pedidosTotal);

$selecionaPedidosLimite = $con->prepare('Select * from pedido where id_prestador = :id order by data DESC LIMIT :inicio, :fim');
$selecionaPedidosLimite->execute( array (':id' => $_SESSION['id'], ':inicio' => $limite, ':fim' => $qtdRegistros ) );

$pedidos = $selecionaPedidosLimite->fetchAll(PDO::FETCH_ASSOC);


?>

<?php if(isset($pedidos)) : ?>
	<label>Filtrar por:</label>
	<div class="box-filter">
	   	<form method="post" action="">
	   	    <div class="row">
	   	        <div class="col-lg-4">
	   	            <label>Status</label>
            	    <select id="pesquisa-status-pedido" name="status-pedido" class="input-light">
            	        <option value="none">Selecione..</option>
            	        <option value="1">Válido</option>
            	        <option value="2">Aguardando Pagamento</option>
            	        <option value="3">Inválido</option>
            	    </select>
	   	        </div>
	   	        <div class="col-lg-4">
	   	             <label>Data</label>
        	        <input type="date" id="pesquisa-data-pedido" name="data-pedido" placeholder="data" class="input-light">
	   	        </div>
        	    <div class="col-lg-4">
            	    <label>CPF comprador</label>
            		<input type="text" id="pesquisa-cpf-pedido" name="cpf-comprador" placeholder="000.000.000-00" class="input-light">
        	    </div>
	   	    </div>
	   	    
    		<!--<input type="submit" name="pesquisa-voucher" value="Pesquisar" class="btn-pp-p">-->
	    </form> 
	</div>
    <br/>
    <div class="box-orders">

		<?php 

			foreach ($pedidos as $key => $pedido) 
			{
				$selecionaServico = $con->prepare('SELECT * from servico where id = :id');
				$selecionaServico->execute( array (':id' => $pedido['id_servico']) );
				$servico = $selecionaServico->fetch(PDO::FETCH_ASSOC);

				$selecionaComprador = $con->prepare('SELECT * from usuario where id = :id');
				$selecionaComprador->execute( array (':id' => $pedido['id_comprador']) );
				$comprador = $selecionaComprador->fetch(PDO::FETCH_ASSOC);

				$title = '';
				$cor = '';
				$form = '';
				if( $pedido['status'] == 1 && $pedido['valido'] == 0 )
				{
					$title = 'Inválido, aguardando pagamento';
					$cor = 'orange';
					$status = "Inválido - Aguardando Pagamento";
					//aguardado pagamento, invalido
				}
				else if ($pedido['status'] == 2 && $pedido['valido'] == 0)
				{
					$title = 'Inválido, voucher usado';
					$cor = 'red';
					$status = "Inválido - Usado";
					//usado, inválido
				}
				else if ($pedido['status'] == 2 && $pedido['valido'] == 1 )
				{
					//valido
					$title = 'Válido, pagamento efetuado';
					$cor = 'green';
					$form = 
					'<form method="post" action="voucher.php" style="float: right;">
						<input type="hidden" name="id-pedido" value="' . $pedido['id'] . '">
						<input type="submit" value="Usar" class="btn-pp-p">
					</form>';
					$status = "Válido";
				}

				?>
					<div class="header-item-admin">
						<?php 
							echo $pedido['voucher']; 
							echo $form;
						?>
						<div class="clear"></div>
					</div>
					<div class="box-item-admin">
						<table>
							<tr>
								<td>Status</td>
								<td><?php echo $status; ?></td>
							</tr>
							<tr>
								<td>Data</td>
								<td><?php echo date('d/m/Y', strtotime($pedido['data'])); ?></td>
							</tr>
							<tr>
								<td>Serviço</td>
								<td><?php echo $servico['titulo'];  ?></td>
							</tr>
							<tr>
								<td>Valor</td>
								<td><?php echo number_format($pedido['valor_servico'], 2, ',', '.'); ?></td>
							</tr>
							<tr>
								<td>Comprador</td>
								<td><?php echo $comprador['nome'] . '<br/> ' . $comprador['cpf'];  ?></td>
							</tr>
						</table>
					</div>

				<?php
			}
		?>


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

				if( $qtdPedidos >= $qtdRegistros )
				{
					$total = $qtdPedidos/$qtdRegistros;

					for ($i=0; $i < $total; $i++) 
					{ 
						$ur = 'painel.php?' . http_build_query( array_merge ( $_GET, array("pp"=>$i) ) ); 
						echo '<li><a href="' . $ur . '">' . ($i + 1) . '</a></li>';
					}

				}
			?>
			<?php 
				$totalPaginas = $qtdPedidos/$qtdRegistros; 

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

<?php endif; ?>