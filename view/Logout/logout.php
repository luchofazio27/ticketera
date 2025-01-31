<?php
require_once("../../config/conexion.php"); //Conexion con DB
session_destroy(); //destruye toda la información asociada con la sesión actual
header("Location:".Conectar::ruta()."index.php"); //te redirija denuevo al Login
exit();
?>