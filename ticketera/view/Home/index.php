<?php
require_once("../../config/conexion.php"); //Conexion con DB
if(isset($_SESSION["usu_id"])){ // Si hay un usuario logueado:
?>
<!DOCTYPE html>
<html>
<?php require_once("../MainHead/head.php");?>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<link rel="stylesheet" href="../../public/css/lib/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="../../public/css/separate/pages/calendar.min.css">
<title>Ticketera VER :: Estadísticas</title>
<body class="with-side-menu">

<?php require_once("../MainHeader/header.php");?>

	<div class="mobile-menu-left-overlay"></div>
	
    <?php require_once("../MainNav/nav.php");?>

	<div class="page-content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-xl-12">
					<div class="row">
						<div class="col-sm-4">
							<article class="statistic-box green">
								<div>
									<div class="number" id="lbltotal"></div>
									<div class="caption">Total De Tickets</div>
								</div>
							</article>
						</div>
						<div class="col-sm-4">
							<article class="statistic-box yellow">
								<div>
									<div class="number" id="lbltotalabiertos"></div>
									<div class="caption">Total De Tickets Abiertos</div>
								</div>
							</article>
						</div>
						<div class="col-sm-4">
							<article class="statistic-box red">
								<div>
									<div class="number" id="lbltotalcerrados"></div>
									<div class="caption">Total De Tickets Cerrados</div>
								</div>
							</article>
						</div>
					</div>
				</div>
			</div>

			<section class="card">
				<header class="card-header">
					Grafico Estadístico
				</header>
				<div class="card-block">
					<div id="divgrafico" style="height: 250px;"></div>
				</div>
			</section>

			<section class="card">
				<header class="card-header">
					Calendario
				</header>
				<div class="card-block">
					<div id="idcalendar"></div>
				</div>
			</section>
			
		</div><!--.container-fluid-->
	</div><!--.page-content-->
    <?php require_once("../MainJs/js.php");?>
	<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
	<script type="text/javascript" src="../../public/js/lib/moment/moment-with-locales.min.js"></script>
	<script src="../../public/js/lib/fullcalendar/fullcalendar.min.js"></script>
	<script type="text/javascript" src="home.js"></script>
	<script type="text/javascript" src="../notificacion.js"></script>
</body>
</html>
<?php
} else { // Si no hay usuario logueado, te dirige a la pantalla de login
	header("Location:".Conectar::ruta()."index.php");
}
?>