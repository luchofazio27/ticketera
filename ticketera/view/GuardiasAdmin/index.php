<?php
require_once("../../config/conexion.php");

if(isset($_SESSION["usu_id"]) && $_SESSION["rol_id"] == 2){
?>

<!DOCTYPE html>
<html>

<?php require_once("../MainHead/head.php"); ?>

<title>Ticketera VER :: Administración de Guardias</title>

<body class="with-side-menu">

<?php require_once("../MainHeader/header.php"); ?>

<div class="mobile-menu-left-overlay"></div>

<?php require_once("../MainNav/nav.php"); ?>

<div class="page-content">
    <div class="container-fluid">

        <header class="section-header">
            <h3 style="
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 4px;
    ">Administración de Guardias</h3>
        </header>

        <div class="box-typical box-typical-padding">

            <div class="row">

    <div class="col-lg-6">
        <fieldset class="form-group">
            <label>Guardia 1</label>
            <select id="guardia1" class="form-control"></select>
        </fieldset>
    </div>

    <div class="col-lg-6">
        <fieldset class="form-group">
            <label>Celular Guardia 1</label>
            <input type="text" id="celular1" class="form-control">
        </fieldset>
    </div>

</div>

<div class="row">

    <div class="col-lg-6">
        <fieldset class="form-group">
            <label>Guardia 2</label>
            <select id="guardia2" class="form-control"></select>
        </fieldset>
    </div>

    <div class="col-lg-6">
        <fieldset class="form-group">
            <label>Celular Guardia 2</label>
            <input type="text" id="celular2" class="form-control">
        </fieldset>
    </div>

</div>

<div class="row">
    <div class="col-lg-12">
        <button id="btnGuardar" class="btn btn-success">
            Guardar Guardias
        </button>
    </div>
</div>

        </div>

    </div>
</div>

<?php require_once("../MainJs/js.php"); ?>

<script src="guardiasadmin.js"></script>

</body>
</html>

<?php
}else{
    header("Location:".Conectar::ruta()."index.php");
}
?>