<?php
require_once("../../config/conexion.php"); //Conexion con DB
require_once("../../helpers/seguridad.php"); //Llama al archivo seguridad.php para que se ejecute una sol vez

SeguridadHelper::verificarAcceso([2]); // Solo admin (rol 2) pueden entrar
if(isset($_SESSION["usu_id"])){ // Si hay un usuario logueado:
?>
<!DOCTYPE html>
<html>
<?php require_once("../MainHead/head.php");?>
<title>Ticketera VER :: Mantenimiento Categoria</title>
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
								<h3 style="
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 4px;
    ">Mantenimiento Categoria</h3>
							</div>
						</div>
					</div>
				</header>

				<div class="box-typical box-typical-padding">
					<button type="button" id="btnnuevo" class="btn btn-inline btn-primary">Nuevo Registro</button>
					<table id="usuario_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
						<thead>
							<tr>
								<th style="width: 10%;">Nombre</th>
								<th style="width: 10%;">Sector</th>
								<th style="width: 5%;">Editar</th>
								<th style="width: 5%;">Borrar</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
		</div><!--.container-fluid-->
	</div><!--.page-content-->
	<?php require_once("modalmantenimiento.php");?>
    <?php require_once("../MainJs/js.php");?>
	<script type="text/javascript" src="mntcategoria.js"></script>
	<script type="text/javascript" src="../notificacion.js"></script>
</body>
</html>
<?php
} else { // Si no hay usuario logueado, te dirige a la pantalla de login
	header("Location:".Conectar::ruta()."index.php");
}
?>