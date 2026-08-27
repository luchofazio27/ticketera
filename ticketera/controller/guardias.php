<?php
require_once("../config/conexion.php");
require_once("../models/Usuario.php");

$usuario = new Usuario();

switch($_GET["op"]){

    case "mostrar":

        $datos = $usuario->get_guardias_actuales();

        echo json_encode($datos);

    break;

    case "combo_usuarios":

        $datos = $usuario->get_usuarios_sistemas();

        echo json_encode($datos);

    break;

    case "guardia_actual":

    $datos = $usuario->get_guardias_actuales();

    echo json_encode($datos);

break;

case "guardar":

    $usuario->actualizar_guardias(
        $_POST["guardia1"],
        $_POST["guardia2"],
        $_POST["celular1"],
        $_POST["celular2"]
    );

    echo json_encode(["status" => "ok"]);

break;
}