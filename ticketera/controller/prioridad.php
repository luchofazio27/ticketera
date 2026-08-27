<?php
require_once("../config/conexion.php"); //Conexion con DB
require_once("../models/Prioridad.php"); //Conexion con el modelo Categoria
$prioridad = new Prioridad(); // Declaramos esa clase

switch($_GET["op"]){ //$_GET es una matriz de variables que se pasan al script actual a través de los parámetros de URL
    case "guardaryeditar":
            $datos= $prioridad->get_prioridad_x_nom($_POST["prio_nom"]);
            if(count($datos)==0){
                if(empty($_POST["prio_id"])){
                    $prioridad->insert_prioridad($_POST["prio_nom"]);
                    echo "1";
                } else {
                    $prioridad->update_prioridad($_POST["prio_id"],$_POST["prio_nom"]);
                    echo "2";
                }
            }else{
                echo "0";
            }
            break;

    case "listar":
        $datos = $prioridad->get_prioridad(); //Declaramos la variable datos para almacenar la consulta // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $data = array(); // Declaramos un array
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["prio_nom"];
            $sub_array[] = '<button type="button" onClick="editar(' . $row["prio_id"] . ');" id="' . $row["prio_id"] . '" class="btn btn-inline btn-warning btn-sm ladda-button"><i class="fa fa-edit"></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row["prio_id"] . ');" id="' . $row["prio_id"] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></button>';
            $data[] = $sub_array;
        };

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data), // Cuenta cuántos elementos hay en el arreglo
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results); // Convierte el arreglo en una cadena JSON
        break;

    case "eliminar":
        $prioridad->delete_prioridad($_POST["prio_id"]);
        break;

    case "mostrar";
        $datos = $prioridad->get_prioridad_x_id($_POST["prio_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["prio_id"] = $row["prio_id"];
                $output["prio_nom"] = $row["prio_nom"];
            }
            echo json_encode($output);
        }
        break;
    case "combo":
        $datos = $prioridad -> get_prioridad(); //Declaramos la variable datos para almacenar la consulta // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        if(is_array($datos) == true and count($datos) > 0) { //si es un array y si tiene datos
            $html.="<option label='Seleccionar'></option>";
            foreach($datos as $row){ // $row es una matriz que puede contener varios valores al mismo tiempo
               $html.="<option value='".$row['prio_id']."'>".$row['prio_nom']."</option>"; //declaramos un html con dichos valores
            }
            echo $html; // Retorna el html
        }
        break;
}
?>