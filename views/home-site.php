<body class="container-fluid">
    <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>

<?php include "parts/header-site.php"; ?>


<?php 

$pagina = (isset($_GET['p'])) ? $_GET['p'] : 0;

if( $pagina ) :

	switch ($pagina) 
	{
		case 'login':
			include "forms/form-login.php";
			break;

		case 'petserv':
			include "parts/lista-prestadores.php";
			break;

		case 'perfil':
			include "parts/prestador.php";
			break;	
			
	    case 'contato':
		    include "parts/contato.php";
		    break;
		    
		case 'politicas-e-termos':
		    include "parts/politicas-e-termos.php";
		    break;
		
		default:
			# code...
			break;
	} 
?>

<?php else : ?>

	<div id="banner">
		<p class="text-home">
			Encontre os melhores serviços para o seu pet
		</p>
		<form method="post" class="search-serv" action="?p=petserv&s=1">
			<i class="fas fa-map-marker-alt"></i>
			<select name="uf" onchange="carregaCidades('uf', 'cidade')">
				<?php 

					$uf = $con->prepare('Select Nome, UF from estados');
					$uf->execute();

					$ufAll =  $uf->fetchAll(PDO::FETCH_ASSOC);

					foreach ($ufAll as $key => $value) 
					{
						echo '<option value="' . $value['UF'] . '">' . $value['Nome'] . '</option>';
					}

				?>
			</select>
			<select name="cidade">
			</select>
			<input type="submit" name="pesquisa-servidor" value="Encontrar">
		</form>
	</div>
	<div id="box-main">
		<div class="box-three">
			<img src="views/imgs/fig1.png">
			<p class="subtitle">Nós acreditamos que o melhor amigo é o nosso petzinho</p>
			<p class="description">
				Bora saber nos detalhes o que é a PetPlace, e tudo que ela pode oferecer de melhor para você e o seu melhor amigo.
				Nós estamos de braços e coração abertos!
			</p>
			<a class="btn-pp" href="">SAIBA MAIS</a>
		</div>
		<div class="box-three">
			<img src="views/imgs/fig2.png">
			<p class="subtitle">Nós apoiamos aqueles que amam e acolhem</p>
			<p class="description">
				A PetPlace por amar e respeitar os animais, e apoiar todas as causas de proteção a eles, conta com um espaço totalmente dedicado a ONG'S de proteção.
			</p>
			<a class="btn-pp" href="">SAIBA MAIS</a>
		</div>
		<div class="box-three">
			<img src="views/imgs/fig3.png">
			<p class="subtitle">Os melhores serviços por preços melhores ainda</p>
			<p class="description">
				Nós somos compromissados com você e seu pet. 
				<br>
				Faça uma busca com o seu endereço para encontrar prestadores prontos para melhor atende-lo. 
			</p>
			<a class="btn-pp" href="?p=petserv">SAIBA MAIS</a>
		</div>
	</div>
	<div>
		<img style="margin-top: 5%;" src="views/imgs/home.png">
		<img style="margin-bottom: -4px;" src="views/imgs/home-2.png">
	</div>
<?php endif; ?>

<?php include "parts/footer-site.php"; ?>
</body>
