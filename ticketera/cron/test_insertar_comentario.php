<?php

require_once("../config/conexion.php");
require_once("../models/Usuario.php");
require_once("../models/Ticket.php");

$usuario = new Usuario();
$ticket = new Ticket();

$correo = 'ver.abasto@ver.com.ar';

$datosUsuario = $usuario->get_usuario_x_correo($correo);

if (empty($datosUsuario)) {
    die("Usuario no encontrado");
}

$usu_id = $datosUsuario[0]["usu_id"];

echo "Usuario encontrado: ".$usu_id."<br>";

$ticket_id = 321;

$comentario = "PRUEBA AUTOMATICA DESDE IMAP - ".date('Y-m-d H:i:s');

$resultado = $ticket->insert_ticketdetalle(
    $ticket_id,
    $usu_id,
    $comentario,
    true
);

echo "<pre>";
print_r($resultado);
echo "</pre>";

echo "<br>Comentario insertado";