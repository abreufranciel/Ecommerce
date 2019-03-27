    <?php
	
		if(isset($_GET['s']) && $_GET['s'])
		{
		    $estado = (isset ($_POST['uf'])) ? $_POST['uf'] : '';
		    $cidade = (isset ($_POST['cidade'])) ? $_POST['cidade'] : '';
			if(isset($_POST['cidade']))
			{
				$cidade = $_POST['cidade'];
				$selecionaVendedor = $con->prepare('Select * from usuario where vendedor = 1 and cidade = :cidade order by RAND()');
				$selecionaVendedor->execute( array(':cidade' => $cidade) );
			}
			else if(isset($_POST['aplicar']))
            {
                $estado = $_POST['filter-uf'];
    		    $cidade = $_POST['filter-cidade'];
    		    $tipoArray = $_POST['tipo'];
    		    
    		    $i=0;
    		    $condicao = '';
    		    foreach ($tipoArray as $key => $value)
    		    {
    		        $i++;
    		        if($i == count($tipoArray))
    		        {
    		             $condicao .= ' v.tipo = ' . $value ;
    		        }
    		        else
    		        {
    		             $condicao .= ' v.tipo = ' .$value. ' or';
    		        }
    		 
    		    }
    	
    		    $query = 'SELECT * from usuario as u INNER JOIN vendedor as v ON u.id = v.id_usuario WHERE (' . $condicao . ') and u.vendedor = 1 and u.cidade = :cidade';
    			$selecionaVendedor = $con->prepare($query);
    			$selecionaVendedor->execute( array(':cidade' => $cidade) );

            }
			else
			{
				echo '<p class="error">Não há registros referente a pesquisa!</p>';
			}
		}
		 
        
        else
		{
			$selecionaVendedor = $con->prepare('Select * from usuario where vendedor = 1 order by RAND()');
			$selecionaVendedor->execute();
		}
	?>
<div class="box-main list-s">
	<p class="title">Os melhores estão aqui</p>
	<form method="post" class="search-serv  search-color" action="?p=petserv&s=1">
		<i class="fas fa-map-marker-alt"></i>
		<select name="uf" onchange="carregaCidades('uf', 'cidade')">
			<?php 

				$uf = $con->prepare('Select Nome, UF from estados');
				$uf->execute();

				$ufAll =  $uf->fetchAll(PDO::FETCH_ASSOC);

    
				foreach ($ufAll as $key => $value) 
				{
				    $selected = '';
				    if(isset($_POST['uf']) && $value['UF'] == $_POST['uf'])
				    {
				        $selected = 'selected';
				    }
					echo '<option ' . $selected . ' value="' . $value['UF'] . '">' . $value['Nome'] . '</option>';
				}

			?>
		</select>
		<select name="cidade">
		    <?php 
			   
    	        if(isset ($_POST['uf'] )) {
    		        $uf = $_POST['uf'];
                
                	$consultaCidade = $con->prepare('Select Id, Nome from cidades where Uf = :uf');
                			$consultaCidade->execute( array ( ':uf' => $uf ) );
                
        			$cidadeAll =  $consultaCidade->fetchAll(PDO::FETCH_ASSOC);
        
        			foreach ($cidadeAll as $key => $value) 
        			{
        			    $selected = '';
        			    if(isset($_POST['cidade']) && $value['Id'] == $_POST[cidade])
        			    {
        			        $selected = 'selected';
        			    }
        				echo '<option ' . $selected . ' value="' . $value['Id'] . '">' . $value['Nome'] . '</option>';
        			}
    	        }
			  ?>
		</select>
		<input type="submit" name="pesquisa-servidor" value="Encontrar">
	</form>

	<div class="separator"></div>
	
	 <label>Filtrar por:</label>
	 <div class="box-filter">
       <form action="" method="post" style="margin-bottom: 10px;">
           <input type="submit" name="aplicar" value="Aplicar" class="btn-pp-p">
           <br/>
            <label>Categoria:</label>
           <input type="hidden" name="filter-uf" value="<?php echo (isset ($estado)) ? $estado : ''; ?>">
           <input type="hidden" name="filter-cidade" value="<?php echo (isset ($cidade)) ? $cidade : ''; ?>">
           <input type="checkbox" name="tipo[]" value="1"> <label>Autonômo</label> 
           <input type="checkbox" name="tipo[]" value="2"> <label>Cliníca Veterinária</label>
           <input type="checkbox" name="tipo[]" value="3"> <label>Pet Shop</label>
           <input type="checkbox" name="tipo[]" value="4"> <label>Hotelaria</label>
           
       </form>
    </div>

	
	<?php

		if(isset($selecionaVendedor))
		{
			$tipos = array (
			1 => 'Autonômo',
			2 => 'Cliníca Veterinária',
			3 => 'Pet Shop',
			4 => 'Hotelaria'
		); 

			$vendedores = $selecionaVendedor->fetchAll(PDO::FETCH_ASSOC);
		

			foreach ($vendedores as $key => $vendedor) 
			{
				$selecionaDados = $con->prepare('Select * from vendedor where id_usuario = :id');
				$selecionaDados->execute(array (':id' => $vendedor['id']));
				$dados = $selecionaDados->fetch(PDO::FETCH_ASSOC);


				?>
					<div class="preview-box-profile">
						<div class="img" style="background-image: url('<?php echo  ( $vendedor['capa'] ) ? $vendedor['capa'] : '/views/imgs/capa.png';  ?>');">
						</div>
						<div class="preview-info-profile">
							
							<span class="preview-title-profile">
								<?php echo $vendedor['nome']; ?>
							</span>
								<p>
									<i class="fas fa-store-alt icon-preview"></i>
									<?php echo $tipos[$dados['tipo']]; ?>
								</p>
						
								<p>
									<i class="fas fa-map-marker-alt icon-preview"></i>
									<?php echo ucfirst(exibeCidade( $vendedor['cidade']) ) . ' - ' . ucfirst($vendedor['uf']); ?>
								</p>
							
					
				
							<a href="?p=perfil&id=<?php echo $vendedor['id']; ?>#servicos" class="btn-pp">ver serviços</a>
						</div>
					</div>
				<?php
			}

		}	
	?>
	<div class="clear"></div>
</div>