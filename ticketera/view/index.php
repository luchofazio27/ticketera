<?php
require_once("../config/conexion.php"); //Conexion con DB
header("Location:".Conectar::ruta()."index.php"); //Redirecciona a la pantalla de login
?>