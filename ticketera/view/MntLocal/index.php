<?php require_once("../../config/conexion.php"); ?>
<?php if(isset($_SESSION["usu_id"])) { ?>
<!DOCTYPE html>
<html>
<?php require_once("../MainHead/head.php"); ?>
<title>Ticketera VER :: Mantenimiento de Locales</title>
</head>
<body class="with-side-menu">
    <?php require_once("../MainHeader/header.php"); ?>
    <?php require_once("../MainNav/nav.php"); ?>

    <div class="page-content">
        <div class="container-fluid">
            <div class="box-typical box-typical-padding">
                <div class="box-typical-header">
                    <h3 class="panel-title" style="
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 4px;
    ">Mantenimiento de Locales</h3>
                </div>
                <button id="btnnuevo" type="button" class="btn btn-primary">Nuevo Local</button>
                <table id="local_data" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nombre del Local</th>
                            <th>Editar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="modalmantenimiento" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <form id="local_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Formulario de Local</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="loc_id" id="loc_id">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="loc_nom" id="loc_nom" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php require_once("../MainJs/js.php"); ?>
    <script type="text/javascript" src="local.js"></script>
</body>
</html>
<?php } else {
    header("Location:".Conectar::ruta()."index.php");
} ?>