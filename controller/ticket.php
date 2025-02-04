<?php
require_once("../config/conexion.php"); //Conexion con DB
require_once("../models/Ticket.php"); //Conexion con el modelo Ticket
$ticket = new Ticket(); // Declaramos esa clase

switch($_GET["op"]){ //$_GET es una matriz de variables que se pasan al script actual a través de los parámetros de URL
    case "insert":
        $ticket-> insert_ticket($_POST["usu_id"],$_POST["cat_id"],$_POST["tick_titulo"],$_POST["tick_descrip"]);
        break;
}
//La línea de código está llamando al método insert_ticket del objeto $ticket y le pasa cuatro parámetros que se toman de un formulario enviado mediante el método POST
?>
