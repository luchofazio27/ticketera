<?php

require_once("../../config/conexion.php");

/* Limpiar variables de sesión de la Ticketera */
$_SESSION = array();

/* Destruir sesión PHP de la Ticketera */
session_destroy();

/* Volver siempre al login principal */
header("Location: " . Conectar::ruta());
exit();

?>