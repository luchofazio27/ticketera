<?php
require_once("../../config/conexion.php"); //Conexion con DB
if (isset($_SESSION["usu_id"])) { // Si hay un usuario logueado:
?>
	<!DOCTYPE html>
	<html>
	<?php require_once("../MainHead/head.php"); ?>
	<title>Ticketera VER :: Detalle Ticket</title>

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
								<h3 id="lblnomidticket"
    style="
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 4px;
        margin-bottom: 20px;
    ">
    Detalle Ticket - 1
</h3>

<div id="lblestado"></div>

<div style="margin-top: 8px;">
    <span class="label label-pill label-primary" id="lblnomusuario"></span>
</div>

<div style="margin-top: 5px;">
    <span class="label label-pill label-info" id="lblnomcreador"></span>
</div>

<div style="margin-top: 5px;">
    <span class="label label-pill label-default" id="lblfechcrea"></span>
</div>
							</div>
						</div>
					</div>
				</header>

				<div class="box-typical box-typical-padding">
					<div class="row">

					    <div class="col-lg-12">
							<fieldset class="form-group">
								<label class="form-label semibold" for="tick_titulo">Titulo</label>
								<input type="text" class="form-control" id="tick_titulo" name="tick_titulo" readonly>
							</fieldset>
						</div>

						<div class="col-lg-4">
							<fieldset class="form-group">
								<label class="form-label semibold" for="cat_nom">Categoria</label>
								<input type="text" class="form-control" id="cat_nom" name="cat_nom" readonly>
							</fieldset>
						</div>

						<div class="col-lg-4">
							<fieldset class="form-group">
								<label class="form-label semibold" for="cat_nom">SubCategoria</label>
								<input type="text" class="form-control" id="cats_nom" name="cats_nom" readonly>
							</fieldset>
						</div>

						<div class="col-lg-4">
							<fieldset class="form-group">
								<label class="form-label semibold" for="prio_nom">Prioridad</label>
								<input type="text" class="form-control" id="prio_nom" name="prio_nom" readonly>
							</fieldset>
						</div>


						<div class="col-lg-12">
							<fieldset class="form-group">
								<label class="form-label semibold" for=tick_titulo>Documentos Adicionales</label>
								<table  id="documentos_data" class="table table-borderer table-striped teble_vcenter js-dataTable-full">
									<thead>
										<tr>
											<th style="width: 90%;">Nombre</th>
											<th class="text-center" style="width: 10%;"></th>
										</tr>
									</thead>
									<tbody>

									</tbody>
								</table>
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
						Ingrese respuesta, duda o consulta
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
							<fieldset class="form-group">
								<label class="form-label semibold" for="fileElem">Documentos Adicionales</label>
								<input type="file" name="fileElem" id="fileElem" class="form-control" multiple>
								<!-- Vista previa de archivos seleccionados -->
<div id="previewFiles" style="margin-top:10px;"></div>

							</fieldset>
						</div>

						<div class="col-lg-12">
							<button type="button" id="btnenviar" class="btn btn-rounded btn-inline btn-primary">Responder</button>
							<?php if ($_SESSION["rol_id"] == 5 || $_SESSION["rol_id"] == 2) { ?>
  <button type="button" id="btnasignar_detalle" class="btn btn-rounded btn-inline btn-success">Asignar Agente</button>
<?php } ?>

							<button type="button" id="btncerrarticket" class="btn btn-rounded btn-inline btn-warning" style="display: none;">Cerrar Ticket</button>
							<button type="button" id="btncomentar_cerrar" class="btn btn-rounded btn-inline btn-danger">Comentar y Cerrar Ticket</button>
						</div>

					</div><!--.row-->

				</div>
<!-- BOTÓN REABRIR TICKET -->
<div class="box-typical box-typical-padding" id="pnlreabrir" style="display: none; text-align: center;">

    <button type="button" id="btnreabrirticket" class="btn btn-rounded btn-inline btn-warning">
        <i class="fa fa-unlock"></i> Reabrir Ticket
    </button>

</div>
			</div><!--.container-fluid-->
		</div><!--.page-content-->
		<?php require_once("../ConsultarTicket/modalasignar.php"); ?>
		<?php require_once("../MainJs/js.php"); ?>
		<script type="text/javascript" src="detalleticket.js"></script>
		<script type="text/javascript" src="../notificacion.js"></script>
	</body>

	</html>
<?php
} else { // Si no hay usuario logueado, te dirige a la pantalla de login
	header("Location:" . Conectar::ruta() . "index.php");
}
?>