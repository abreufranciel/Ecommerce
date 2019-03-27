<header class="color-back">	
<div class="box-logo">
	<a href="/">
		<img src="views/imgs/logo.png">
	</a>
</div>
<i class="fas fa-bars white bar-menu-mobile"></i>
<div class="logout">
	<a href="logout.php">Sair</a>
</div>
</header>
<div class="menu-mobile">
	<div class="info-user">
		<?php 
			if(isset($_POST['salva-foto']))
			{

				$imagemUsuario = ( $_FILES['imagem-usuario']['error'] != 4 ) ? $_FILES['imagem-usuario'] : false;

				if($imagemUsuario)
				{			
					$extensoes = array ('.jpg', '.png');
					$extensao = strrchr($_FILES['imagem-usuario']['name'], '.');

					//verifica se é png ou jpg
					if(in_array($extensao, $extensoes) === true)
					{
						$uploaddir = 'upload/perfil/';
						$uploadfile = $uploaddir . 'user-' . $_SESSION['id'] . rand(0, 99999) . $_FILES['imagem-usuario']['name'];

						if (move_uploaded_file($_FILES['imagem-usuario']['tmp_name'], $uploadfile))
						{
							
							if(!empty($_SESSION['imagem']))
							{
								unlink($_SESSION['imagem']);
							}

							$mudaFoto = $con->prepare("Update usuario set imagem = :imagem where id = :id_usuario ");
							$resultFoto = $mudaFoto->execute(array ( ':imagem' => $uploadfile, ':id_usuario' => $_SESSION['id'] ));
							$_SESSION['imagem'] = $uploadfile;

						}
					}
					else
					{
						echo '<div class="error-pp"><p>Somente arquivos: jpg ou png.</p></div>';
					}
				}
			}
		?>
		<div class="photo-user">
			<?php if ( empty($_SESSION['imagem']) ) : ?>
				<img title="Alterar Foto" src="views/imgs/user.png">
			<?php else : ?>
				<img title="Alterar Foto" src="<?php echo $_SESSION['imagem']; ?>">
			<?php endif; ?>
		</div>
		<form method="post" enctype="multipart/form-data" action="" id="upload-foto">
			<input type="file" name="imagem-usuario" value="Alterar" id="envia-foto-usuario" class="hide" onchange="submeter('#salva-foto');">
			<input type="submit" name="salva-foto" class="hide" id="salva-foto">
		</form>
		<p class="name-user"><?php echo 'Olá, ' . ucfirst( current( str_word_count( $_SESSION['nome'], 2 ) ) ); ?></p>
	</div>
	<nav>
		<?php $p = (isset($_GET['p'])) ? true : false; ?>
		<ul class="menu">
		    <li <?php if( !isset($p)) echo 'class="active"'; ?> >
				<div>
					<i class="fas fa-home"></i>
					<a href="painel.php">Início</a>
				</div>
			</li>
			<li <?php if( $p && $_GET['p'] == 'meu-perfil' ) echo 'class="active"'; ?> >
				<div>
					<i class="fas fa-user"></i>
					<a href="?p=meu-perfil">Meu Perfil</a>
				</div>
			</li>
			<li <?php if( $p && $_GET['p'] == 'servicos' ) echo 'class="active"'; ?> >
				<div>
					<i class="fas fa-hand-holding-heart"></i>
					<a href="?p=servicos">Meus Serviços</a>
				</div>
			</li>
			<li <?php if( $p && $_GET['p'] == 'pedidos' ) echo 'class="active"'; ?> >
				<div>
					<i class="fas fa-clipboard-list"></i>
					<a href="?p=pedidos">Meus Pedidos</a>
				</div>
			</li>
			<li <?php if( $p && $_GET['p'] == 'compras' ) echo 'class="active"'; ?> >
				<div>
					<i class="fas fa-shopping-basket"></i>
					<a href="?p=compras">Minhas Compras</a>
				</div>
			</li>
			<li  <?php if( $p && $_GET['p'] == 'imagens' ) echo 'class="active"'; ?> >
				<div>
					<i class="fas fa-images"></i>
					<a href="?p=imagens">Imagens</a>
				</div>
			</li>
			<li>
				<div>
					<i class="fas fa-sign-out-alt"></i>
					<a href="logout.php">Sair</a>
				</div>
			</li>
		</ul>
	</nav>
</div>