<?php

use Dom\Document;

require_once("../config/conexion.php"); //Conexion con DB
require_once("../models/Ticket.php"); //Conexion con el modelo Ticket
$ticket = new Ticket(); // Declaramos esa clase
require_once("../models/Usuario.php"); //Conexion con el modelo Usuario
$usuario = new Usuario(); // Declaramos esa clase
require_once("../models/Documento.php"); //Conexion con el modelo Documento
$documento = new Documento(); // Declaramos esa clase
require_once("../models/Email.php"); //Conexion con el modelo Email
$email = new Email(); // Declaramos esa clase
require_once("../models/Sector.php"); //Conexion con el modelo Sector
$sector = new Sector(); // Declaramos esa clase

$key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
$cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); //Vector de inicialización (IV) necesario para el cifrado

switch ($_GET["op"]) { //$_GET es una matriz de variables que se pasan al script actual a través de los parámetros de URL
    case "insert":

$resolver_ticket = isset($_POST["resolver_ticket"]) 
    ? (int)$_POST["resolver_ticket"] 
    : 0;

    // Usuario que está creando el ticket
$usu_id_creador = $_SESSION["usu_id"];
$rol_creador = (int)$_SESSION["rol_id"];

// Por defecto, el ticket queda a nombre del usuario logueado
$usu_id = $usu_id_creador;

// Solo los roles 2, 4 y 5 pueden crear tickets a nombre de otro usuario
if (in_array($rol_creador, [2, 4, 5], true)) {

    if (!empty($_POST["usu_id_solicitante"])) {
        $usu_id = (int)$_POST["usu_id_solicitante"];
    }
}

    $datos = $ticket->insert_ticket(
    $usu_id,
    $_POST["cat_id"],
    $_POST["cats_id"],
    $_POST["tick_titulo"],
    $_POST["tick_descrip"],
    $_POST["prio_id"],
    $_POST["soporte_sec_id"],
    $usu_id_creador
);

    if (is_array($datos) == true and count($datos) > 0) {

        foreach ($datos as $row) {

            $output["tick_id"] = $row["tick_id"];

            // ARCHIVOS
            if (empty($_FILES['files']['name'])) {

            } else {

                $countfiles = count($_FILES['files']['name']);
                $ruta = "../public/document/" . $output["tick_id"] . "/";

                if (!file_exists($ruta)) {
                    mkdir($ruta, 0777, true);
                }

                for ($index = 0; $index < $countfiles; $index++) {

                    $doc1 = $_FILES['files']['tmp_name'][$index];
                    $destino = $ruta . $_FILES['files']['name'][$index];

                    $documento->insert_documento(
                        $output["tick_id"],
                        $_FILES['files']['name'][$index]
                    );

                    move_uploaded_file($doc1, $destino);
                }
            }
        }
    }

    echo json_encode($datos);

if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}

require_once("../models/Email.php");

$email = new Email();

if ($resolver_ticket == 1) {

    // Cerrar inmediatamente el ticket recién creado
    $ticket->update_ticket($output["tick_id"]);

    // Registrar evento de sistema indicando quién lo cerró
    $datosUsuario = $usuario->get_usuario_x_id($_SESSION["usu_id"]);

    $nomUsuario = $datosUsuario[0]["usu_nom"] . " " . $datosUsuario[0]["usu_ape"];

    $datosSector = $sector->get_sector_x_id($_SESSION["sec_id"]);
    $nomSector = $datosSector["sec_nom"];

    $mensajeSistema =
        "[SISTEMA][CIERRE] "
        . $nomUsuario
        . " ("
        . $nomSector
        . ") creó y cerró el ticket.";

    $ticket->insert_ticketdetalle_sistema(
        $output["tick_id"],
        $_SESSION["usu_id"],
        $mensajeSistema
    );

    // Enviar correo de ticket cerrado
    $email->ticket_cerrado(
    $output["tick_id"],
    $_SESSION["usu_id"],
    true
);

} else {

    // Comportamiento normal: ticket abierto
    $email->ticket_abierto($output["tick_id"]);
}
    break;

    case "update":
        $iv_dec = substr(base64_decode($_POST["tick_id"]), 0, openssl_cipher_iv_length($cipher)); /* Obtener el IV del texto cifrado */
        $cifradoSinIV = substr(base64_decode($_POST["tick_id"]), openssl_cipher_iv_length($cipher)); /* Obtener el texto cifrado sin el IV */
        $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec); /* TODO: Descifrado */
        $ticket->update_ticket($decifrado); //La línea de código está llamando al método update_ticket del objeto $ticket y le pasa un parámetro tick_id
        $ticket->insert_ticketdetalle_cerrar($decifrado, $_SESSION["usu_id"]);
$datosUsuario = $usuario->get_usuario_x_id($_SESSION["usu_id"]);
$nomUsuario = $datosUsuario[0]["usu_nom"] . " " . $datosUsuario[0]["usu_ape"];

$datosSector = $sector->get_sector_x_id($_SESSION["sec_id"]);
$nomSector = $datosSector["sec_nom"];

$mensajeSistema =
"[SISTEMA][CIERRE]
{$nomUsuario} ({$nomSector}) cerró el ticket.";

$ticket->insert_ticketdetalle_sistema(
    $decifrado,
    $_SESSION["usu_id"],
    $mensajeSistema
);
        $email->ticket_cerrado($decifrado, $_POST["usu_id"]);
        break;
    case "reabrir":

    // ==========================================
    // OBTENER ID DEL TICKET
    // ==========================================

    $tick_id_recibido = $_POST["tick_id"];

    // Detectar si viene encriptado o si ya viene como ID numérico
    if (is_numeric($tick_id_recibido)) {

        // Desde HistorialTicket viene el ID numérico
        $tick_id = $tick_id_recibido;

    } else {

        // Desde DetalleTicket viene el ID encriptado

        $iv_dec = substr(
            base64_decode($tick_id_recibido),
            0,
            openssl_cipher_iv_length($cipher)
        );

        $cifradoSinIV = substr(
            base64_decode($tick_id_recibido),
            openssl_cipher_iv_length($cipher)
        );

        $tick_id = openssl_decrypt(
            $cifradoSinIV,
            $cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv_dec
        );
    }

    // Validar que hayamos obtenido un ID válido
    if (empty($tick_id) || !is_numeric($tick_id)) {

        echo json_encode([
            "error" => "No se pudo obtener un ID de ticket válido."
        ]);

        exit;
    }


    // ==========================================
    // REABRIR TICKET
    // ==========================================

    $ticket->reabrir_ticket($tick_id);


    // ==========================================
    // OBTENER USUARIO QUE REABRE
    // ==========================================

    $datosUsuario = $usuario->get_usuario_x_id($_SESSION["usu_id"]);

    $nomUsuario =
        $datosUsuario[0]["usu_nom"] . " " .
        $datosUsuario[0]["usu_ape"];


    // ==========================================
    // OBTENER SECTOR
    // ==========================================

    $datosSector = $sector->get_sector_x_id($_SESSION["sec_id"]);

    $nomSector = $datosSector["sec_nom"];


    // ==========================================
    // EVENTO DE SISTEMA
    // ==========================================

    $mensajeSistema =
        "[SISTEMA][REAPERTURA]\n" .
        "{$nomUsuario} ({$nomSector}) reabrió el ticket.";

    $ticket->insert_ticketdetalle_sistema(
        $tick_id,
        $_SESSION["usu_id"],
        $mensajeSistema
    );


    // ==========================================
    // EMAIL DE REAPERTURA
    // ==========================================

    $email->ticket_reabierto(
        $tick_id,
        $_POST["usu_id"]
    );


    // ==========================================
    // RESPUESTA AL AJAX
    // ==========================================

    echo json_encode([
        "ok" => 1,
        "tick_id" => $tick_id
    ]);

    break;
    case "asignar":
        // Validación mínima: que exista tick_id
        if (empty($_POST["tick_id"])) {
            echo json_encode(["error" => "tick_id faltante"]);
            exit;
        }

        $tick_id = $_POST["tick_id"];
        // usu_asig puede venir vacío (asignación a sector)
        $usu_asig = isset($_POST["usu_asig"]) ? $_POST["usu_asig"] : null;
        $soporte_sec_id = isset($_POST["soporte_sec_id"]) ? $_POST["soporte_sec_id"] : null;

        // Permisos: dejar una regla simple
        $rol_actual = $_SESSION['rol_id'];
        $usu_actual = $_SESSION['usu_id'];

        // Obtener ticket actual para validar si el usuario que intenta reasignar es el asignado
        $ticketInfo = $ticket->listar_ticket_x_id($tick_id); // asumimos que devuelve array
        $ticketInfoRow = is_array($ticketInfo) && count($ticketInfo) ? $ticketInfo[0] : null;
        $currentAssigned = $ticketInfoRow['usu_asig'] ?? null;

        // Reglas: permitir si es admin (2) o soportista (rol 5) o el usuario actualmente asignado
        $canAssign = ($rol_actual == 2) || ($rol_actual == 5) || ($currentAssigned == $usu_actual);

        if (!$canAssign) {
            echo json_encode(["error" => "No tiene permisos para asignar"]);
            exit;
        }

        try {

    $ticket->update_ticket_asignacion($tick_id, $usu_asig, $soporte_sec_id);

    //==================================================
    // COMENTARIO AUTOMÁTICO DE ASIGNACIÓN
    //==================================================

    $usuarioActual = $usuario->get_usuario_x_id($_SESSION["usu_id"]);

    $nombreAsignador =
        $usuarioActual[0]["usu_nom"] . " " .
        $usuarioActual[0]["usu_ape"];

    $mensaje = "[SISTEMA][ASIGNACION]";

    if (!empty($soporte_sec_id) && !empty($usu_asig)) {

    $sectorDestino = $sector->get_sector_x_id($soporte_sec_id);
    $usuarioDestino = $usuario->get_usuario_x_id($usu_asig);

    $mensaje .= "\n\n" .
        $nombreAsignador .
        " reasignó el ticket al sector " .
        $sectorDestino["sec_nom"] .
        " y lo asignó a " .
        $usuarioDestino[0]["usu_nom"] . " " .
        $usuarioDestino[0]["usu_ape"] . ".";

}
elseif (!empty($soporte_sec_id)) {

    $sectorDestino = $sector->get_sector_x_id($soporte_sec_id);

    $mensaje .= "\n\n" .
        $nombreAsignador .
        " derivó el ticket al sector " .
        $sectorDestino["sec_nom"] . ".";

}
elseif (!empty($usu_asig)) {

    $usuarioDestino = $usuario->get_usuario_x_id($usu_asig);

    $mensaje .= "\n\n" .
        $nombreAsignador .
        " asignó el ticket a " .
        $usuarioDestino[0]["usu_nom"] . " " .
        $usuarioDestino[0]["usu_ape"] . ".";

}

    $ticket->insert_ticketdetalle_sistema(
        $tick_id,
        $_SESSION["usu_id"],
        $mensaje
    );

    //==================================================

    if (!empty($usu_asig)) {
        $email->ticket_asignado($tick_id);
    }

    echo json_encode(["ok" => 1]);

} catch (Exception $e) {

    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
        break;



    case "listar_x_usu":
    $rol_id = $_SESSION['rol_id'];
    $usu_id = $_SESSION['usu_id'];
    $sec_id = $_SESSION['sec_id'];

    $sectores = [];
    $sectores_data = $sector->get_sectores_soporte();
    foreach ($sectores_data as $s) {
        $sectores[$s['sec_id']] = $s['sec_nom'];
    }

    $datos = $ticket->listar_ticket_x_usu($_POST["usu_id"]);
    $data = array();

    foreach ($datos as $row) {

        // 🔹 Determinar si va en negrita
        $negrita = false;

        if ($rol_id == 1 && $row['leido_usuario'] == 0) {
            $negrita = true;
        }

        if ($rol_id != 1 && $row['leido_soporte'] == 0) {
            $negrita = true;
        }

        // Omitir tickets cerrados
        if ($row["tick_estado"] == "Cerrado") {
            continue;
        }

        // Usuario común solo ve sus propios tickets
        if ($rol_id == 1 && $row["usu_id"] != $usu_id) {
            continue;
        }

        // 🔹 Aplicar estilo
        $style = $negrita ? 'style="font-weight:bold;"' : '';

        $sub_array = array();

        $sub_array[] = '<span '.$style.'>'.$row["tick_id"].'</span>';
        $sub_array[] = '<span '.$style.'>'.$row["usu_nom"] . ' ' . $row["usu_ape"].'</span>';
        $sub_array[] = '<span '.$style.'>'.$row["sec_nom"].'</span>';
        $sub_array[] = '<span '.$style.'>'.$row["cat_nom"].'</span>';
        $sub_array[] = '<span '.$style.'>'.$row["tick_titulo"].'</span>';
        $sub_array[] = '<span '.$style.'>'.$row["prio_nom"].'</span>';

        // Estado (NO hace falta negrita acá)
        if ($row["tick_estado"] == "Abierto") {
            $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
        } elseif ($row["tick_estado"] == "En Progreso") {
            $sub_array[] = '<span class="label label-pill label-warning">En Progreso</span>';    
        } else {
            $sub_array[] = '<a onClick="CambiarEstado(' . $row["tick_id"] . ')">
            <span class="label label-pill label-danger">Cerrado</span></a>';
        }

        // Fecha
        $sub_array[] = '<span '.$style.'>'.date("d/m/Y H:i:s", strtotime($row["fech_crea"])).'</span>';

        // Asignación
        if ($row["usu_asig"] == null) {
            if (!empty($row["soporte_sec_id"])) {
                $nombre_sector = $sectores[$row["soporte_sec_id"]] ?? 'Sin Sector';
                $sub_array[] = '<span class="label label-pill label-info">' . $nombre_sector . '</span>';
            } else {
                $sub_array[] = '<span class="label label-pill label-warning">Sin Asignar</span>';
            }
        } else {
            $datos1 = $usuario->get_usuario_x_id($row["usu_asig"]);
            foreach ($datos1 as $row1) {
                $sub_array[] = '<span class="label label-pill label-success">' . $row1["usu_nom"] . '</span>';
            }
        }

        // Botón ver
        $cifrado = openssl_encrypt($row["tick_id"], $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $textoCifrado = base64_encode($iv . $cifrado);
        $sub_array[] = '<button type="button" data-ciphertext="' . $textoCifrado . '" 
        id="' . $textoCifrado . '" class="btn btn-inline btn-primary btn-sm ladda-button">
        <i class="fa fa-eye"></i></button>';

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


    case "listar_x_usu_historial":
        $datos = $ticket->listar_ticket_x_usu($_POST["usu_id"]); //Llama al metodo "listar_ticket_x_usu" del objeto ticket y le envia parametros
        $data = array(); // Declaramos un array
        foreach ($datos as $row) { // Recorre la variable datos que tiene el listado de tickets
            // 🔹 1. Filtrar tickets cerrados (no se muestran)
            if ($row["tick_estado"] == "Abierto" || $row["tick_estado"] == "En Progreso") {
                continue;
            }
            $sub_array = array(); // Se crea un array que almacena el tick id, cat nom, tick titulo.
            $sub_array[] = $row["tick_id"];
            $sub_array[] = $row["usu_nom"] . ' ' . $row["usu_ape"];
            $sub_array[] = $row["sec_nom"];
            $sub_array[] = $row["cat_nom"];
            $sub_array[] = $row["tick_titulo"];
            $sub_array[] = $row["prio_nom"];
            if ($row["tick_estado"] == "Abierto") { // Verificamos el estado del ticket para darle una clase
                $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
            } else {
                $sub_array[] = '<a onClick="CambiarEstado(' . $row["tick_id"] . ')"><span class="label label-pill label-danger">Cerrado</span></a>';
            }
            $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"])); // Trae la fecha y la hora de cuando se creo el ticket


            if ($row["fech_cierre"] == null) { // Verificamos si esta el ticket con fecha de cierre
                $sub_array[] = '<span class="label label-pill label-default">Sin cerrar</span>';
            } else {
                $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_cierre"]));
            }

            if ($row["usu_asig"] == null) { // Verifica si el ticket esta asignado a un usuario
                $sub_array[] = '<span class="label label-pill label-warning">Sin Asignar</span>';
            } else {
                $datos1 = $usuario->get_usuario_x_id($row["usu_asig"]); // Llama a dicha funcion del modelo Usuario
                foreach ($datos1 as $row1) {
                    $sub_array[] = '<span class="label label-pill label-success">' . $row1["usu_nom"] . '</span>';
                }
            }

            $cifrado = openssl_encrypt($row["tick_id"], $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $textoCifrado = base64_encode($iv . $cifrado);

            // Cifrado del tick_id
            $sub_array[] = '<button type="button" data-ciphertext="' . $textoCifrado . '" id="' . $textoCifrado . '" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></button>';
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

    case "listar":
        $datos = $ticket->listar_ticket(); //Llama al metodo "listar_ticket" del objeto ticket con la consulta en el modelo
        $data = array(); // Declaramos un array
        foreach ($datos as $row) { // Recorre la variable datos que tiene el listado de tickets
            $sub_array = array(); // Se crea un array que almacena el tick id, cat nom, tick titulo.
            $sub_array[] = $row["tick_id"];
            $sub_array[] = $row["usu_nom"] . ' ' . $row["usu_ape"];
            $sub_array[] = $row["cat_nom"];
            $sub_array[] = $row["tick_titulo"];
            $sub_array[] = $row["prio_nom"];
            if ($row["tick_estado"] == "Abierto") { // Verificamos el estado del ticket para darle una clase
                $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
            } elseif ($row["tick_estado"] == "En Progreso") {
                $sub_array[] = '<span class="label label-pill label-warning">En Progreso</span>';
            } else {
                $sub_array[] = '<a onClick="CambiarEstado(' . $row["tick_id"] . ')"><span class="label label-pill label-danger">Cerrado</span></a>';
            }
            $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"])); // Trae la fecha y la hora de cuando se creo el ticket


            // if ($row["fech_cierre"] == null) { // Verificamos si esta el ticket con fecha de cierre
            //$sub_array[] = '<span class="label label-pill label-default">Sin cerrar</span>';
            //} else {
            //$sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_cierre"]));
            // }

            if ($row["usu_asig"] == null) { // Verifica si el ticket esta asignado a un usuario
                $sub_array[] = '<a onClick="asignar(' . $row["tick_id"] . ');"><span class="label label-pill label-warning">Sin Asignar</span></a>';
            } else {
                $datos1 = $usuario->get_usuario_x_id($row["usu_asig"]);
                foreach ($datos1 as $row1) {
                    $sub_array[] = '<span class="label label-pill label-success">' . $row1["usu_nom"] . '</span>';
                }
            }

            $sub_array[] = '<button type="button" onClick="ver(' . $row["tick_id"] . ');" id="' . $row["tick_id"] . '" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></button>'; // onClick="ver('.$row["tick_id"].');": Este es un evento que se ejecutará cuando se haga clic en el botón
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
    case "listar_filtro":
        $rol_id = $_SESSION['rol_id'];
        $usu_id = $_SESSION['usu_id'];
        $sec_id = $_SESSION['sec_id']; // Sector del usuario logueado (para los soportistas)
        $sectores = [];
        $sectores_data = $sector->get_sectores_soporte(); // o la función que tengas para traer todos
        foreach ($sectores_data as $s) {
            $sectores[$s['sec_id']] = $s['sec_nom'];
        }

        $sector_local = "Local";
        $categoria_visual = "Requerimiento Arquitectura y Visual";

        $datos = $ticket->filtrar_ticket($_POST["tick_titulo"], $_POST["cat_id"], $_POST["prio_id"]);
        $data = array();

        foreach ($datos as $row) {

$negrita = false;

// Usuario común
if ($rol_id == 1 && $row['leido_usuario'] == 0) {
    $negrita = true;
}

// Soporte REAL (rol 5)
if ($rol_id == 5 && $row['leido_soporte'] == 0) {
    $negrita = true;
}

// Admin (rol 2) → tratarlo como usuario o soporte? elegimos soporte PERO controlado
if ($rol_id == 2 && $row['leido_soporte'] == 0) {
    $negrita = true;
}

$style = $negrita ? 'style="font-weight:bold;"' : '';

            // Filtrar tickets cerrados (no se muestran)
            if ($row["tick_estado"] == "Cerrado") {
                continue;
            }

            // Rol 1 - Usuario común: solo sus propios tickets
            if ($rol_id == 1 && $row["usu_id"] != $usu_id) {
                continue;
            }

            // Rol 2 - Administrador: ve todo (sin filtro)
            // No necesita condición

            // Rol 3 - Supervisora de Locales: solo tickets del sector "Local"
            if ($rol_id == 3 && $row["sec_nom"] != $sector_local) {
                continue;
            }

            // Rol 4 - Supervisora de Visual: solo tickets con categoría "Requerimiento Arquitectura y Visual"
            if ($rol_id == 4 && $row["cat_nom"] != $categoria_visual) {
                continue;
            }

            // Rol 5 - Soportistas (Sistemas, Mantenimiento, Administración, Producto)
            // Solo ven los tickets dirigidos a su propio sector (definido por soporte_secs_id)
            $soporte_target = $row['soporte_sec_id'] ?? $row['soporte_secs_id_subcat'] ?? null;
            if ($rol_id == 5 || $rol_id == 2) {
                if (!$soporte_target || $soporte_target != $sec_id) continue;
            }


            // -------------------------------
            // Construcción del array final
            // -------------------------------
            $sub_array = array();
            $sub_array[] = '<span ' . $style . '>' . $row["tick_id"] . '</span>';
$sub_array[] = '<span ' . $style . '>' . $row["usu_nom"] . ' ' . $row["usu_ape"] . '</span>';
$sub_array[] = '<span ' . $style . '>' . $row["sec_nom"] . '</span>';
$sub_array[] = '<span ' . $style . '>' . $row["cat_nom"] . '</span>';
            // Limitar largo del título para la tabla
            $titulo = $row["tick_titulo"];
$limite = 35;

if (strlen($titulo) > $limite) {
    $titulo = substr($titulo, 0, $limite) . '...';
}

$sub_array[] = '<span ' . $style . '>' . $titulo . '</span>';

            $sub_array[] = '<span ' . $style . '>' . $row["prio_nom"] . '</span>';

            // Estado del ticket
            if ($row["tick_estado"] == "Abierto") {
                $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
            } elseif ($row["tick_estado"] == "En Progreso") {
                $sub_array[] = '<span class="label label-pill label-warning">En Progreso</span>';
            } else {
                $sub_array[] = '<a onClick="CambiarEstado(' . $row["tick_id"] . ')"><span class="label label-pill label-danger">Cerrado</span></a>';
            }

            // Fechas
            $sub_array[] = '<span ' . $style . '>' . date("d/m/Y H:i:s", strtotime($row["fech_crea"])) . '</span>';
            //$sub_array[] = $row["fech_cierre"] == null
            //? '<span class="label label-pill label-default">Sin cerrar</span>'
            //: date("d/m/Y H:i:s", strtotime($row["fech_cierre"]));

            // Asignación
            // Determinar si el usuario puede reasignar
            $puede_asignar = !in_array($rol_id, [1, 3, 4]); // soportistas y admin

            if ($row["usu_asig"] == null) {
                if (!empty($row["soporte_sec_id"])) {
                    // Mostrar el sector asignado, siempre con posibilidad de reasignar
                    $nombre_sector = $sectores[$row["soporte_sec_id"]] ?? 'Sin Sector';
                    if ($puede_asignar) {
                        $sub_array[] = '<a onClick="asignar(' . $row["tick_id"] . ');">
                <span class="label label-pill label-info">' . $nombre_sector . '</span></a>';
                    } else {
                        $sub_array[] = '<span class="label label-pill label-info">' . $nombre_sector . '</span>';
                    }
                } else {
                    // Sin asignar
                    if ($puede_asignar) {
                        $sub_array[] = '<a onClick="asignar(' . $row["tick_id"] . ');">
                <span class="label label-pill label-warning">Sin Asignar</span></a>';
                    } else {
                        $sub_array[] = '<span class="label label-pill label-warning">Sin Asignar</span>';
                    }
                }
            } else {
                // Ticket asignado a un usuario
                $datos1 = $usuario->get_usuario_x_id($row["usu_asig"]);
                foreach ($datos1 as $row1) {
                    if ($puede_asignar) {
                        $sub_array[] = '<a onClick="asignar(' . $row["tick_id"] . ');">
                <span class="label label-pill label-success">' . $row1["usu_nom"] . '</span></a>';
                    } else {
                        $sub_array[] = '<span class="label label-pill label-success">' . $row1["usu_nom"] . '</span>';
                    }
                }
            }



            // Encriptar ID
            $cifrado = openssl_encrypt($row["tick_id"], $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $textoCifrado = base64_encode($iv . $cifrado);
            $sub_array[] = '<button type="button" data-ciphertext="' . $textoCifrado . '" id="' . $textoCifrado . '" 
            class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></i></button>';

            $data[] = $sub_array;
        }

        // Resultado final
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
        break;

    case "asignar_detalle":
        $tick_id = $_POST["tick_id"] ?? null;
        $soporte_sec_id = $_POST["soporte_sec_id"] ?? null;
        $usu_asig = $_POST["usu_asig"] ?? null;

        if (empty($tick_id) || empty($soporte_sec_id)) {
            echo json_encode(["error" => "Datos incompletos"]);
            exit;
        }

        try {
            $ticket->update_ticket_asignacion($tick_id, $usu_asig, $soporte_sec_id);
            if (!empty($usu_asig)) {
                $email->ticket_asignado($tick_id);
            }
            echo json_encode(["ok" => 1]);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;


    case "comentar_cerrar":
        // 1️⃣ Insertar detalle (comentario)
        $iv_dec = substr(base64_decode($_POST["tick_id"]), 0, openssl_cipher_iv_length($cipher));
        $cifradoSinIV = substr(base64_decode($_POST["tick_id"]), openssl_cipher_iv_length($cipher));
        $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec);
        $datos = $ticket->insert_ticketdetalle($decifrado, $_POST["usu_id"], $_POST["tickd_descrip"]);

        // 2️⃣ Cerrar ticket automáticamente
$ticket->update_ticket($decifrado);

$datosUsuario = $usuario->get_usuario_x_id($_SESSION["usu_id"]);
$nomUsuario = $datosUsuario[0]["usu_nom"] . " " . $datosUsuario[0]["usu_ape"];

$datosSector = $sector->get_sector_x_id($_SESSION["sec_id"]);
$nomSector = $datosSector["sec_nom"];

$mensajeSistema =
"[SISTEMA][CIERRE]
{$nomUsuario} ({$nomSector}) cerró el ticket.";

$ticket->insert_ticketdetalle_sistema(
    $decifrado,
    $_SESSION["usu_id"],
    $mensajeSistema
);

$ticket->notify_ticket_cerrado($decifrado);

        // 3️⃣ Enviar un solo correo con comentario + aviso de cierre
        $email->ticket_comentario_cierre($decifrado, $_POST["usu_id"]);

        echo json_encode(["status" => "ok"]);
        break;



    case "listar_filtro_historial":
        $rol_id = $_SESSION['rol_id'];
        $usu_id = $_SESSION['usu_id'];
        $sec_id = $_SESSION['sec_id']; // Sector del usuario logueado (para los soportistas)
        $sectores = [];
        $sectores_data = $sector->get_sectores_soporte(); // o la función que tengas para traer todos
        foreach ($sectores_data as $s) {
            $sectores[$s['sec_id']] = $s['sec_nom'];
        }

        $sector_local = "Local";
        $categoria_visual = "Requerimiento Arquitectura y Visual";

        $datos = $ticket->filtrar_ticket($_POST["tick_titulo"], $_POST["cat_id"], $_POST["prio_id"]);
        $data = array();

        foreach ($datos as $row) {

            // Filtrar tickets cerrados (no se muestran)
            if ($row["tick_estado"] == "Abierto" || $row["tick_estado"] == "En Progreso") {
                continue;
            }

            // Rol 1 - Usuario común: solo sus propios tickets
            if ($rol_id == 1 && $row["usu_id"] != $usu_id) {
                continue;
            }

            // Rol 2 - Administrador: ve todo (sin filtro)
            // No necesita condición

            // Rol 3 - Supervisora de Locales: solo tickets del sector "Local"
            if ($rol_id == 3 && $row["sec_nom"] != $sector_local) {
                continue;
            }

            // Rol 4 - Supervisora de Visual: solo tickets con categoría "Requerimiento Arquitectura y Visual"
            if ($rol_id == 4 && $row["cat_nom"] != $categoria_visual) {
                continue;
            }

            // Rol 5 - Soportistas (Sistemas, Mantenimiento, Administración, Producto)
            // Solo ven los tickets dirigidos a su propio sector (definido por soporte_secs_id)
            $soporte_target = $row['soporte_sec_id'] ?? $row['soporte_secs_id_subcat'] ?? null;
            if ($rol_id == 5 || $rol_id == 2) {
                if (!$soporte_target || $soporte_target != $sec_id) continue;
            }


            // -------------------------------
            // Construcción del array final
            // -------------------------------
            $sub_array = array();
            $sub_array[] = $row["tick_id"];
            $sub_array[] = $row["usu_nom"] . ' ' . $row["usu_ape"];
            $sub_array[] = $row["sec_nom"];
            $sub_array[] = $row["cat_nom"];
            $sub_array[] = $row["tick_titulo"];
            $sub_array[] = $row["prio_nom"];

            // Estado del ticket
            if ($row["tick_estado"] == "Abierto") {
                $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
            } else {
                $sub_array[] = '<a onClick="CambiarEstado(' . $row["tick_id"] . ')"><span class="label label-pill label-danger">Cerrado</span></a>';
            }

            // Fechas
            $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));
            $sub_array[] = $row["fech_cierre"] == null
                ? '<span class="label label-pill label-default">Sin cerrar</span>'
                : date("d/m/Y H:i:s", strtotime($row["fech_cierre"]));

            // Asignación
            // Determinar si el usuario puede reasignar
            $puede_asignar = !in_array($rol_id, [1, 3, 4]); // soportistas y admin

            if ($row["usu_asig"] == null) {
                if (!empty($row["soporte_sec_id"])) {
                    // Mostrar el sector asignado, siempre con posibilidad de reasignar
                    $nombre_sector = $sectores[$row["soporte_sec_id"]] ?? 'Sin Sector';
                    if ($puede_asignar) {
                        $sub_array[] = '<a onClick="asignar(' . $row["tick_id"] . ');">
                <span class="label label-pill label-info">' . $nombre_sector . '</span></a>';
                    } else {
                        $sub_array[] = '<span class="label label-pill label-info">' . $nombre_sector . '</span>';
                    }
                } else {
                    // Sin asignar
                    if ($puede_asignar) {
                        $sub_array[] = '<a onClick="asignar(' . $row["tick_id"] . ');">
                <span class="label label-pill label-warning">Sin Asignar</span></a>';
                    } else {
                        $sub_array[] = '<span class="label label-pill label-warning">Sin Asignar</span>';
                    }
                }
            } else {
                // Ticket asignado a un usuario
                $datos1 = $usuario->get_usuario_x_id($row["usu_asig"]);
                foreach ($datos1 as $row1) {
                    if ($puede_asignar) {
                        $sub_array[] = '<a onClick="asignar(' . $row["tick_id"] . ');">
                <span class="label label-pill label-success">' . $row1["usu_nom"] . '</span></a>';
                    } else {
                        $sub_array[] = '<span class="label label-pill label-success">' . $row1["usu_nom"] . '</span>';
                    }
                }
            }



            // Encriptar ID
            $cifrado = openssl_encrypt($row["tick_id"], $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $textoCifrado = base64_encode($iv . $cifrado);
            $sub_array[] = '<button type="button" data-ciphertext="' . $textoCifrado . '" id="' . $textoCifrado . '" 
            class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></i></button>';

            $data[] = $sub_array;
        }

        // Resultado final
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
        break;


    case "listardetalle":

        /* Obtener el IV del texto cifrado */
        $iv_dec = substr(base64_decode($_POST["tick_id"]), 0, openssl_cipher_iv_length($cipher));
        /* Obtener el texto cifrado sin el IV */
        $cifradoSinIV = substr(base64_decode($_POST["tick_id"]), openssl_cipher_iv_length($cipher));
        /* TODO: Descifrado */
        $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec);

        $datos = $ticket->listar_ticketdetalle_x_ticket($decifrado); // Llama a la funcion "listar_ticketdetalle_x_ticket" del objeto ticket con la consulta en el modelo
        ?>
        <?php
        foreach ($datos as $row) {

$isEvento = (
    strpos($row["tickd_descrip"], "[SISTEMA][ASIGNACION]") === 0 ||
    strpos($row["tickd_descrip"], "[SISTEMA][CIERRE]") === 0 ||
    strpos($row["tickd_descrip"], "[SISTEMA][REAPERTURA]") === 0 ||
    strpos($row["tickd_descrip"], "[SISTEMA][REACCION]") === 0
);

    if ($isEvento) {
?>
    <article class="ticket-event">
        <div class="ticket-event-icon">
<?php
if (strpos($row["tickd_descrip"], "[SISTEMA][CIERRE]") === 0) {

    echo "✅";

} elseif (strpos($row["tickd_descrip"], "[SISTEMA][REAPERTURA]") === 0) {

    echo "🔓";

} elseif (strpos($row["tickd_descrip"], "[SISTEMA][REACCION][CORAZON]") === 0) {

    echo "❤️";

} elseif (strpos($row["tickd_descrip"], "[SISTEMA][REACCION][LIKE]") === 0) {

    echo "👍";

} elseif (strpos($row["tickd_descrip"], "[SISTEMA][REACCION][CELEBRACION]") === 0) {

    echo "🎉";

} elseif (strpos($row["tickd_descrip"], "[SISTEMA][REACCION][RISA]") === 0) {

    echo "😂";

} elseif (strpos($row["tickd_descrip"], "[SISTEMA][REACCION][SORPRESA]") === 0) {

    echo "😮";

} elseif (strpos($row["tickd_descrip"], "[SISTEMA][REACCION][TRISTE]") === 0) {

    echo "😢";

} else {

    echo "🔄";

}
?>
</div>

        <div class="ticket-event-body">

            <div class="ticket-event-date">
                <?php echo date("d/m/Y H:i", strtotime($row["fech_crea"])); ?>
            </div>

            <div class="ticket-event-text">
    <?php
        $texto = $row["tickd_descrip"];

$texto = str_replace("[SISTEMA][ASIGNACION]", "", $texto);
$texto = str_replace("[SISTEMA][CIERRE]", "", $texto);
$texto = str_replace("[SISTEMA][REAPERTURA]", "", $texto);

$texto = str_replace("[SISTEMA][REACCION][CORAZON]", "", $texto);
$texto = str_replace("[SISTEMA][REACCION][LIKE]", "", $texto);
$texto = str_replace("[SISTEMA][REACCION][CELEBRACION]", "", $texto);
$texto = str_replace("[SISTEMA][REACCION][RISA]", "", $texto);
$texto = str_replace("[SISTEMA][REACCION][SORPRESA]", "", $texto);
$texto = str_replace("[SISTEMA][REACCION][TRISTE]", "", $texto);

echo trim($texto);
    ?>
</div>

        </div>
    </article>
<?php
        continue;
    }

    $isSistema = ($row['rol_id'] == 2 || $row['rol_id'] == 5);
        ?>
            <article class="activity-line-item box-typical
<?php
if ($isEvento){
    echo 'ticket-event';
}elseif($isSistema){
    echo 'sistema-comment';
}else{
    echo 'usuario-comment';
}
?>">
                <div class="activity-line-date">
                    <?php echo date("d/m/Y", strtotime($row["fech_crea"])); ?>
                </div>
                <header class="activity-line-item-header">
                    <div class="activity-line-item-user">
                        <div class="activity-line-item-user-photo">
                            <a href="#">
                                <img src="../../public/<?php echo $row['rol_id'] ?>.jpg" alt="">
                            </a>
                        </div>
                        <div class="activity-line-item-user-name">
                            <?php
                            if ($isSistema) {
                                echo "💻 " . $row['usu_nom'] . ' ' . $row['usu_ape'];
                            } else {
                                echo $row['usu_nom'] . ' ' . $row['usu_ape'];
                            }
                            ?>

                        </div>
                        <div class="activity-line-item-user-status">
                            <?php echo !empty($row['sec_nom']) ? $row['sec_nom'] : 'Sin sector'; ?>
                        </div>
                    </div>
                </header>
                <div class="activity-line-action-list">
                    <section class="activity-line-action">
                        <div class="time"><?php echo date("H:i:s", strtotime($row["fech_crea"])); ?></div>
                        <div class="cont">
                            <div class="cont-in">
                                <p><?php echo $row["tickd_descrip"]; ?></p>
                                <br>
                                <?php
                                $datos_det = $documento->get_documento_detalle_x_ticket($row["tickd_id"]);
                                if (is_array($datos_det) && count($datos_det) > 0) {
                                    foreach ($datos_det as $row1) {
                                ?>
                                        <a href="../../public/document_detalle/<?php echo $row["tickd_id"] . '/' . $row1["det_nom"]; ?>" target="_blank" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-download"></i> <?php echo $row1["det_nom"]; ?></a>
                                <?php
                                    }
                                } else {
                                    //echo '<span class="label label-pill label-default">Sin Documentos</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </section>
                </div>
            </article>
        <?php
        }
        ?>

        <style>
            /* Estilo general */
            article.usuario-comment,
            article.sistema-comment {
                border: 1px solid #ddd !important;
                border-radius: 6px;
                padding: 14px;
                margin-bottom: 14px;
                background-color: #fff;
            }

            /* Usuario normal */
            article.usuario-comment {
                background-color: #fff !important;
            }

            /* Sistemas diferenciado */
            article.sistema-comment {
                background-color: #edededff !important;
                /* sutil azul grisáceo */
                border: 1px solid #bfbfbfff !important;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            }

article.ticket-event{
    background:#fff8e6 !important;
    border-left:5px solid #f0ad4e !important;
    border-top:1px solid #f3d18d;
    border-right:1px solid #f3d18d;
    border-bottom:1px solid #f3d18d;
}

article.ticket-event .activity-line-item-user-name{
    font-weight:bold;
    color:#8a6d3b;
}

article.ticket-event .cont p{
    margin:0;
    font-size:14px;
    color:#444;
}

.ticket-event{
    display:flex;
    align-items:center;

    background:#f8f9fa;

    border-left:4px solid #0d6efd;

    padding:8px 12px;
    margin-bottom:8px;

    border-radius:6px;
}

.ticket-event-icon{
    font-size:18px;
    margin-right:10px;
}

.ticket-event-date{
    font-size:11px;
    color:#888;
    margin-bottom:2px;
}

.ticket-event-text{
    font-size:13px;
    font-weight:600;
    line-height:1.3;
}
        </style>
        <?php
        break;
    case "mostrar":
        $iv_dec = substr(base64_decode($_POST["tick_id"]), 0, openssl_cipher_iv_length($cipher)); /* Obtener el IV del texto cifrado */
        $cifradoSinIV = substr(base64_decode($_POST["tick_id"]), openssl_cipher_iv_length($cipher)); /* Obtener el texto cifrado sin el IV */
        $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec); /* TODO: Descifrado */

$ticket->marcar_como_leido($decifrado, $_SESSION['rol_id']);

        $datos = $ticket->listar_ticket_x_id($decifrado);

        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["tick_id"] = $row["tick_id"];
                $output["usu_id"] = $row["usu_id"];
                $output["cat_id"] = $row["cat_id"];
                $output["tick_titulo"] = $row["tick_titulo"];
                $output["tick_descrip"] = $row["tick_descrip"];
                if ($row["tick_estado"] == "Abierto") {
                    $output["tick_estado"] = '<span class="label label-pill label-success">Abierto</span>';
                } elseif ($row["tick_estado"] == "En Progreso") {
                    $output["tick_estado"] = '<span class="label label-pill label-warning">En Progreso</span>';
                } else {
                    $output["tick_estado"] = '<span class="label label-pill label-danger">Cerrado</span>';
                };
                $output["tick_estado_texto"] = $row["tick_estado"];
                $output["fech_crea"] = date("d:m:Y H:i:s", strtotime($row["fech_crea"]));
                $output["fech_cierre"] = date("d:m:Y H:i:s", strtotime($row["fech_cierre"]));
                $output["usu_nom"] = $row["usu_nom"];
                $output["usu_ape"] = $row["usu_ape"];

$output["usu_crea"] = $row["usu_crea"];
$output["usu_crea_nom"] = $row["usu_crea_nom"];
$output["usu_crea_ape"] = $row["usu_crea_ape"];

                $output["cat_nom"] = $row["cat_nom"];
                $output["cats_nom"] = $row["cats_nom"];
                $output["prio_nom"] = $row["prio_nom"];
            }
            echo json_encode($output);
        }
        break;
    case "mostrar_noencry";

        $datos = $ticket->listar_ticket_x_id($_POST["tick_id"]);

        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["tick_id"] = $row["tick_id"];
                $output["usu_id"] = $row["usu_id"];
                $output["cat_id"] = $row["cat_id"];
                $output["tick_titulo"] = $row["tick_titulo"];
                $output["tick_descrip"] = $row["tick_descrip"];
                if ($row["tick_estado"] == "Abierto") {
                    $output["tick_estado"] = '<span class="label label-pill label-success">Abierto</span>';
                    } elseif ($row["tick_estado"] == "En Progreso") {
                    $output["tick_estado"] = '<span class="label label-pill label-warning">En Progreso</span>';
                } else {
                    $output["tick_estado"] = '<span class="label label-pill label-danger">Cerrado</span>';
                };
                $output["tick_estado_texto"] = $row["tick_estado"];
                $output["fech_crea"] = date("d:m:Y H:i:s", strtotime($row["fech_crea"]));
                $output["fech_cierre"] = date("d:m:Y H:i:s", strtotime($row["fech_cierre"]));
                $output["usu_nom"] = $row["usu_nom"];
                $output["usu_ape"] = $row["usu_ape"];
                $output["cat_nom"] = $row["cat_nom"];
                $output["cats_nom"] = $row["cats_nom"];
                $output["prio_nom"] = $row["prio_nom"];
            }
            echo json_encode($output);
        }
        break;
    case "insertdetalle":
        $iv_dec = substr(base64_decode($_POST["tick_id"]), 0, openssl_cipher_iv_length($cipher)); /* Obtener el IV del texto cifrado */
        $cifradoSinIV = substr(base64_decode($_POST["tick_id"]), openssl_cipher_iv_length($cipher)); /* Obtener el texto cifrado sin el IV */
        $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec); /* TODO: Descifrado */
        $datos = $ticket->insert_ticketdetalle($decifrado, $_POST["usu_id"], $_POST["tickd_descrip"]); //La línea de código está llamando al método insert_ticketdetalle del objeto $ticket y le pasa tres parámetros que se toman de un formulario enviado mediante el método POST
        $ticket->update_ticket_en_progreso($decifrado);
        if (is_array($datos) == true and count($datos) > 0) { //Verificamos si $datos es un array y si tiene datos
            foreach ($datos as $row) {
                $output["tickd_id"] = $row["tickd_id"]; //$output es otro arreglo, y en este caso, la clave "tickd_id" se está utilizando para almacenar un valor dentro de ese arreglo
                if (empty($_FILES['files']['name'])) { // Verificamos si llegan archivos de la vista
                    //Si no llegan archivos, no hacemos nada
                } else {
                    $countfiles = count($_FILES['files']['name']); // Cuenta los archivos
                    $ruta = "../public/document_detalle/" . $output["tickd_id"] . "/"; // Los guardamos en la siguiente ruta, crea una ruta con el mismo nombre que el tick ID recien creado
                    $files_arr = array(); // Se inicializa un array vacio para almacenar los archivos
                    if (!file_exists($ruta)) { // Si la ruta No existe, la crea. Verifica si la carpeta donde se van a subir los arhivos ya existe
                        mkdir($ruta, 0777, true); // Creamos la carpeta
                    }
                    for ($index = 0; $index < $countfiles; $index++) { // Recorremos cada archivo subido
                        $doc1 = $_FILES['files']['tmp_name'][$index]; // Obtiene el nombre del archivo en el índice actual
                        $destino = $ruta . $_FILES['files']['name'][$index]; // Define la ruta completa donde se guardará el archivo, concatenando la carpeta de destino ($ruta) con el nombre del archivo ($doc1).
                        $documento->insert_documento_detalle($output["tickd_id"], $_FILES['files']['name'][$index]); // Guardamos el ticket en la DB, Se inserta el tick_id (para asociar el archivo con el ticket) y el nombre del archivo ($doc1).
                        move_uploaded_file($doc1, $destino); //move_uploaded_fil: Mueve un archivo subido a una nueva ubicacion
                    }
                }
            }
        }
        $email->ticket_comentario($decifrado, $_POST["usu_id"]);
        echo json_encode($datos); // Convierte el arreglo en una cadena JSON
        break;
    case "total";
        $datos = $ticket->get_ticket_total();
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["TOTAL"] = $row["TOTAL"];
            }
            echo json_encode($output);
        }
        break;
    case "totalabierto";
        $datos = $ticket->get_ticket_totalabierto();
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["TOTAL"] = $row["TOTAL"];
            }
            echo json_encode($output);
        }
        break;
    case "totalcerrado";
        $datos = $ticket->get_ticket_totalcerrado();
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["TOTAL"] = $row["TOTAL"];
            }
            echo json_encode($output);
        }
        break;

    case "grafico";
        $datos = $ticket->get_ticket_grafico();
        echo json_encode($datos);
        break;

    case "all_calendar":
        $datos = $ticket->get_calendar_all();
        echo json_encode($datos); // Convierte el arreglo en una cadena JSON
        break;
    case "usu_calendar":
        $datos = $ticket->get_calendar_usu($_POST["usu_id"]);
        echo json_encode($datos); // Convierte el arreglo en una cadena JSON
        break;
    case "listar_mis_tickets":

        $usu_asig = isset($_POST["usu_asig"]) ? $_POST["usu_asig"] : '';

        $data = array();

        if ($usu_asig !== '') {

            $datos = $ticket->filtrar_ticket_x_usuario($usu_asig);

            // 🔥 FILTRAR SOLO TICKETS ABIERTOS (aplica para soportista rol 5)
            $datos = array_filter($datos, function ($row) {
    return in_array($row["tick_estado"], ["Abierto", "En Progreso"]);
});

            foreach ($datos as $row) {

                $sub_array = array();
                $sub_array[] = $row["tick_id"];
                $sub_array[] = $row["usu_nom"] . ' ' . $row["usu_ape"];
                $sub_array[] = $row["sec_nom"];
                $sub_array[] = $row["cat_nom"];
                $sub_array[] = $row["tick_titulo"];
                $sub_array[] = $row["prio_nom"];

                if ($row["tick_estado"] == "Abierto") {
    $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
} elseif ($row["tick_estado"] == "En Progreso") {
    $sub_array[] = '<span class="label label-pill label-warning">En Progreso</span>';
}

                $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));

                //$sub_array[] = '<span class="label label-pill label-default">Sin cerrar</span>';

                if ($row["usu_asig"] == null) {
                    $sub_array[] = '<a onClick="asignar(' . $row["tick_id"] . ');"><span class="label label-pill label-warning">Sin Asignar</span></a>';
                } else {
                    $datos1 = $usuario->get_usuario_x_id($row["usu_asig"]);
                    foreach ($datos1 as $row1) {
                        $sub_array[] = '<span class="label label-pill label-success">' . $row1["usu_nom"] . '</span>';
                    }
                }

                // Cifrado del ID
                if (!isset($iv) || empty($iv)) {
                    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
                }
                $cifrado = openssl_encrypt($row["tick_id"], $cipher, $key, OPENSSL_RAW_DATA, $iv);
                $textoCifrado = base64_encode($iv . $cifrado);

                $sub_array[] = '<button type="button" data-ciphertext="' . $textoCifrado . '" id="' . $textoCifrado . '" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></i></button>';

                $data[] = $sub_array;
            }
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;


    case "listar_mis_tickets2":

        $usu_asig = isset($_POST["usu_asig"]) ? $_POST["usu_asig"] : '';

        $data = array();

        if ($usu_asig !== '') {

            $datos = $ticket->listar_ticket_creado_por_usuario($usu_asig);

            // 🔥 FILTRAR SOLO TICKETS ABIERTOS (aplica para soportista rol 5)
            $datos = array_filter($datos, function ($row) {
    return in_array($row["tick_estado"], ["Abierto", "En Progreso"]);
});

            foreach ($datos as $row) {

                $sub_array = array();
                $sub_array[] = $row["tick_id"];
                $sub_array[] = $row["usu_nom"] . ' ' . $row["usu_ape"];
                $sub_array[] = $row["sec_nom"];
                $sub_array[] = $row["cat_nom"];
                $sub_array[] = $row["tick_titulo"];
                $sub_array[] = $row["prio_nom"];

                if ($row["tick_estado"] == "Abierto") {
    $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
} elseif ($row["tick_estado"] == "En Progreso") {
    $sub_array[] = '<span class="label label-pill label-warning">En Progreso</span>';
}

                $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));

                //$sub_array[] = '<span class="label label-pill label-default">Sin cerrar</span>';

                if ($row["usu_asig"] == null) {
                    $sub_array[] = '<a onClick="asignar(' . $row["tick_id"] . ');"><span class="label label-pill label-warning">Sin Asignar</span></a>';
                } else {
                    $datos1 = $usuario->get_usuario_x_id($row["usu_asig"]);
                    foreach ($datos1 as $row1) {
                        $sub_array[] = '<span class="label label-pill label-success">' . $row1["usu_nom"] . '</span>';
                    }
                }

                // Cifrado del ID
                if (!isset($iv) || empty($iv)) {
                    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
                }
                $cifrado = openssl_encrypt($row["tick_id"], $cipher, $key, OPENSSL_RAW_DATA, $iv);
                $textoCifrado = base64_encode($iv . $cifrado);

                $sub_array[] = '<button type="button" data-ciphertext="' . $textoCifrado . '" id="' . $textoCifrado . '" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></i></button>';

                $data[] = $sub_array;
            }
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;


    case "listar_mis_tickets_historial":
        // Si no viene usu_asig devolvemos vacio
        $usu_asig = isset($_POST["usu_asig"]) ? $_POST["usu_asig"] : '';

        $data = array();
        if ($usu_asig !== '') {
            $datos = $ticket->filtrar_ticket_x_usuario($usu_asig);
            foreach ($datos as $row) {
                if ($row["tick_estado"] == "Abierto") {
                    continue;
                }
                $sub_array = array();
                $sub_array[] = $row["tick_id"];
                $sub_array[] = $row["usu_nom"] . ' ' . $row["usu_ape"];
                $sub_array[] = $row["sec_nom"];
                $sub_array[] = $row["cat_nom"];
                $sub_array[] = $row["tick_titulo"];
                $sub_array[] = $row["prio_nom"];
                if ($row["tick_estado"] == "Abierto") {
                    $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
                } else {
                    $sub_array[] = '<a onClick="CambiarEstado(' . $row["tick_id"] . ')"><span class="label label-pill label-danger">Cerrado</span></a>';
                }
                $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));
                if ($row["fech_cierre"] == null) {
                    $sub_array[] = '<span class="label label-pill label-default">Sin cerrar</span>';
                } else {
                    $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_cierre"]));
                }

                if ($row["usu_asig"] == null) {
                    $sub_array[] = '<a onClick="asignar(' . $row["tick_id"] . ');"><span class="label label-pill label-warning">Sin Asignar</span></a>';
                } else {
                    $datos1 = $usuario->get_usuario_x_id($row["usu_asig"]);
                    foreach ($datos1 as $row1) {
                        $sub_array[] = '<span class="label label-pill label-success">' . $row1["usu_nom"] . '</span>';
                    }
                }

                // Asegurate que $cipher, $key, $iv estén definidos arriba del switch (como en tu controller)
                if (!isset($iv) || empty($iv)) {
                    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
                }
                $cifrado = openssl_encrypt($row["tick_id"], $cipher, $key, OPENSSL_RAW_DATA, $iv);
                $textoCifrado = base64_encode($iv . $cifrado);

                $sub_array[] = '<button type="button" data-ciphertext="' . $textoCifrado . '" id="' . $textoCifrado . '" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></i></button>';
                $data[] = $sub_array;
            };
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case "total_supervisora":
        $sec_id = $_SESSION["sec_id"]; // Sector de la supervisora
        $datos = $ticket->total_ticket_supervisora($sec_id);
        echo json_encode($datos);
        break;

    case "totalabierto_supervisora":
        $sec_id = $_SESSION["sec_id"];
        $datos = $ticket->total_abierto_supervisora($sec_id);
        echo json_encode($datos);
        break;

    case "totalcerrado_supervisora":
        $sec_id = $_SESSION["sec_id"];
        $datos = $ticket->total_cerrado_supervisora($sec_id);
        echo json_encode($datos);
        break;

    case "grafico_supervisora":
        $sec_id = $_SESSION["sec_id"];
        $datos = $ticket->grafico_supervisora($sec_id);
        echo json_encode($datos);
        break;

    case "usu_calendar_supervisora":
        $sec_id = $_SESSION["sec_id"];
        $datos = $ticket->calendar_supervisora($sec_id);
        echo json_encode($datos);
        break;
}
?>