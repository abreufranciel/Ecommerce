<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html>
<head>
	<?php require_once "views/parts/head.php"; ?>
	<title>Início</title>
</head>

<?php if(!isset($_GET['v'])) : ?>
	
	<?php require_once 'views/home-site.php'; ?>

<?php elseif(isset($_GET['v']) && $_GET['v'] == 'ongs-amigas'): ?>

	<?php include_once 'views/ongs-amigas.php';  ?>

<?php endif; ?>

</html>

