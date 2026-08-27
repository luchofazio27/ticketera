<?php
require_once("../../config/conexion.php"); //Conexion con DB
if (isset($_SESSION["usu_id"])) { // Si hay un usuario logueado:
?>
	<!DOCTYPE html>
	<html>
	<?php require_once("../MainHead/head.php"); ?>
		<style>
    /* Hace que la tabla sea scrolleable horizontalmente en pantallas chicas */
    #table {
        overflow-x: auto !important;
        width: 100%;
    }

    #ticket_data {
        width: 100% !important;
        table-layout: auto !important;
    }

    /* Evitar que los textos largos rompan el layout */
    #ticket_data td,
    #ticket_data th {
        white-space: nowrap !important;
        text-overflow: ellipsis;
        overflow: hidden;
        max-width: 180px;
    }

    /* Ajuste específico para pantallas ≤ 768px */
    @media (max-width: 768px) {
        #table {
            overflow-x: scroll !important;
            -webkit-overflow-scrolling: touch;
        }
    }
</style>
	<title>Ticketera VER :: Consultar Ticket</title>

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
								<h3 style="
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 4px;
    ">Historial de Tickets</h3>
							</div>
						</div>
					</div>
<br>
                    <p>
<span class="glyphicon glyphicon-info-sign" style="color:#3498db;"></span>
En esta página puedes consultar el historial de tus tickets.<br>
<b>IMPORTANTE:</b> En la columna <b>"Estado"</b>, al presionar el botón rojo <b>"Cerrado"</b> podrás reabrir el ticket.
</p>
				</header>

				<div class="box-typical box-typical-padding">

					<div class="row" id="viewuser">
    <div class="col-lg-2 col-md-2 col-sm-6">
        <fieldset class="form-group">
            <label class="form-label" for="tick_titulo">Titulo</label>
            <input type="text" id="tick_titulo" name="tick_titulo" class="form-control" placeholder="Ingrese Referencia" required>
        </fieldset>
    </div>

    <div class="col-lg-2 col-md-2 col-sm-6">
        <fieldset class="form-group">
            <label class="form-label" for="cat_id">Categoria</label>
            <select class="select2 form-control" name="cat_id" id="cat_id" data-placeholder="Seleccionar"></select>
        </fieldset>
    </div>

    <div class="col-lg-2 col-md-2 col-sm-6">
        <fieldset class="form-group">
            <label class="form-label" for="prio_id">Prioridad</label>
            <select class="select2 form-control" name="prio_id" id="prio_id" data-placeholder="Seleccionar"></select>
        </fieldset>
    </div>

    <div class="col-lg-2 col-md-2 col-sm-6">
        <fieldset class="form-group">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-rounded btn-primary btn-block" id="btnfiltrar">Filtrar</button>
        </fieldset>
    </div>

    <div class="col-lg-2 col-md-2 col-sm-6">
        <fieldset class="form-group">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-rounded btn-primary btn-block" id="btntodo">Ver Todo</button>
        </fieldset>
    </div>

    <div class="col-lg-2 col-md-2 col-sm-6">
        <fieldset class="form-group">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-rounded btn-primary btn-block" id="btnmistickets">Mis Tickets</button>
        </fieldset>
    </div>
</div>


					<div class="box-typical box-typical-padding" id="table">
						<table id="ticket_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
							<thead>
								<tr>
									<th style="width: 5%;">Ticket</th>
									<th class="d-none d-sm-table-cell" style="width: 15%;">Solicitante</th>
									<th class="d-none d-sm-table-cell" style="width: 5%;">Sector</th>
									<th style="width: 5%;">Categoria</th>
									<th class="d-none d-sm-table-cell" style="width: 15%;">Titulo</th>
									<th class="d-none d-sm-table-cell" style="width: 2%;">Prioridad</th>
									<th class="d-none d-sm-table-cell" style="width: 2%;">Estado<br>¿Reabrir?</th>
									<th class="d-none d-sm-table-cell" style="width: 7%;">Creación</th>
									<th class="d-none d-sm-table-cell" style="width: 7%;">Cierre</th>
									<th class="d-none d-sm-table-cell" style="width: 7%;">Soporte</th>
									<th class="text-center" style="width: 1%;"></th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>

				</div>
			</div><!--.container-fluid-->
		</div><!--.page-content-->
		
		<?php require_once("../MainJs/js.php"); ?>
		<script type="text/javascript" src="historialticket.js"></script>
		<script type="text/javascript" src="../notificacion.js"></script>
	</body>

	</html>
<?php
} else { // Si no hay usuario logueado, te dirige a la pantalla de login
	header("Location:" . Conectar::ruta() . "index.php");
}
?>