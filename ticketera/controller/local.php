<?php
require_once("../config/conexion.php");
require_once("../models/Local.php"); // modelo de locales

$local = new Local();

switch($_GET["op"]) {
    case "combo":
        $datos = $local->get_locales_activos();
        $html = "<option value=''>Seleccionar</option>";
        foreach($datos as $row){
            $html .= "<option value='".$row['loc_id']."'>".$row['loc_nom']."</option>";
        }
        echo $html;
    break;
    case "listar":
        $datos = $local->get_locales_activos();
        $data = array();
        foreach($datos as $row){
            $sub_array = array();
            $sub_array[] = $row["loc_nom"];
            $sub_array[] = '<button type="button" onClick="editar('.$row["loc_id"].');" id="'.$row["loc_id"].'" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar('.$row["loc_id"].');" id="'.$row["loc_id"].'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho"=>1,
            "iTotalRecords"=>count($data),
            "iTotalDisplayRecords"=>count($data),
            "aaData"=>$data
        );
        echo json_encode($results);
    break;

    case "mostrar":
        $datos = $local->get_local_x_id($_POST["loc_id"]);
        echo json_encode($datos);
    break;

    case "guardaryeditar":
        if (empty($_POST["loc_id"])) {
            $local->insert_local($_POST["loc_nom"]);
        } else {
            $local->update_local($_POST["loc_id"], $_POST["loc_nom"]);
        }
    break;

    case "eliminar":
        $local->delete_local($_POST["loc_id"]);
    break;
}

?>
