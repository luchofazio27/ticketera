<?php
require_once("../../config/conexion.php");
if (isset($_SESSION["usu_id"])) {
?>
<!DOCTYPE html>
<html>
<?php require_once("../MainHead/head.php"); ?>
<title>Ticketera VER :: Aromatizantes</title>

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
                        <h3>Insumos de Aromatización</h3>
                        <ol class="breadcrumb breadcrumb-simple">
                            <li><a href="#">Home</a></li>
                            <li class="active">Modelos de referencia</li>
                        </ol>
                    </div>
                </div>
            </div>
        </header>

        <div class="box-typical box-typical-padding">

            <p class="lead">
                A continuación se muestran los equipos disponibles para solicitar mediante ticket.
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Artículo</th>
                            <th>Imagen</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>
                                <strong>MiniScentHD</strong>
                            </td>

                            <td>
                                <img
                                    style="width:150px"
                                    src="../../public/img/miniscenthd.png"
                                    alt="MiniScentHD">
                            </td>

                            <td>
                                Equipo aromatizador compacto.
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Equipo Eléctrico</strong>
                            </td>

                            <td>
                                <img
                                    style="width:150px"
                                    src="../../public/img/equipoelectrico.png"
                                    alt="Equipo Eléctrico">
                            </td>

                            <td>
                                Equipo principal de aromatización.
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Bidón</strong>
                            </td>

                            <td>
                                <img
                                    style="width:150px"
                                    src="../../public/img/bidon.png"
                                    alt="Bidón">
                            </td>

                            <td>
                                Bidones de 1 y 5 Litros
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

<?php require_once("../MainJs/js.php"); ?>
<script type="text/javascript" src="../notificacion.js"></script>

</body>
</html>

<?php
} else {
    header("Location:" . Conectar::ruta() . "index.php");
}
?>