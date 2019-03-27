<header>
	<div class="box-logo">
		<a href="/">
			<img src="views/imgs/logo.png">
		</a>
	</div>
	<?php session_start(); ?>
	<?php if ( isset($_SESSION['logado']) && $_SESSION['logado']) : ?>

		<div class="user-logado">			
			<a href="painel.php">Olá, <?php echo ucfirst( current( str_word_count( $_SESSION['nome'], 2 ) ) ); ?>! </a>	
			<span class="color-white">|</span>
			<a href="logout.php">Sair</a>
		</div>

	<?php else: ?>

		<div class="link-login">
			<a href="?p=login" id="a-login">ENTRAR</a>
			<span class="color-white">|</span>
			<a href="?p=login" id="a-register">CADASTRAR</a>
		</div>

	<?php endif; ?>
	
</header>