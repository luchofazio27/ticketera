<?php
require_once("../config/conexion.php"); //Conexion con DB
require_once("../models/Ticket.php"); //Conexion con el modelo Ticket
$ticket = new Ticket(); // Declaramos esa clase

switch($_GET["op"]){ //$_GET es una matriz de variables que se pasan al script actual a través de los parámetros de URL
    case "insert":
        $ticket-> insert_ticket($_POST["usu_id"],$_POST["cat_id"],$_POST["tick_titulo"],$_POST["tick_descrip"]); //La línea de código está llamando al método insert_ticket del objeto $ticket y le pasa cuatro parámetros que se toman de un formulario enviado mediante el método POST
        break;
    case "listar_x_usu":
        $datos = $ticket -> listar_ticket_x_usu($_POST["usu_id"]); //Llama al metodo "listar_ticket_x_usu" del objeto ticket y le envia parametros
        $data = Array(); // Declaramos un array
        foreach($datos as $row){ // Recorre la variable datos que tiene el listado de tickets
            $sub_array = array(); // Se crea un array que almacena el tick id, cat nom, tick titulo.
            $sub_array[] = $row["tick_id"];
            $sub_array[] = $row["cat_nom"];
            $sub_array[] = $row["tick_titulo"];
            $sub_array[] = '<button type="button" onClick="ver('.$row["tick_id"].');" id="'.$row["tick_id"].'" class="btn btn-outline-primary btn-icon><div><i class="fa fa-edit"></div></button>'; // onClick="ver('.$row["tick_id"].');": Este es un evento que se ejecutará cuando se haga clic en el botón
        } 
        
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data), // Cuenta cuántos elementos hay en el arreglo
            "iTotalDisplayRecords" => count($data),
            "aaData"=> $data);
            echo json_encode($results); // Convierte el arreglo en una cadena JSON
        break;    
        
}

?>
