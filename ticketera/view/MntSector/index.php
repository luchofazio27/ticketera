<?php
require_once("../../config/conexion.php");
require_once("../../helpers/seguridad.php"); //Llama al archivo seguridad.php para que se ejecute una sol vez

SeguridadHelper::verificarAcceso([2]); // Solo admin (rol 2) pueden entrar
if(isset($_SESSION["usu_id"])){
?>
<!DOCTYPE html>
<html>
<?php require_once("../MainHead/head.php");?>
<title>Ticketera VER :: Mantenimiento de Sectores</title>
</head>
<body class="with-side-menu">

<?php require_once("../MainHeader/header.php");?>
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
    ">Mantenimiento Sectores</h3>
                    </div>
                </div>
            </div>
        </header>

        <button type="button" id="btnnuevo" class="btn btn-inline btn-primary">Nuevo Registro</button>
        <br><br>

        <div class="box-typical box-typical-padding">
            <table id="sector_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal -->
<div id="modalmantenimiento" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="modal-close" data-dismiss="modal" aria-label="Close">
          <i class="font-icon-close-2"></i>
        </button>
        <h4 class="modal-title" id="mdltitulo"></h4>
      </div>
      <form method="post" id="sector_form">
        <div class="modal-body">
            <input type="hidden" id="sec_id" name="sec_id">
            <div class="form-group">
                <label class="form-label" for="sec_nom">Nombre</label>
                <input type="text" class="form-control" id="sec_nom" name="sec_nom" placeholder="Ingrese Nombre" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="sec_descr">Descripción</label>
                <textarea class="form-control" id="sec_descr" name="sec_descr" placeholder="Ingrese Descripción"></textarea>
            </div>
<div class="form-group">
    <label class="form-label" for="sec_correo_ticket">Correo para tickets abiertos</label>
    <input type="text" 
           class="form-control" 
           id="sec_correo_ticket" 
           name="sec_correo_ticket"
           placeholder="ej: sistemas@ver.com.ar (opcional)">
</div>
            <div class="form-group">
  <label for="es_soporte">¿Sector de soporte?</label><br>

  <!-- hidden para mantener el valor 0 al destildar -->
  <input type="hidden" name="es_soporte" value="0">

  <!-- checkbox visualmente mejorado -->
  <label class="checkbox-wrapper">
    <input type="checkbox" id="es_soporte" name="es_soporte" value="1">
    <span>SI</span>
  </label>
</div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-rounded btn-default" data-dismiss="modal">Cerrar</button>
          <button type="submit" name="action" class="btn btn-rounded btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once("../MainJs/js.php");?>
<script type="text/javascript" src="mntsector.js"></script>

</body>
</html>
<?php
} else {
    header("Location:".Conectar::ruta()."index.php");
}
?>

<style>
/* === Checkbox estilizado (sin afectar el funcionamiento) === */
.checkbox-wrapper {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  user-select: none;
}

.checkbox-wrapper input[type="checkbox"] {
  appearance: none;
  -webkit-appearance: none;
  width: 22px;
  height: 22px;
  border: 2px solid #999;
  border-radius: 6px;
  display: inline-block;
  position: relative;
  background-color: #fff;
  transition: all 0.2s ease;
}

.checkbox-wrapper input[type="checkbox"]:checked {
  background-color: #4CAF50;
  border-color: #4CAF50;
}

.checkbox-wrapper input[type="checkbox"]:checked::after {
  content: "✓";
  color: #fff;
  font-weight: bold;
  position: absolute;
  top: 1px;
  left: 5px;
  font-size: 16px;
}
</style>