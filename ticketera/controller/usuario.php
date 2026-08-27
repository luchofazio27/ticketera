<?php
require_once("../config/conexion.php"); //Conexion con DB
require_once("../models/Usuario.php"); //Conexion con el modelo Usuario
$usuario = new Usuario(); // Declaramos esa clase

$key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
$cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); //Vector de inicialización (IV) necesario para el cifrado

switch ($_GET["op"]) { //$_GET es una matriz de variables que se pasan al script actual a través de los parámetros de URL
    case "guardaryeditar":
        $usu_id = isset($_POST["usu_id"]) ? $_POST["usu_id"] : null;
        $datos = $usuario->get_usuario_x_correo($_POST["usu_correo"]);

        if (count($datos) == 0 || ($usu_id && $datos[0]["usu_id"] == $usu_id)) {
            // Si es nuevo
            if (empty($usu_id)) {
                $usuario->insert_usuario($_POST["usu_nom"], $_POST["usu_ape"], $_POST["usu_correo"], $_POST["usu_pass"], $_POST["rol_id"], $_POST["sec_id"]);
                echo "1";
            } else {
                // Si está editando sin cambiar la contraseña
                if (empty($_POST["usu_pass"])) {
                    $usuario->update_usuario_sin_pass($usu_id, $_POST["usu_nom"], $_POST["usu_ape"], $_POST["usu_correo"], $_POST["rol_id"], $_POST["sec_id"]);
                } else {
                    $usuario->update_usuario($usu_id, $_POST["usu_nom"], $_POST["usu_ape"], $_POST["usu_correo"], $_POST["usu_pass"], $_POST["rol_id"], $_POST["sec_id"]);
                }
                echo "2";
            }
        } else {
            echo "0"; // correo duplicado
        }
        break;



    case "listar":
        $datos = $usuario->get_usuario(); // Llamamos a la función get_usuario del modelo Usuario
        $data = array(); // Declaramos un array
        foreach ($datos as $row) { // Recorremos el array de datos
            $sub_array = array(); // Se declara un sub_array
            $sub_array[] = $row["usu_nom"];
            $sub_array[] = $row["usu_ape"];
            $sub_array[] = $row["usu_correo"];
            $sub_array[] = $row["usu_pass"];
            $sub_array[] = $row["sec_nom"];
            if ($row["rol_id"] == "1") { // Verificamos el rol id
                $sub_array[] = '<span class="label label-pill label-success">Usuario</span>';
            } else if ($row["rol_id"] == "2") {
                $sub_array[] = '<span class="label label-pill label-info">Administrador</span>';
            } else if ($row["rol_id"] == "3") {
                $sub_array[] = '<span class="label label-pill label-warning">Supervisora Locales</span>';
            } else if ($row["rol_id"] == "4") {
                $sub_array[] = '<span class="label label-pill label-primary">Supervisora Visual</span>';
            } else if ($row["rol_id"] == "5") {
                $sub_array[] = '<span class="label label-pill label-primary">Soporte</span>';
            } else {
                $sub_array[] = '<span class="label label-pill label-default">Desconocido</span>';
            }
            $sub_array[] = '<button type="button" onClick="editar(' . $row["usu_id"] . ');" id="' . $row["usu_id"] . '" class="btn btn-inline btn-warning btn-sm ladda-button"><i class="fa fa-edit"></button>'; // onClick="ver('.$row["tick_id"].');": Este es un evento que se ejecutará cuando se haga clic en el botón
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row["usu_id"] . ');" id="' . $row["usu_id"] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></button>';
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
        $usuario->delete_usuario($_POST["usu_id"]);
        break;

    case "mostrar":
        $datos = $usuario->get_usuario_x_id($_POST["usu_id"]);
        if (is_array($datos) && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["usu_id"]     = $row["usu_id"];
                $output["usu_nom"]    = $row["usu_nom"];
                $output["usu_ape"]    = $row["usu_ape"];
                $output["usu_correo"] = $row["usu_correo"];
                $output["rol_id"]     = $row["rol_id"];
                $output["sec_id"]     = $row["sec_id"];

                if ((int)$row["rol_id"] === 2) {
                    // Solo descifrar si es un usuario de sistemas
                    $iv_len = openssl_cipher_iv_length($cipher);
                    $raw = base64_decode($row["usu_pass"], true);

                    if ($raw !== false && strlen($raw) > $iv_len) {
                        $iv_dec = substr($raw, 0, $iv_len);
                        $cifradoSinIV = substr($raw, $iv_len);
                        $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec);
                        $output["usu_pass"] = $decifrado !== false ? $decifrado : "";
                    } else {
                        $output["usu_pass"] = "";
                    }
                } else {
                    // Usuarios comunes (Microsoft) → no mostrar pass
                    $output["usu_pass"] = "";
                }
            }
        }
        echo json_encode($output);
        break;

    case "total";
        $datos = $usuario->get_usuario_total_x_id($_POST["usu_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["TOTAL"] = $row["TOTAL"];
            }
            echo json_encode($output);
        }
        break;
    case "totalabierto";
        $datos = $usuario->get_usuario_totalabierto_x_id($_POST["usu_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["TOTAL"] = $row["TOTAL"];
            }
            echo json_encode($output);
        }
        break;
    case "totalcerrado";
        $datos = $usuario->get_usuario_totalcerrado_x_id($_POST["usu_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["TOTAL"] = $row["TOTAL"];
            }
            echo json_encode($output);
        }
        break;

    case "grafico";
        $datos = $usuario->get_usuario_grafico($_POST["usu_id"]);
        echo json_encode($datos);
        break;
    case "combo";
        $datos = $usuario->get_usuario_x_rol();
        if (is_array($datos) == true and count($datos) > 0) {
            $html .= "<option label='Seleccionar'></option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['usu_id'] . "'>" . $row['usu_nom'] . "</option>";
            }
            echo $html;
        }
        break;

    case "combo_solicitante":

    $datos = $usuario->get_usuario();

    $html = "";

    if (is_array($datos) && count($datos) > 0) {

        foreach ($datos as $row) {

            $html .= "<option value='" . $row["usu_id"] . "'>"
                . htmlspecialchars($row["usu_nom"] . " " . $row["usu_ape"])
                . "</option>";
        }
    }

    echo $html;

    break;

    case "password":
        $cifrado = openssl_encrypt($_POST["usu_pass"], $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $textoCifrado = base64_encode($iv . $cifrado);
        $usuario->update_usuario_pass($_POST["usu_id"],  $textoCifrado);
        break;
    case "correo":
        $datos = $usuario->get_usuario_x_correo($_POST["usu_correo"]);
        if (is_array($datos) == true and count($datos) > 0) {
            echo "Existe";
        } else {
            echo "NoExiste";
        }
        break;

    case "get_sectores":
        $datos = $usuario->get_sectores();
        echo json_encode($datos);
        break;
    case "combo_x_sector":
        $datos = $usuario->get_usuario_x_sector($_POST["sec_id"]);
        $html = "";
        foreach ($datos as $row) {
            $html .= "<option value='" . $row["usu_id"] . "'>" . $row["usu_nom"] . " " . $row["usu_ape"] . "</option>";
        }
        echo $html;
        break;
}