<?php
require_once("../config/conexion.php"); //Conexion con DB
require_once("../models/Categoria.php"); //Conexion con el modelo Categoria
$categoria = new Categoria(); // Declaramos esa clase

switch ($_GET["op"]) { //$_GET es una matriz de variables que se pasan al script actual a través de los parámetros de URL
    case "guardaryeditar":
        $datos = $categoria->get_categoria_x_nom($_POST["cat_nom"], $_POST["sec_id"]); //Declaramos la variable datos para almacenar la consulta // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        if (count($datos) == 0) {
            if (empty($_POST["cat_id"])) { // Preguntamos si de la vista el usu_id viene vacio
                $categoria->insert_categoria($_POST["cat_nom"], $_POST["sec_id"]); // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
                echo "1"; //En caso de que se actualice o inserte correctamente, retornamos 1
            } else {
                $categoria->update_categoria($_POST["cat_id"], $_POST["cat_nom"], $_POST["sec_id"]);
                echo "2"; //En caso de que se actualice o inserte correctamente, retornamos 2
            }
        } else {
            echo "0"; //En caso de que ya exista la categoria, retornamos 0
        }
        break;


    case "listar":
        $datos = $categoria->get_categoria(); //Declaramos la variable datos para almacenar la consulta // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $data = array(); // Declaramos un array
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["cat_nom"];
            $sub_array[] = $row["sec_nom"];
            $sub_array[] = '<button type="button" onClick="editar(' . $row["cat_id"] . ');" id="' . $row["cat_id"] . '" class="btn btn-inline btn-warning btn-sm ladda-button"><i class="fa fa-edit"></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row["cat_id"] . ');" id="' . $row["cat_id"] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></button>';
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
        $categoria->delete_categoria($_POST["cat_id"]);
        break;

    case "mostrar";
        $datos = $categoria->get_categoria_x_id($_POST["cat_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["cat_id"] = $row["cat_id"];
                $output["cat_nom"] = $row["cat_nom"];
                $output["sec_id"] = $row["sec_id"];
            }
            echo json_encode($output);
        }
        break;
    case "combo":
        session_start();
        $sec_id = isset($_SESSION["sec_id"]) ? $_SESSION["sec_id"] : null;

        if ($sec_id && $sec_id != "") {
            // Si el usuario tiene un sector, traer solo sus categorías
            $datos = $categoria->get_categoria_x_sector($sec_id);
        } else {
            // Si no tiene sector (por ejemplo, admin local), traer todas
            $datos = $categoria->get_categoria();
        }

        $html = "<option label='Seleccionar'></option>";
        if (is_array($datos) && count($datos) > 0) {
            foreach ($datos as $row) {
                $html .= "<option value='" . $row["cat_id"] . "'>" . $row["cat_nom"] . "</option>";
            }
        }
        echo $html;
        break;


    case "combo_categoria_x_sector":
        $datos = $categoria->get_categoria_x_sector($_POST["sec_id"]);
        $html = "<option label='Seleccionar'>Seleccionar</option>";
        foreach ($datos as $row) {
            $html .= "<option value='" . $row["cat_id"] . "'>" . $row["cat_nom"] . "</option>";
        }
        echo $html;
        break;

    case "combo_filtro":
    $datos = $categoria->get_categoria(); // 🔥 TODAS, sin filtrar
    $html = "<option value=''>Todas</option>";

    if (is_array($datos) && count($datos) > 0) {
        foreach ($datos as $row) {
            $html .= "<option value='{$row["cat_id"]}'>{$row["cat_nom"]}</option>";
        }
    }

    echo $html;
    break;    
}
