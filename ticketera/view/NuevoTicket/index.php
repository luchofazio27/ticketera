<?php
require_once("../../config/conexion.php"); //Conexion con DB
if (isset($_SESSION["usu_id"])) { // Si hay un usuario logueado:
?>
	<!DOCTYPE html>
	<html>
	<?php require_once("../MainHead/head.php"); ?>
	<title>Ticketera VER :: Nuevo Ticket</title>

<style>
/* SWITCH MODERNO */
.switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 26px;
    margin: 0;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.3s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
}

.slider.round {
    border-radius: 26px;
}

.slider.round:before {
    border-radius: 50%;
}

.switch input:checked + .slider {
    background-color: #3498db;
}

.switch input:checked + .slider:before {
    transform: translateX(22px);
}

.switch input:focus + .slider {
    box-shadow: 0 0 1px #3498db;
}
</style>

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
    ">Nuevo Ticket</h3>
							</div>
						</div>
					</div>
				</header>

				<div class="box-typical box-typical-padding">
					<p>
<span class="glyphicon glyphicon-info-sign" style="color:#3498db;"></span>
Desde esta página podrás generar nuevos tickets.<br>
<b>IMPORTANTE:</b> Los campos marcados con (*) son obligatorios.
</p>

					<h5 class="m-t-lg with-border">Ingresar Información</h5>

					<div class="row">
						<form method="post" id="ticket_form">
							<input type="hidden" id="usu_id" name="usu_id" value="<?php echo $_SESSION["usu_id"] ?>">

<?php if ($_SESSION["rol_id"] == 2 || $_SESSION["rol_id"] == 4 || $_SESSION["rol_id"] == 5) { ?>

    <!-- SWITCH: CREAR TICKET PARA OTRO USUARIO -->
    <div class="col-lg-12">

        <div class="form-group">

            <label class="form-label semibold">
                ¿Crear ticket para otro usuario?
            </label>

            <div style="display:flex; align-items:center; gap:10px;">

                <label class="switch">
                    <input type="checkbox" id="switch_otro_usuario">
                    <span class="slider round"></span>
                </label>

                <span id="texto_switch_usuario" style="font-weight:600;">
                    No
                </span>

            </div>

        </div>

    </div>


    <!-- SELECTOR DE USUARIO -->
    <div class="col-lg-12" id="contenedor_usuario_solicitante" style="display:none;">

        <fieldset class="form-group">

            <label class="form-label semibold" for="usu_id_solicitante">
                Crear ticket a nombre de
            </label>

            <select
    class="form-control"
    id="usu_id_solicitante"
    name="usu_id_solicitante"
    style="width: 100%;"
>
    <option value="">Seleccionar usuario...</option>
</select>

            <small class="text-muted">
                Seleccione el usuario que solicitó el soporte.
            </small>

        </fieldset>

    </div>

<!-- SWITCH: RESOLVER TICKET EN EL MOMENTO -->
<div class="col-lg-12" id="resolver-ticket-wrap" style="display:none; margin-top:15px;">

    <div class="form-group">

        <label class="form-label semibold" style="display:block; margin-bottom:8px;">
            ¿Resolver ticket en el momento?
        </label>

        <div style="display:flex; align-items:center; gap:10px;">

            <label class="switch">
                <input 
                    type="checkbox" 
                    id="resolver_ticket" 
                    name="resolver_ticket" 
                    value="1"
                >
                <span class="slider round"></span>
            </label>

            <span id="texto_resolver_ticket" style="font-weight:600;">
                No
            </span>

        </div>

    </div>

</div>

<!-- MENSAJE EXPLICATIVO -->
<div class="col-lg-12" id="resolver-ticket-info" style="display:none; margin-top:10px;">

    <div class="alert alert-info" style="margin-bottom:15px;">

        <strong>💡 Importante:</strong><br>

        Al resolver el ticket en el momento, utilizá el campo
        <strong>Descripción</strong> para indicar tanto el detalle del problema
        como la <strong>solución aplicada</strong>.

    </div>

</div>

<?php } ?>

                            <div class="col-lg-12">
								<fieldset class="form-group">
									<label class="form-label semibold" for=sec_id>Destino del ticket (Sector) (*)</label>
									<select class="form-control" id="sec_id" name="sec_id">
										<option label="Seleccionar"></option>
										<input type="hidden" id="soporte_sec_id" name="soporte_sec_id" value="">
									</select>
								</fieldset>
							</div>

							<div class="col-lg-6">
								<fieldset class="form-group">
									<label class="form-label semibold" for=cat_id>Categoria (*)</label>
									<select class="form-control" id="cat_id" name="cat_id">
										<option label="Seleccionar"></option>
									</select>
								</fieldset>
							</div>

							<div class="col-lg-6">
								<fieldset class="form-group">
									<label class="form-label semibold" for=cat_id>SubCategoria (*)</label>
									<select class="form-control" id="cats_id" name="cats_id">
										<option label="Seleccionar"></option>
									</select>
								</fieldset>
							</div>

							<!-- Campos que aparecen solo para Requerimiento Arquitectura y Visual -->
<div class="col-lg-12" id="fila_local_cantidad" style="display:none;">
	<div class="row">
		<div class="col-lg-6">
			<fieldset class="form-group">
				<label class="form-label semibold" for="select_local">Local</label>
				<select class="form-control" id="select_local" name="select_local" disabled>
    <option value="">Seleccione un local...</option>
</select>

			</fieldset>
		</div>

		<div class="col-lg-6">
			<fieldset class="form-group">
				<label class="form-label semibold" for="input_cantidad">Cantidad</label>
				<input type="number" class="form-control" id="input_cantidad" name="input_cantidad" min="1" disabled>
			</fieldset>
		</div>
	</div>

	<div id="link_codigos" style="display:none; margin-bottom:20px;">
        <a href="../NuevoTicket/codigos-arts.php" target="_blank" style="font-weight:600; color:#007bff; text-decoration:underline;">
            🔗 Click acá para ver el Listado de artículos y códigos
        </a>
    </div>
</div>


							<!-- WARNING CAI (insertar justo después del bloque del select cats_id) -->
<div class="col-lg-12" id="cai-warning-wrap" style="display:none; margin-top:10px;">
  <div id="cai-warning" class="alert alert-danger" role="alert" style="margin:0;">
    <strong>⚠️ ATENCIÓN:</strong> Para los casos de <em>CAI</em> es obligatorio adjuntar las siguientes fotos:<br>
	- Foto del talonario fisico del CAI actual.<br>
	- Foto del talonario fisico del CAI nuevo.<br>
	- Foto del POS donde esta el CAI cargado.<br>
	
  </div>
</div>

<div class="col-lg-12" id="subtest-warning-wrap" style="display:none; margin-top:10px;">
  <div id="subtest-warning" class="alert alert-warning" role="alert" style="margin:0;">
    <strong>⚠️ ATENCIÓN:</strong><br>
-Complete la cantidad requerida de cada insumo en la descripción.<br>
-Reemplace la "X" por la cantidad solicitada.<br>
-Borre el renglón del insumo que NO va a pedir
  </div>
</div>

<div id="link_aromatizantes" style="display:none; margin-top:10px; margin-bottom:20px;">
    <a href="../NuevoTicket/aromatizantes.php"
       target="_blank"
       style="font-weight:600; color:#007bff; text-decoration:underline;">
        🔗 Click acá para ver los modelos de aromatizadores
    </a>
</div>


							<div class="col-lg-6">
								<fieldset class="form-group">
									<label class="form-label semibold" for=cat_id>Prioridad (*)</label>
									<select class="form-control" id="prio_id" name="prio_id">

									</select>
								</fieldset>
							</div>

							<div class="col-lg-6">
								<fieldset class="form-group">
									<label class="form-label semibold" for=cat_id>Documentos Adjuntos</label>
									<input type="file" class="form-control" id="fileElem" name="fileElem" multiple>
								</fieldset>
							</div>

							<div class="col-lg-12">
	<fieldset class="form-group">
		<label class="form-label semibold" for="tick_titulo">Título (*)</label>

		<input 
			type="text" 
			class="form-control" 
			id="tick_titulo" 
			name="tick_titulo" 
			placeholder="Ingrese Título"
			maxlength="55"
		>

		<small id="contador_titulo" style="color: #6c757d;">0 / 55</small>
	</fieldset>
</div>

<script>
document.getElementById('tick_titulo').addEventListener('input', function() {
    document.getElementById('contador_titulo').innerText =
        this.value.length + " / " + this.maxLength;
});
</script>

							
							<div class="col-lg-12">
								<fieldset class="form-group">
									<label class="form-label semibold" for="tick_descrip">Descripción (*)</label>
									<div class="summernote-theme-1">
										<textarea id="tick_descrip" class="summernote" name="tick_descrip" name="name"></textarea>
									</div>
								</fieldset>
							</div>

							<div class="col-lg-12">
								<button type="submit" id="btnguardar" name="action" value="add" class="btn btn-rounded btn-inline btn-prumary">Guardar</button>
							</div>
						</form>
					</div><!--.row-->

				</div>
			</div><!--.container-fluid-->
		</div><!--.page-content-->
		<?php require_once("../MainJs/js.php"); ?>
		<script type="text/javascript" src="nuevoticket.js"></script>
		<script type="text/javascript" src="../notificacion.js"></script>
	</body>

	</html>
<?php
} else { // Si no hay usuario logueado, te dirige a la pantalla de login
	header("Location:" . Conectar::ruta() . "index.php");
}
?>