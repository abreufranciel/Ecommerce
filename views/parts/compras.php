<?php 

    $qtdRegistros = 5;
    $numPagina = (isset($_GET['pp'])) ? intval( $_GET['pp'] ) : 0;
    $limite = $numPagina * $qtdRegistros;
	
	$selecionaPedidos = $con->prepare("Select * from pedido where id_comprador = :id");
	$selecionaPedidos->execute( array ( ':id' => $_SESSION['id'] ) );

	$pedidosTotal = $selecionaPedidos->fetchAll(PDO::FETCH_ASSOC);

	$qtdPedidos = count($pedidosTotal);

	$selecionaPedidosLimite = $con->prepare('Select * from pedido where id_comprador = :id LIMIT :inicio, :fim');
	$selecionaPedidosLimite->execute( array (':id' => $_SESSION['id'], ':inicio' => $limite, ':fim' => $qtdRegistros ) );

	$pedidos = $selecionaPedidosLimite->fetchAll(PDO::FETCH_ASSOC);


?>

<?php if(isset($pedidos)) : ?>
	
	<label>Filtrar por:</label>
	<div class="box-filter">
	    <form method="post" action="">
	        <div class="row">
	            <div class="col-lg-4">
	                <label>Voucher</label>
    		        <input type="text" name="voucher-servico" class="input-light" placeholder="V1ACCKA" id="pesquisa-voucher-compra">
	            </div>
    	     <!--   <label>Serviço</label>-->
    		    <!--<input type="text" name="nome-servico" class="input-light" placeholder="Ex: Tosa" id="pesquisa-nome-compra">-->
    		    <div class="col-lg-4">
    		        <label>Data</label>
    		        <input type="date" name="data-servico" class="input-light" id="pesquisa-data-compra">
    		    </div>
	        </div>
	    </form>
	</div>

<div class="box-orders">
	<table class="table-orders" cellspacing="0">
		<tr>
			<th>Voucher</th>
			<th>Serviço</th>
			<th>Data</th>
			<th>Valor</th>
			<th>Vendido por</th>
		</tr>

		<?php 


			foreach ($pedidos as $key => $pedido) 
			{
				$selecionaServico = $con->prepare('SELECT * from servico where id = :id');
				$selecionaServico->execute( array (':id' => $pedido['id_servico']) );
				$servico = $selecionaServico->fetch(PDO::FETCH_ASSOC);

				$selecionaPrestador = $con->prepare('SELECT * from usuario where id = :id');
				$selecionaPrestador->execute( array (':id' => $pedido['id_prestador']) );
				$prestador = $selecionaPrestador->fetch(PDO::FETCH_ASSOC);

				echo '<tr class="tr-hover">';
				echo '<td>' . $pedido['voucher'] . '</td>';
				echo '<td>' . $servico['titulo'] . '</td>';
				echo '<td>' . date('d/m/Y', strtotime($pedido['data'])) . '</td>';
				echo '<td>R$ ' . number_format($pedido['valor_servico'], 2, ',', '.') . '</td>';
				echo '<td>' . $prestador['nome'] . '</td>';
				echo '</tr>';
			}


		?>
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
	<div class="clear"></div>
</div>
<?php endif; ?>