<body class="painel">
<?php require_once "php/functions.php"; ?>
<?php include "parts/sidebar-painel.php"; ?>
<?php 

$idUsuario = $_SESSION['id'];

$selecionaUsuario = $con->prepare('SELECT verificado from usuario where id = :id');
$selecionaUsuario->execute( array (':id' => $idUsuario) );
$resultado = $selecionaUsuario->fetch(PDO::FETCH_ASSOC);

if ($resultado['verificado'] == 0)
{
    if(isset ($_GET['pconf']) )
    {
        $update = $con->prepare('UPDATE usuario set verificado = 1 where id = :id');
        $result = $update->execute( array ( ':id' =>  $_GET['pconf']) );
        
        if ($result)
        {
            header('Location: http://mypetplace.online/painel.php');
        }
    }
    if( isset($_POST['enviar-email']) )
    {
        $cor = " '#02c1b1' ";
        $email = $_POST['email-verificacao'];
        $destino = $email;
        $assunto = "Confirmar E-mail";
        $mensagem = "
        <html>
            <body style='background-color: #f2f2f2;'>
                <div style='background-color: #02c1b1; padding: 20px; color: #fff; width: 80%; margin: 20px auto 0 auto;'>PETPLACE</div>
                <div style='background-color: #fff; padding: 20px; width: 80%; margin: 0 auto 20px auto;'>
                    <p style='font-size: 12px; text-align: center;'> Olá, nós da PetPlace estamos muito feliz de ter você conosco. Mas antes de tudo, precisamos confirmar que é você mesmo. <br/>
                    <a href='http://mypetplace.online/painel.php?pconf=" . $idUsuario . "'>Clique aqui</a> para confirmar seu e-mail!
                    </p>
                </div>
            </body>
        </html>

        ";
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: PetPlace <contato@mypetplace.online>' . "\r\n";
        
        mail($destino, $assunto, $mensagem, $headers);
    }
    ?>
        <div class="modal-verificacao" style="font-size: 12px;">
            <p>Precisamos da confirmação do seu e-mail.</p>
            <p>Reenviar confirmação</p>
            <form method="post" action="" >
                <input type="text" placeholder="E-mail" name="email-verificacao">
                <input type="submit" name="enviar-email" class="btn-pp-p" value="Reenviar">
            </form>
        </div>
         <script>
            $('.modal-verificacao').dialog({
                draggable: false,
                modal: true,
                title: "Por favor, confirme seu e-mail",
                open: function(event, ui) {
                  $(this).closest('.ui-dialog').find('.ui-dialog-titlebar-close').hide();
                }
            });
        </script>
    <?php
}


?>
	<?php 
		$titles = array (
			'meu-perfil' => 'Perfil',
			'servicos' => 'Meus Serviços',
			'pedidos' => 'Meus Pedidos',
			'compras' => 'Minhas Compras',
			'pets' => 'Meus Pets',
			'imagens' => 'Mídia'  
		);
	?>
	<div class="box-content">
		<?php 

			if(isset($_GET['p']))
			{			
				switch ($_GET['p']) 
				{
					case 'meu-perfil':
						echo '<div class="box-title">Meu Perfil</div>';
						break;
					case 'servicos':
						echo '<div class="box-title">Meus Serviços</div>';
						break;
					case 'pedidos':
						echo '<div class="box-title">Meus Pedidos</div>';
						break;
					case 'compras':
						echo '<div class="box-title">Minhas Compras</div>';
						break;
					case 'pets':
						echo '<div class="box-title">Meus Pets</div>';
						break;
					case 'imagens':
						echo '<div class="box-title">Mídia</div>';
						break;
					
					default:
						echo '<div class="box-title">Não Encontrado</div>';
						break;
				}
			}
		?>
		<div class="box-admin">
			<div class="clear-both"></div>

			<?php if (isset($_GET['p'])) : ?>
				<?php switch ($_GET['p']) :

					// PERFIL
					case 'meu-perfil':

						echo '<div class="box-profile">';
						include 'views/parts/perfil.php';
						echo '</div>';
						
						break;
					// FIM PERFIL

					// SERVIÇOS 
					case 'servicos':

						include 'views/parts/servicos.php';
						
						break;
					// FIM SERVIÇOS

					// PEDIDOS
					case 'pedidos':

						include 'views/parts/pedidos.php';
						
						break;

					//FIM PEDIDOS

					case 'compras':

						include 'views/parts/compras.php';

						break;

					// box pets
					case 'pets':

						include 'views/parts/pets.php';
						
						break;
					// fim box pets

					case 'imagens':

						include 'views/parts/imagens.php';

						break;
					
					default:
					?>

					<?php
						break;
				endswitch;
				?>
				<?php else: ?>
				<div class="row">
				    <h3 class="title">Bem vindo(a), <?php echo $_SESSION['nome']; ?></h3>
				</div>
				<div class="row">
				    <div class="col-lg-4">
				        <div class="box-dashboard panel-green">
				        <?php 
				            $selectNovosPedidos = $con->prepare("SELECT id from pedido where status = 2 and valido = 1 and id_prestador = :id");
				            $selectNovosPedidos->execute( array (':id' => $_SESSION['id']) );
				            $resultNovosPedidos = $selectNovosPedidos->fetchALL(PDO::FETCH_ASSOC);
				            $qtdNovosPedidos = count($resultNovosPedidos);
				            
				            
				            if($qtdNovosPedidos > 0)
				            {
				                echo "<h2 class='number-box'>" . $qtdNovosPedidos . "</h2>";
				                echo "<p class='text-box'>Pedidos em aberto</p>";
				                echo "<a class='white' href='http://mypetplace.online/painel.php?p=pedidos'>Ver pedidos</a>";
				            }
				            else
				            {
				                  echo "<p class='text-box'>Você ainda não possui vendas!</p>";
				            }
				        ?>
				        </div>
				    </div>
				    <!--<div class="col-lg-4">-->
				    <!--    <div class="box-dashboard panel-blue">-->
				    <!--        oi-->
				    <!--    </div>-->
				    <!--</div>-->
				    <!--<div class="col-lg-4">-->
				    <!--    <div class="box-dashboard panel-orange">-->
				    <!--        oi-->
				    <!--    </div>-->
				    <!--</div>-->
				</div>
				<?php endif; ?>
		</div>
		
		<?php 
		    $selectNaoAvaliados = $con->prepare('SELECT * from pedido where status = 2 and valido = 0 and avaliado = 0 and id_comprador = :id');
		    $selectNaoAvaliados->execute(array (':id' => $_SESSION['id']) );
		    $resultNaoAvaliados = $selectNaoAvaliados->fetchALL(PDO::FETCH_ASSOC);
		    
		    if($resultNaoAvaliados)
		    {
		        echo '<div class="box-admin">';
		        echo '<h4>Comente sua experência sobre os serviços prestados a você</h4>';
		        foreach ($resultNaoAvaliados as $key => $value)
		        {
		            ?>
		                <div class="box-filter">
		                    <span>
		                       <?php
		                            $selectTitulo = $con->prepare('SELECT titulo from servico where id = :id');
		                            $selectTitulo->execute( array ( ':id' => $value['id_servico'] ) );
		                            $titulo = $selectTitulo->fetch(PDO::FETCH_ASSOC);
		                            
		                            $selectPrestador = $con->prepare('SELECT nome from usuario where id = :id');
		                            $selectPrestador->execute( array ( ':id' => $value['id_prestador'] ) );
		                            $prestador = $selectPrestador->fetch(PDO::FETCH_ASSOC);
		                            echo $titulo['titulo'];
		                            echo ' - ';
		                            echo '<a href="http://mypetplace.online/petplace.php?p=perfil&id=' . $value['id_prestador'] . '#servicos">' . $prestador['nome'] . '</a>';
		                       ?>
		                    </span>
		                    <form method="post" action="">
		                        <input type="hidden" name="prestador" value="<?php echo $value['id_prestador']; ?>">
		                        <input type="hidden" name="comprador" value="<?php echo $value['id_comprador'];?> ">
		                        <input type="hidden" name="id-pedido" value="<?php echo $value['id']; ?> ">
		                        <textarea class="input-light col-lg-6" rows=3 name="comentario" placeholder="Avaliação..">
		                          </textarea>
		                          <br/>
		                        <input class="btn-pp-p" type="submit" name="avaliar" value="Avaliar" style="margin-bottom: 10px">
		                    </form>
		                </div>
		            <?php
		        }
		        echo '</div>';
		    }
		    
		    if(isset($_POST['avaliar']))
		    {
    		    $insertAvaliacao = $con->prepare('INSERT into avaliacao (comentario, id_prestador, id_comprador) values (:comentario, :prestador, :comprador)');
    		    $insertAvaliacao->execute(array (':comentario' => $_POST['comentario'], ':prestador' => $_POST['prestador'], ':comprador' => $_POST['comprador']) );
    		    
    		    $mudaStatusAvaliado = $con->prepare('UPDATE pedido set avaliado = 1 where id = :id');
    		    $mudaStatusAvaliado->execute(array (':id' => $_POST['id-pedido']) );
		    }
		    
		?>

	</div>
</body>
<!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
(function(){ var widget_id = 'mwKsGSDzQ4';var d=document;var w=window;function l(){var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script>
<!-- {/literal} END JIVOSITE CODE -->
