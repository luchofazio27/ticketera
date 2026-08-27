<?php

require_once("../config/conexion.php");
require_once("../models/Usuario.php");

$usuario = new Usuario();

$datos = $usuario->get_usuario_x_correo('ver.abasto@ver.com.ar');

echo "<pre>";
print_r($datos);
echo "</pre>";