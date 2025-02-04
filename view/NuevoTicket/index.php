<?php
require_once("../../config/conexion.php"); //Conexion con DB
if (isset($_SESSION["usu_id"])) { // Si hay un usuario logueado:
?>
	<!DOCTYPE html>
	<html>
	<?php require_once("../MainHead/head.php"); ?>
	<title>Ticketera VER</>::Nuevo Ticket</title>

	<body class="with-side-menu">

		<?php require_once("../MainHeader/header.php"); ?>

		<div class="mobile-menu-left-overlay"></div>

		<?php require_once("../MainNav/nav.php"); ?>

		<div class="page-content">
			<div class="container-fluid">
				<header class="section-header">
					<div class="tbl">
						<div class="tbl-row">
							<div class="tbl-cell">
								<h3>Nuevo Ticket</h3>
								<ol class="breadcrumb breadcrumb-simple">
									<li><a href="#">Home</a></li>
									<li class="active">Nuevo Ticket</li>
								</ol>
							</div>
						</div>
					</div>
				</header>

				<div class="box-typical box-typical-padding">
					<p>
						Desde esta ventana podran generar nuevos Tickets
					</p>

					<h5 class="m-t-lg with-border">Ingresar Información</h5>

					<div class="row">
						<form method="post" id="ticket_form">
							<input type="hidden" id="usu_id" name="usu_id" value="<?php echo $_SESSION["usu_id"] ?>">
							<div class="col-lg-6">
								<fieldset class="form-group">
									<label class="form-label semibold" for=cat_id>Categoria</label>
									<select class="form-control" id="cat_id" name="cat_id">

									</select>
								</fieldset>
							</div>
							<div class="col-lg-6">
								<fieldset class="form-group">
									<label class="form-label semibold" for="tick_titulo">Titulo</label>
									<input type="text" class="form-control" id="tick_titulo" name="tick_titulo" placeholder="Ingrese Titulo">
								</fieldset>
							</div>
							<div class="col-lg-12">
								<fieldset class="form-group">
									<label class="form-label semibold" for="tick_descrip">Descripción</label>
									<div class="summernote-theme-1">
										<textarea id="tick_descrip" class="summernote" name="tick_descrip" name="name" required></textarea>
									</div>
								</fieldset>
							</div>
							<div class="col-lg-12">
								<button type="submit" name="action" value="add" class="btn btn-rounded btn-inline btn-prumary">Guardar</button>
							</div>
						</form>
					</div><!--.row-->

				</div>
			</div><!--.container-fluid-->
		</div><!--.page-content-->
		<?php require_once("../MainJs/js.php"); ?>
		<script type="text/javascript" src="nuevoticket.js"></script>
	</body>

	</html>
<?php
} else { // Si no hay usuario logueado, te dirige a la pantalla de login
	header("Location:" . Conectar::ruta() . "index.php");
}
?>