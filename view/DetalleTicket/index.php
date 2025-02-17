<?php
require_once("../../config/conexion.php"); //Conexion con DB
if (isset($_SESSION["usu_id"])) { // Si hay un usuario logueado:
?>
	<!DOCTYPE html>
	<html>
	<?php require_once("../MainHead/head.php"); ?>
	<title>Ticketera VER</>::Detalle Ticket</title>

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
								<h3 id="lblnomidticket">Detalle Ticket - 1</h3>
								<div id="lblestado"></div>
								<span class="label label-pill label-primary" id="lblnomusuario"></span>
								<span class="label label-pill label-default" id="lblfechcrea"></span>
								<ol class="breadcrumb breadcrumb-simple">
									<li><a href="#">Home</a></li>
									<li class="active">Detalle Ticket</li>
								</ol>
							</div>
						</div>
					</div>
				</header>

				<div class="box-typical box-typical-padding">
					<div class="row">

						<div class="col-lg-6">
							<fieldset class="form-group">
								<label class="form-label semibold" for=cat_id>Categoria</label>
								<input type="text" class="form-control" id="cat_nom" name="cat_nom" readonly>

							</fieldset>
						</div>

						<div class="col-lg-6">
							<fieldset class="form-group">
								<label class="form-label semibold" for=cat_id>Titulo</label>
								<input type="text" class="form-control" id="tick_titulo" name="tick_titulo" readonly>

							</fieldset>
						</div>

						<div class="col-lg-12">
							<fieldset class="form-group">
								<label class="form-label semibold" for=tickd_descripusu>Descripción</label>
								<div class="summernote-theme-1">
									<textarea id="tickd_descripusu" class="summernote" name=tickd_descripusu name="name" required></textarea>
								</div>
								<input type="text" class="form-control" id="cat_nom" name="cat_nom" readonly>

							</fieldset>
						</div>

						</form>
					</div><!--.row-->

				</div>

				<section class="activity-line" id="lbldetalle">



				</section><!--.activity-line-->


				<div class="box-typical box-typical-padding" id="pnldetalle">

					<p>
						Ingrese duda o consulta
					</p>

					<div class="row">

						<div class="col-lg-12">
							<fieldset class="form-group">
								<label class="form-label semibold" for="tickd_descrip">Descripción</label>
								<div class="summernote-theme-1">
									<textarea id="tickd_descrip" class="summernote" name="tickd_descrip" name="name"></textarea>
								</div>
							</fieldset>
						</div>
						<div class="col-lg-12">
							<button type="button" id="btnenviar" class="btn btn-rounded btn-inline btn-primary">Enviar</button>
							<button type="button" id="btncerrarticket" class="btn btn-rounded btn-inline btn-warning">Cerrar Ticket</button>
						</div>

					</div><!--.row-->

				</div>

			</div><!--.container-fluid-->
		</div><!--.page-content-->
		<?php require_once("../MainJs/js.php"); ?>
		<script type="text/javascript" src="detalleticket.js"></script>
	</body>

	</html>
<?php
} else { // Si no hay usuario logueado, te dirige a la pantalla de login
	header("Location:" . Conectar::ruta() . "index.php");
}
?>