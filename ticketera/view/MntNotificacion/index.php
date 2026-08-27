<?php
require_once("../../config/conexion.php"); //Conexion con DB
if(isset($_SESSION["usu_id"])){ // Si hay un usuario logueado:
?>
<!DOCTYPE html>
<html>
<?php require_once("../MainHead/head.php");?>
<title>Ticketera VER :: Notificación</title>
<body class="with-side-menu">

<?php require_once("../MainHeader/header.php");?>

	<div class="mobile-menu-left-overlay"></div>
	
    <?php require_once("../MainNav/nav.php");?>

	<div class="page-content">
		<div class="container-fluid">
		<header class="section-header">
					<div class="tbl">
						<div class="tbl-row">
							<div class="tbl-cell">
								<h3>Notificacion</h3>
								<ol class="breadcrumb breadcrumb-simple">
									<li><a href="#">Home</a></li>
									<li class="active">Notificacion</li>
								</ol>
							</div>
						</div>
					</div>
				</header>

				<div class="box-typical box-typical-padding">
					<table id="notificacion_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
    <thead>
        <tr>
            <th style="width: 75%;">Notificacion</th>
            <th class="text-center" style="width: 5%;">Acciones</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

				</div>
		</div><!--.container-fluid-->
	</div><!--.page-content-->
    <?php require_once("../MainJs/js.php");?>
	<script type="text/javascript" src="mntnotificacion.js"></script>
	<script type="text/javascript" src="../notificacion.js"></script>
</body>
</html>
<?php
} else { // Si no hay usuario logueado, te dirige a la pantalla de login
	header("Location:".Conectar::ruta()."index.php");
}
?>