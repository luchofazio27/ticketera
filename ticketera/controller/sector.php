<?php
require_once("../config/conexion.php");
require_once("../models/Sector.php");

$sector = new Sector();

switch ($_GET["op"]) {

    case "guardaryeditar":
        if (empty($_POST["sec_id"])) {
            // Insertar
            $sector->insert_sector($_POST["sec_nom"], $_POST["sec_descr"], $_POST["es_soporte"], $_POST["sec_correo_ticket"]);
            echo "1"; // insertado
        } else {
            // Actualizar
            $sector->update_sector($_POST["sec_id"], $_POST["sec_nom"], $_POST["sec_descr"], $_POST["es_soporte"], $_POST["sec_correo_ticket"]);
            echo "2"; // actualizado
        }
        break;

    case "eliminar":
        $sector->delete_sector($_POST["sec_id"]);
        break;

    case "mostrar":
        $datos = $sector->get_sector_x_id($_POST["sec_id"]);
        echo json_encode($datos);
        break;

    case "listar":
        $datos = $sector->get_sectores();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["sec_nom"];
            $sub_array[] = $row["sec_descr"];
            $sub_array[] = '<button type="button" onClick="editar(' . $row["sec_id"] . ');" id="' . $row["sec_id"] . '" class="btn btn-inline btn-warning btn-sm ladda-button"><i class="fa fa-edit"></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row["sec_id"] . ');" id="' . $row["sec_id"] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></button>';
            $data[] = $sub_array;
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case "combo":
        $datos = $sector->get_sectores();
        $html = "<option value=''>Seleccionar</option>";
        foreach ($datos as $row) {
            $html .= "<option value='" . $row["sec_id"] . "'>" . $row["sec_nom"] . "</option>";
        }
        echo $html;
        break;

    case "combo_soporte":
        $datos = $sector->get_sectores_soporte();
        $html = "<option value=''>Seleccionar</option>";
        foreach ($datos as $row) {
            $html .= "<option value='" . $row["sec_id"] . "'>" . $row["sec_nom"] . "</option>";
        }
        echo $html;
        break;
}