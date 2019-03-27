<?php
	session_start();
	if( ! $_SESSION['logado'] ):
		header("Location: petplace");
		exit();
	endif;
?>