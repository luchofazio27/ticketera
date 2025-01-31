<?php
require_once("../../config/conexion.php"); //Conexion con DB
if(isset($_SESSION["usu_id"])){ // Si hay un usuario logueado:
?>
<!DOCTYPE html>
<html>
<?php require_once("../MainHead/head.php");?>
<title>Ticketera VER</>::Home</title>
<body class="with-side-menu">

<?php require_once("../MainHeader/header.php");?>

	<div class="mobile-menu-left-overlay"></div>
	
    <?php require_once("../MainNav/nav.php");?>

	<div class="page-content">
		<div class="container-fluid">
			Blank page.
		</div><!--.container-fluid-->
	</div><!--.page-content-->
    <?php require_once("../MainJs/js.php");?>
	<script type="text/javascript" src="home.js"></script>
</body>
</html>
<?php
} else { // Si no hay usuario logueado, te dirige a la pantalla de login
	header("Location:".Conectar::ruta()."index.php");
}
?>