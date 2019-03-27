<div class="box-main" style="background-color: #f2f2f2;">
	<?php 
		$id = strip_tags($_GET['id']);
		$tiposVendedor = array (
			1 => 'Autonômo',
			2 => 'Cliníca Veterinária',
			3 => 'Pet Shop'
		); 
		$selecionaVendedor = $con->prepare('Select * from usuario where id = :id');
		$selecionaVendedor->execute( array (':id' => $id) );
		$dadosUsuario = $selecionaVendedor->fetch(PDO::FETCH_ASSOC);

		$selecionaDados = $con->prepare('Select * from vendedor where id_usuario = :id');
		$selecionaDados->execute( array (':id' => $dadosUsuario['id']) );
		$dadosVendedor = $selecionaDados->fetch(PDO::FETCH_ASSOC);

	?>
	<div class="header-profile" style="background-image: url(<?php echo  ( $dadosUsuario['capa'] ) ? $dadosUsuario['capa'] : '/views/imgs/capa.png'; ?>);">
		<a href=""></a>
	</div>
	<div class="sidebar-profile">
		<div class="photo-profile">
			<img src="<?php echo ( $dadosUsuario['imagem'] ) ? $dadosUsuario['imagem'] : '/views/imgs/no-image.png'; ?>">
		</div>
		<div class="info-profile">
			<p>
				<i class="fas fa-store-alt"></i>
				<?php echo $tiposVendedor[ $dadosVendedor['tipo'] ]; ?>
			</p>
			<?php if($dadosVendedor['hora_inicio'] != "" || $dadosVendedor['hora_fim']) : ?>
		        
				<p>
				    <i class="fas fa-clock"></i>
					<?php echo ($dadosVendedor['hora_inicio'] != "") ? $dadosVendedor['hora_inicio'] : ''; ?>
					<?php echo ($dadosVendedor['hora_fim'] != "") ? ' - ' . $dadosVendedor['hora_fim'] : ''; ?>
					
				</p>
			<?php endif; ?>
			<p>
				<i class="fas fa-map-marker-alt"></i>
				<?php echo ucfirst($dadosUsuario['bairro'])  . ' - ' . ucfirst( exibeCidade($dadosUsuario['cidade']) ) . ', ' . $dadosUsuario['uf']; ?>
					<br/>
				<?php echo '<span style="margin-left: 25px;">' . ucfirst($dadosUsuario['rua']) . ', ' . $dadosUsuario['numero'] . '</span>'; ?>
			</p>
		
			<?php if($dadosVendedor['whatsapp'] != "") : ?>
				<p>
					<i class="fab fa-whatsapp" style="font-weight: bold;"></i>
					<?php echo $dadosVendedor['whatsapp']; ?>
				</p>
			<?php endif; ?>

			<?php if($dadosVendedor['facebook'] != "") : ?>
				<p>
					<i class="fab fa-facebook-square"></i>
					<a target="_blank" href="https://www.facebook.com/<?php echo $dadosVendedor['facebook']; ?>">Facebook</a>
				</p>
			<?php endif; ?>

			<?php if($dadosVendedor['instagram'] != "") : ?>
				<p>
					<i class="fab fa-instagram"></i>
					<a target="_blank" href="https://www.instagram.com/<?php echo $dadosVendedor['instagram']; ?>">Instagram</a>
				</p>
			<?php endif; ?>
		</div>
	</div>

	<div class="content-side">
		<ul class="abas">
			<li class="tab active-tab">	
				<a href="#servicos">Serviços</a>
			</li>
			<li class="tab">	
				<a href="#galeria">Galeria</a>
			</li>
			<li class="tab">	
				<a href="#mapa">Mapa</a>
			</li>
			<li class="tab">	
				<a href="#avaliacoes">Avaliações</a>
			</li>
		</ul>
		<div class="items">
			<div id="servicos" class="services box-item">
				<?php 

					$consultaServicos = $con->prepare("Select * from servico where id_prest_serv = :id and publicado = 'on' ");
					$consultaServicos->execute( array (':id' => $id) );
					$servicos = $consultaServicos->fetchAll(PDO::FETCH_ASSOC);

					foreach ($servicos as $key => $servico) 
					{
						?>

							<ul class="list-services">
								<li>
									<span class="title-service"><?php echo $servico['titulo']; ?></span>
									<div class="align-right">
										<button class="btn-pp-p btn-see" data-title="<?php echo $servico['titulo']; ?>" data-id="<?php echo $servico['id']; ?>">Ver Serviço</button>
									</div>
									<div id="box-service-<?php echo $servico['id']; ?>" class="content-service" data-id="<?php echo $servico['id']; ?>">
										<div class="description-service">
											<?php echo $servico['descricao']; ?>
										</div>
                                    	<div class="fb-share-button" data-href="http://mypetplace.online" data-layout="button"> </div>
										<?php if ( $servico['tipo_valor'] == 0 ) : ?>
											<?php $valor = ''; ?>
											<span class="obs">*Valor variável, informe o kg do seu pet.</span>
											<div class="calculator">
												<form action="" method="post">
													<select name="kg-cachorro" class="input-light-line">
														<option value="0">até 5kg</option>
														<option value="1">5 a 10kg</option>
														<option value="2">10 a 20kg</option>
														<option value="3">20 a 40kg</option>
														<option value="4">+ 40kg</option>
													</select>
													<input type="hidden" name="id-servico-c" value="<?php echo $servico['id']; ?>">
													<input data-id="<?php echo $servico['id']; ?>" type="submit" name="consulta-preco" value="Consultar" class="btn-pp-p consulta-preco">
												</form>
											</div>

										<?php endif; ?>
										<div class="price-service">
											<?php if( $servico['tipo_valor'] == 1 ) : ?>
												<?php 
													echo 'R$ ' . number_format($servico['valor'], 2, ',', '.');

													$valor = $servico['valor']; 
												?>
											<?php endif; ?>

										</div>
										<div class="align-right">

											<?php $elemento = 'payment-service-' . $servico['id']; ?>
											<button class="btn-pp-p btn-buy" onclick="adquirir('#<?php echo $elemento; ?>');">Adquirir</button>

											<!-- resumo da compra -->
											<div class="modal-payment" id="<?php echo $elemento; ?>">
												<table>
													<th colspan="2" align="left">Resumo</th>
													<tr>
														<td>Serviço:</td>
														<td>
															<?php echo $servico['titulo']; ?>
														</td>
													</tr>
													<tr>
														<td>Valor:</td>
														<td class="service-td-id-<?php echo $servico['id']; ?>">
															<!-- via javascript -->

															<?php if( $servico['tipo_valor'] == 1 ) : ?>
																<?php 
																	echo 'R$ ' . number_format($servico['valor'], 2, ',', '.');

																	$valor = $servico['valor']; 
																?>
															<?php endif; ?>
														</td>
													</tr>
												</table>

												<p style="font-size: 11pt; margin-bottom: 0;">
													<b>Adquirir Via</b>
												</p>
												<form id="formPagseguro-<?php echo $servico['id']; ?>" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" onsubmit="PagSeguroLightbox(this); return false;">
													<input type="hidden" name="code" id="code-<?php echo $servico['id']; ?>" value=""/>
												</form>
												<?php 

													echo '<button  class="btn-pp-p btn-buy no-btn" onclick="enviaPagseguro(this);" data-id-servico="' . $servico['id'] . '" data-titulo-servico="' . $servico['titulo'] . '" data-valor-servico="' . $valor . '" data-id-prestador="' . $servico['id_prest_serv'] . '"  style="display: inline-block; vertical-align: middle;">';
													echo '<img title="PagSeguro" width="100px" src="./views/imgs/pagseguro.png">';
													echo '</button>';


												?>
												<form method="post" target="_blank" action="boleto.php" name="gera-boleto" style="display: inline-block; vertical-align: middle;">

													<input type="hidden" name="valor-servico" value="<?php echo $valor; ?>" id="valor-servico-<?php echo $servico['id']; ?>">
													<input type="hidden" name="id-comprador" value="<?php echo $_SESSION['id']; ?>">
													<input type="hidden" name="id-prestador" value="<?php echo $servico['id_prest_serv']; ?>">
													<input type="hidden" name="id-servico" value="<?php echo $servico['id']; ?>">
													<button class="btn-pp-p btn-buy no-btn" name="adquirir"><img title="Boleto" width="50px" src="./views/imgs/boleto.png"></button>
												</form>
											</div>
											<!-- fim resumo da compra -->
						
										</div>
									</div>
									<div class="clear"></div>
								</li>
							</ul>
						<?php
					}

				?>
			</div>
			<div id="galeria" class="box-item hide-box">
				<?php 
					$selecionaGaleria = $con->prepare("Select imagem from galeria where id_usuario = :id");
					$selecionaGaleria->execute( array (':id' => $id) );
					$galeria = $selecionaGaleria->fetch(PDO::FETCH_ASSOC);

					if($galeria)
					{
						$fotos = unserialize($galeria['imagem']);
					}

					if(isset($fotos))
					{
					    echo '<div class="row">';
						foreach ($fotos as $key => $foto) 
						{
							echo '<div class="col-lg-2" style="margin-bottom: 10px;">'; 
							echo '<a data-fancybox="gallery" href="/'. $foto .'"><img src="/'. $foto .'"></a>';
							echo '</div>';
						}
						echo '</div>';
					}
				?>
			</div>
			<div id="mapa" class="box-item hide-box">
				<?php 
					if(!empty($dadosVendedor['maps'])) :

				?>

				<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3686.56425662843!2d-44.47503268504207!3d-22.483003285228957!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9dd6fae12fc06f%3A0x111333209fd065b6!2sAssocia%C3%A7%C3%A3o+Educacional+Dom+Bosco+%E2%80%A2+AEDB!5e0!3m2!1spt-BR!2sbr!4v1537835668325" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>

				<?php 
					else :

						echo 'Lamentamos, mas não há localização disponível :(';

					endif;
				?>
			</div>
			<div id="avaliacoes" class="box-item hide-box">
				<?php 
				    $consultaAvaliacoes = $con->prepare("Select * from avaliacao where id_prestador = :id ");
					$consultaAvaliacoes->execute( array (':id' => $id) );
					$avaliacoes = $consultaAvaliacoes->fetchAll(PDO::FETCH_ASSOC);
					
					foreach ($avaliacoes as $key => $avaliacao)
					{
					    $consultaAvaliador = $con->prepare("Select * from usuario where id = :id ");
    					$consultaAvaliador->execute( array (':id' => $avaliacao['id_comprador']) );
    					$avaliador = $consultaAvaliador->fetch(PDO::FETCH_ASSOC);
    					
    					?>
    					
    					<div class="box-avaliacao col-lg-12">
        					<img class="col-lg-2 col-3" src="<?php echo $avaliador['imagem']; ?>">
        					<span><b><?php echo $avaliador['nome']; ?>:</b></span>
        					<br/>	<br/>
    					    <p class="offset-1">"<?php echo $avaliacao['comentario']; ?>"</p>
					    </div>
    					
    					<?php
    					
					}
				?>
			</div>
		</div>
	</div>
</div>