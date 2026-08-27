<?php
require_once("../../config/conexion.php");

if(isset($_SESSION["usu_id"])){
?>

<!DOCTYPE html>
<html>

<?php require_once("../MainHead/head.php"); ?>

<title>Ticketera VER :: Guardias</title>

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
    ">Guardias de Sistemas</h3>
<p><br>
    <span class="glyphicon glyphicon-info-sign" style="color:#3498db;"></span>
    Los siguientes contactos corresponden a la <b>guardia de Sistemas para fines de semana y feriados</b>.<br>
    Utilice estos números únicamente ante situaciones urgentes que afecten la facturación y requieran atención inmediata.<br>
    Durante los días hábiles, el equipo de Sistemas se encuentra disponible a través de los canales habituales y la Ticketera.
</p>
                    </div>
                </div>
            </div>
        </header>

        <div class="box-typical box-typical-padding">

            <div id="guardias_container">
                Cargando guardias...
            </div>

        </div>

    </div>
</div>

<?php require_once("../MainJs/js.php"); ?>

<script type="text/javascript" src="guardias.js"></script>

</body>
</html>

<?php
} else {
    header("Location:".Conectar::ruta()."index.php");
}
?>