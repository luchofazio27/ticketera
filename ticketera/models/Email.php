<?php

require_once __DIR__ . '/../include/vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Llamadas a las clases que se usaran par ael envio del mail
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../models/Usuario.php';


class Email extends PHPMailer
{
    protected $gcorreo = 'AKIAZAOUM456KORKCXPU'; //correo destinatario
    protected $gcontraseña = 'BDcsdgYyL6PGKLWmDskVUDC32sgGINJSYNnNr22IljeM'; //clave destinatario

private function aliasSoporte($correo)
    {
        if (strtolower($correo) == "gonzalezm@ver.com.ar") {

            $partes = explode("@", $correo);
            $alias = $partes[0] . "+4@" . $partes[1];

            $this->addBCC($alias);
        }
    }


    public function ticket_abierto($tick_id)
    {
        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);
        foreach ($datos as $row) {
            $id = $row["tick_id"];
            $usu = $row["usu_nom"];
            $ape = $row["usu_ape"];
            $titulo = $row["tick_titulo"];
            $categoria = $row["cat_nom"];
            $descripcion = $row["tick_descrip"];
            $correo = $row["usu_correo"];
$correoSector = $row["sec_correo_ticket"];
        }

        $this->isSMTP();
        $this->Host       = 'email-smtp.us-east-1.amazonaws.com';
        $this->Port       = 587;
        $this->SMTPAuth   = true;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->Username   = $this->gcorreo;       // usuario SMTP de Amazon SES
        $this->Password   = $this->gcontraseña;   // contraseña SMTP de Amazon SES
        $this->CharSet    = 'UTF-8';

        // remitente siempre el verificado
        $this->setFrom('tickets@ticketsver.online', 'Sistema de Tickets');

        // Local que abrió el ticket (Destinatario principal)
$this->addAddress($correo);
$this->aliasSoporte($correo);

// Mantener tickets@ en copia para que las respuestas vuelvan a la ticketera
$this->addCC('tickets@ticketsver.online', 'Sistema de Tickets');

// Correos del sector (ocultos)
if (!empty($correoSector)) {

    $correos = explode(";", $correoSector);

    foreach ($correos as $mail) {
        $this->addBCC(trim($mail));
    }

}

// El Reply-To sigue siendo el local
$this->addReplyTo($correo, $usu);

        $this->isHTML(true);
        $this->Subject = 'Ticket Abierto';

        $cuerpo = file_get_contents(__DIR__ . '/../public/NuevoTicket.html');

        // Encriptar el ID del ticket igual que en insertdetalle
        $key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
        $cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); //Vector de inicialización (IV) necesario para el cifrado
        $cifrado = openssl_encrypt($id, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $ticketEncrypt = base64_encode($iv . $cifrado);
        $link = "https://ticketsver.online/ticketera/view/DetalleTicket/?ID=" . urlencode($ticketEncrypt);

        $cuerpo = str_replace("xnroticket", $id, $cuerpo);
        $cuerpo = str_replace("lblNomUsu", "$usu $ape", $cuerpo);
        $cuerpo = str_replace("lblTitu", $titulo, $cuerpo);
        $cuerpo = str_replace("lblCate", $categoria, $cuerpo);
        $cuerpo = str_replace("lblDesc", nl2br($descripcion), $cuerpo);
        $cuerpo = str_replace("lblLink", $link, $cuerpo);

        $this->Body = $cuerpo;
        $this->AltBody = strip_tags("Ticket Abierto");

        try {
            $this->Send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function ticket_cerrado($tick_id, $usu_id_comentador, $crear_y_cerrar = false)
    {
        error_log("Entré a ticket_cerrado con ID " . $tick_id);

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);

        if (!$datos || count($datos) === 0) {
            error_log("No se encontraron datos del ticket con ID: " . $tick_id);
            return false;
        }

        foreach ($datos as $row) {
            $id = $row["tick_id"];
            $usu = $row["usu_nom"];
            $ape = $row["usu_ape"];
            $titulo = $row["tick_titulo"];
            $categoria = $row["cat_nom"];
            $correo = $row["usu_correo"];
        }

        $usuario = new Usuario();
        $datos2 = $usuario->get_usuario_x_id($datos[0]["usu_asig"]); // usuario asignado
        $comentador = $usuario->get_usuario_x_id($usu_id_comentador); // usuario que comenta
        $nombreComentador = $comentador[0]["usu_nom"] . " " . $comentador[0]["usu_ape"];

        // Obtener el contenido que se mostrará como solución/comentario
$comentario = "";

// CASO ESPECIAL:
// Si el ticket fue creado y cerrado en el momento,
// la descripción original del ticket representa la solución.
if ($crear_y_cerrar) {

    $comentario = isset($datos[0]["tick_descrip"])
        ? $datos[0]["tick_descrip"]
        : "";

} else {

    // CIERRE NORMAL:
    // Buscar el último comentario real, ignorando eventos del sistema.
    $detalle = $ticket->listar_ticketdetalle_x_ticket($tick_id);

    if (is_array($detalle) && count($detalle) > 0) {

        for ($i = count($detalle) - 1; $i >= 0; $i--) {

            $descripcionDetalle = $detalle[$i]["tickd_descrip"];

            // Ignorar eventos automáticos del sistema
            if (strpos($descripcionDetalle, "[SISTEMA]") === 0) {
                continue;
            }

            // Encontramos el último comentario real
            $comentario = $descripcionDetalle;
            break;
        }
    }
}

// Limpiar HTML y conservar saltos de línea
$comentario_limpio = nl2br(strip_tags($comentario));

        // SMTP SES
        $this->isSMTP();
        $this->Host       = 'email-smtp.us-east-1.amazonaws.com';
        $this->Port       = 587;
        $this->SMTPAuth   = true;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->Username   = $this->gcorreo;
        $this->Password   = $this->gcontraseña;
        $this->CharSet    = 'UTF-8';

        // remitente verificado
        $this->setFrom('tickets@ticketsver.online', 'Sistema de Tickets');

        // destinatario
        $this->addAddress($correo);
$this->aliasSoporte($correo);
        $this->addReplyTo($correo, $usu);

        $this->isHTML(true);
        $this->Subject = "Ticket Cerrado #" . $id;

        $cuerpo = file_get_contents(__DIR__ . '/../public/CerradoTicket.html');
        if ($cuerpo === false) {
            error_log("No se pudo cargar la plantilla CerradoTicket.html");
            return false;
        }

        // Encriptar el ID del ticket igual que en insertdetalle
        $key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
        $cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); //Vector de inicialización (IV) necesario para el cifrado
        $cifrado = openssl_encrypt($id, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $ticketEncrypt = base64_encode($iv . $cifrado);
        $link = "https://ticketsver.online/ticketera/view/DetalleTicket/?ID=" . urlencode($ticketEncrypt);

        $cuerpo = str_replace("xnroticket", $id, $cuerpo);
        $cuerpo = str_replace("lblNomUsu", "$usu $ape", $cuerpo);
        $cuerpo = str_replace("lblTitu", $titulo, $cuerpo);
        $cuerpo = str_replace("lblCate", $categoria, $cuerpo);
        $cuerpo = str_replace("lblComentario", $comentario_limpio, $cuerpo);
        $cuerpo = str_replace("lblLink", $link, $cuerpo);
        $cuerpo = str_replace("lblComentador", htmlspecialchars($nombreComentador), $cuerpo);

        $this->Body    = $cuerpo;
        $this->AltBody = strip_tags("Ticket Cerrado #$id");

        try {
            $this->send();
            error_log("Correo enviado correctamente (ticket_cerrado) - ID: " . $tick_id);
            return true;
        } catch (Exception $e) {
            error_log("Error PHPMailer en " . __FUNCTION__ . ": " . $this->ErrorInfo);
            return false;
        }
    }


    public function ticket_asignado($tick_id)
    {
        error_log("Entré a ticket_asignado con ID " . $tick_id);

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);

        if (!$datos || count($datos) === 0) {
            error_log("No se encontraron datos del ticket con ID: " . $tick_id);
            return false;
        }

        foreach ($datos as $row) {
            $id = $row["tick_id"];
            $usu = $row["usu_nom"];
            $ape = $row["usu_ape"];
            $titulo = $row["tick_titulo"];
            $categoria = $row["cat_nom"];
            $correo = $row["usu_correo"];
        }

        $usuario = new Usuario();
        $datos2 = $usuario->get_usuario_x_id($datos[0]["usu_asig"]);
        $asigCorreo = (!empty($datos2[0]["usu_correo"])) ? $datos2[0]["usu_correo"] : null;

        // SMTP SES
        $this->isSMTP();
        $this->Host       = 'email-smtp.us-east-1.amazonaws.com';
        $this->Port       = 587;
        $this->SMTPAuth   = true;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->Username   = $this->gcorreo;
        $this->Password   = $this->gcontraseña;
        $this->CharSet    = 'UTF-8';

        // remitente verificado
        $this->setFrom('tickets@ticketsver.online', 'Sistema de Tickets');

        // destinatarios
        $this->addAddress($correo); // creador
$this->aliasSoporte($correo);
        if ($asigCorreo) $this->addAddress($asigCorreo); // asignado
$this->aliasSoporte($asigCorreo);

        // reply-to para que las respuestas vayan al creador
        $this->addReplyTo($correo, $usu);

        $this->isHTML(true);
        $this->Subject = "Ticket Asignado #" . $id;

        $cuerpo = file_get_contents(__DIR__ . '/../public/AsignarTicket.html');
        if ($cuerpo === false) {
            error_log("No se pudo cargar la plantilla AsignarTicket.html");
            return false;
        }

        // Encriptar el ID del ticket igual que en insertdetalle
        $key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
        $cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); //Vector de inicialización (IV) necesario para el cifrado
        $cifrado = openssl_encrypt($id, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $ticketEncrypt = base64_encode($iv . $cifrado);
        $link = "https://ticketsver.online/ticketera/view/DetalleTicket/?ID=" . urlencode($ticketEncrypt);

        $cuerpo = str_replace("xnroticket", $id, $cuerpo);
        $cuerpo = str_replace("lblNomUsu", "$usu $ape", $cuerpo);
        $cuerpo = str_replace("lblTitu", $titulo, $cuerpo);
        $cuerpo = str_replace("lblCate", $categoria, $cuerpo);
        $cuerpo = str_replace("lblLink", $link, $cuerpo);

        $this->Body    = $cuerpo;
        $this->AltBody = strip_tags("Ticket Asignado #$id");

        try {
            $this->send();
            error_log("Correo enviado correctamente (ticket_asignado) - ID: " . $tick_id);
            return true;
        } catch (Exception $e) {
            error_log("Error PHPMailer en " . __FUNCTION__ . ": " . $this->ErrorInfo);
            return false;
        }
    }


    public function recuperar_contrasena($usu_correo)
    {
        error_log("Entré a recuperar_contrasena con correo: " . $usu_correo);

        $usuario = new Usuario();

        // Generar nueva contraseña temporal y actualizar en DB
        $usuario->get_cambiar_contra_recuperar($usu_correo);

        // Obtener datos del usuario
        $datos = $usuario->get_usuario_x_correo($usu_correo);
        if (!$datos || count($datos) === 0) {
            error_log("No se encontró usuario con el correo: " . $usu_correo);
            return false;
        }

        foreach ($datos as $row) {
            $usu_id  = $row["usu_id"];
            $usu_ape = $row["usu_ape"];
            $usu_nom = $row["usu_nom"];
            $correo  = $row["usu_correo"];
            $usu_pass = $row["usu_pass"];
        }

        // --- CONFIGURACIÓN SMTP (STARTTLS / puerto 587) ---
        $this->isSMTP();
        $this->Host       = 'email-smtp.us-east-1.amazonaws.com';
        $this->Port       = 587; // STARTTLS
        $this->SMTPAuth   = true;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->Username   = $this->gcorreo;
        $this->Password   = $this->gcontraseña;
        $this->CharSet    = 'UTF-8';

        // --- REMITENTE ---
        $this->setFrom($this->gcorreo, 'Recuperación de Contraseña');

        // --- DESTINATARIO ---
        $this->addAddress($usu_correo);
$this->aliasSoporte($usu_correo);

        // --- CONTENIDO DEL CORREO ---
        $this->isHTML(true);
        $this->Subject = "Recuperar Contraseña - Sistema de Tickets";

        $cuerpo = file_get_contents(__DIR__ . '/../public/RecuperarContra.html');
        if ($cuerpo === false) {
            error_log("No se pudo cargar la plantilla RecuperarContra.html");
            return false;
        }

        // Reemplazar placeholders del HTML
        $cuerpo = str_replace("xusunom", $usu_nom, $cuerpo);
        $cuerpo = str_replace("xusuape", $usu_ape, $cuerpo);
        $cuerpo = str_replace("xnuevopass", $usu_pass, $cuerpo);

        $this->Body    = $cuerpo;
        $this->AltBody = strip_tags("Recuperar Contraseña - Sistema de Tickets");

        // --- ENVÍO ---
        try {
            $this->send();
            error_log("Correo enviado correctamente (recuperar_contrasena) a " . $usu_correo);

            // Encriptar la nueva contraseña después del envío exitoso
            $usuario->encriptar_nueva_contra($usu_id, $usu_pass);

            return true;
        } catch (Exception $e) {
            error_log("Error PHPMailer en " . __FUNCTION__ . ": " . $this->ErrorInfo);
            return false;
        }
    }


    public function ticket_comentario($tick_id, $usu_id_comentador)
    {
        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);
        foreach ($datos as $row) {
            $id = $row["tick_id"];
            $usu = $row["usu_nom"];
            $titulo = $row["tick_titulo"];
            $categoria = $row["cat_nom"];
            $correo = $row["usu_correo"];
        }

        $usuario = new Usuario();
        $datos2 = $usuario->get_usuario_x_id($datos[0]["usu_asig"]); // usuario asignado
        $comentador = $usuario->get_usuario_x_id($usu_id_comentador); // usuario que comenta
        $nombreComentador = $comentador[0]["usu_nom"] . " " . $comentador[0]["usu_ape"];

        // Traer el último comentario del ticket
        $detalle = $ticket->listar_ticketdetalle_x_ticket($tick_id);
        $comentario = "";
        if (is_array($detalle) && count($detalle) > 0) {
            $ultimo = end($detalle);
            $comentario = $ultimo["tickd_descrip"];
        }

        // Limpiar HTML y conservar saltos de línea
        $comentario_limpio = nl2br(strip_tags($comentario));

        // Encriptar el ID del ticket igual que en insertdetalle
        $key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
        $cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); //Vector de inicialización (IV) necesario para el cifrado
        $cifrado = openssl_encrypt($id, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $ticketEncrypt = base64_encode($iv . $cifrado);

        $link = "https://ticketsver.online/ticketera/view/DetalleTicket/?ID=" . urlencode($ticketEncrypt);

        // Configurar PHPMailer
        $this->isSMTP();
        $this->Host       = 'email-smtp.us-east-1.amazonaws.com';
        $this->Port       = 587;
        $this->SMTPAuth   = true;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->Username   = $this->gcorreo;
        $this->Password   = $this->gcontraseña;
        $this->CharSet    = 'UTF-8';

        $this->setFrom('tickets@ticketsver.online', 'Sistema de Tickets');
//$this->addReplyTo('sistemas@ver.com.ar', 'Sistemas');
        
        // Correos del creador y del asignado
$correoCreador = $correo;
$nombreCreador = $usu;

$correoAsignado = $datos2[0]["usu_correo"];
$nombreAsignado = $datos2[0]["usu_nom"] . " " . $datos2[0]["usu_ape"];

// --- DESTINATARIOS ---
// Enviar al creador si NO es quien comenta
if ($datos[0]["usu_id"] != $usu_id_comentador) {
    $this->addAddress($correoCreador, $nombreCreador);
$this->aliasSoporte($correoCreador);
}

// Enviar al asignado si NO es quien comenta
if ($datos[0]["usu_asig"] != $usu_id_comentador) {
    $this->addAddress($correoAsignado, $nombreAsignado);
$this->aliasSoporte($correoAsignado);
}


        $this->isHTML(true);
        $this->Subject = 'Nuevo Comentario en el Ticket #' . $id;

        // Armar el cuerpo
        $cuerpo = file_get_contents(__DIR__ . '/../public/ComentarioTicket.html');
        $cuerpo = str_replace("xnroticket", $id, $cuerpo);
        $cuerpo = str_replace("lblNomUsu", $usu, $cuerpo);
        $cuerpo = str_replace("lblTitu", $titulo, $cuerpo);
        $cuerpo = str_replace("lblCate", $categoria, $cuerpo);
        $cuerpo = str_replace("lblComentario", $comentario_limpio, $cuerpo);
        $cuerpo = str_replace("lblLink", $link, $cuerpo);
        $cuerpo = str_replace("lblComentador", htmlspecialchars($nombreComentador), $cuerpo);


        $this->Body = $cuerpo;
        $this->AltBody = strip_tags("Nuevo comentario en el ticket");

        try {
            $this->Send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function ticket_reabierto($tick_id, $usu_id_reabriendo)
    {
        $ticket = new Ticket();
        $usuario = new Usuario();

        // Datos del ticket
        $datos = $ticket->listar_ticket_x_id($tick_id);
        foreach ($datos as $row) {
            $id = $row["tick_id"];
            $usu = $row["usu_nom"];         // creador del ticket
            $titulo = $row["tick_titulo"];
            $categoria = $row["cat_nom"];
            $correoCreador = $row["usu_correo"];
            $usu_asig = $row["usu_asig"];   // quien lo tenía asignado
        }

        // Datos del usuario que reabre el ticket
        $reabriendo = $usuario->get_usuario_x_id($usu_id_reabriendo);
        $nombreReabriendo = $reabriendo[0]["usu_nom"] . " " . $reabriendo[0]["usu_ape"];

        // Datos del usuario que cerró el ticket (es el asignado)
        $cerrador = $usuario->get_usuario_x_id($usu_asig);
        $correoCerrador = $cerrador[0]["usu_correo"];
        $nombreCerrador = $cerrador[0]["usu_nom"] . " " . $cerrador[0]["usu_ape"];

        // Generar link encriptado al ticket
        $key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
        $cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); //Vector de inicialización (IV) necesario para el cifrado
        $cifrado = openssl_encrypt($id, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $ticketEncrypt = base64_encode($iv . $cifrado);
        $link = "https://ticketsver.online/ticketera/view/DetalleTicket/?ID=" . urlencode($ticketEncrypt);

        // Configuración SMTP Amazon SES
        $this->isSMTP();
        $this->Host       = 'email-smtp.us-east-1.amazonaws.com';
        $this->Port       = 587;
        $this->SMTPAuth   = true;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->Username   = $this->gcorreo;
        $this->Password   = $this->gcontraseña;
        $this->CharSet    = 'UTF-8';

        // Remitente verificado
        $this->setFrom('tickets@ticketsver.online', 'Sistema de Tickets');
        $this->addAddress($correoCerrador); // le llega al usuario que cerró
$this->aliasSoporte($correoCerrador);
        $this->addReplyTo($correoCreador, $usu); // opcional, para que pueda responderle

        // Asunto y cuerpo
        $this->isHTML(true);
        $this->Subject = "Ticket Reabierto";

        $cuerpo = file_get_contents(__DIR__ . '/../public/ReabiertoTicket.html');
        $cuerpo = str_replace("xnroticket", $id, $cuerpo);
        $cuerpo = str_replace("lblNomUsu", $usu, $cuerpo);
        $cuerpo = str_replace("lblTitu", $titulo, $cuerpo);
        $cuerpo = str_replace("lblCate", $categoria, $cuerpo);
        $cuerpo = str_replace("lblReabriendo", htmlspecialchars($nombreReabriendo), $cuerpo);
        $cuerpo = str_replace("lblLink", htmlspecialchars($link), $cuerpo);

        $this->Body = $cuerpo;
        $this->AltBody = strip_tags("El ticket $id ha sido reabierto por $nombreReabriendo.");

        try {
            $this->Send();
            return true;
        } catch (Exception $e) {
            error_log("Error PHPMailer en ticket_reabierto: " . $this->ErrorInfo);
            return false;
        }
    }


    public function ticket_comentario_cierre($tick_id, $usu_id_comentador, $usar_descripcion_ticket = false)
    {
        error_log("Entré a ticket_comentario_cierre con ID " . $tick_id);

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);

        if (!$datos || count($datos) === 0) {
            error_log("No se encontraron datos del ticket con ID: " . $tick_id);
            return false;
        }

        // datos del ticket y creador
        foreach ($datos as $row) {
            $id = $row["tick_id"];
            $usu = $row["usu_nom"];
            $usu_ape = isset($row["usu_ape"]) ? $row["usu_ape"] : '';
            $titulo = $row["tick_titulo"];
            $categoria = $row["cat_nom"];
            $descripcion = isset($row["tick_descrip"]) ? $row["tick_descrip"] : '';
            $correo = $row["usu_correo"]; // destinatario principal (creador)
        }

        // usuario asignado (por si hace falta)
        $usuario = new Usuario();
        $datos2 = $usuario->get_usuario_x_id($datos[0]["usu_asig"]);
        $asigCorreo = (!empty($datos2[0]["usu_correo"])) ? $datos2[0]["usu_correo"] : null;

// Obtener el comentario/solución que se mostrará en el correo
$comentario_plain = "";

if ($usar_descripcion_ticket) {

    // En "Crear y cerrar", la descripción original
    // del ticket es también la solución/detalle del cierre.
    $comentario_plain = isset($datos[0]["tick_descrip"])
        ? $datos[0]["tick_descrip"]
        : "";

} else {

    // Flujo normal: buscar el último comentario REAL del cierre,
    // ignorando los eventos automáticos de SISTEMA.
    $detalle = $ticket->listar_ticketdetalle_x_ticket($tick_id);

    if (is_array($detalle) && count($detalle) > 0) {

        // Recorrer desde el último hacia el primero
        for ($i = count($detalle) - 1; $i >= 0; $i--) {

            $descripcion = $detalle[$i]["tickd_descrip"];

            // Ignorar eventos automáticos del sistema
            if (strpos($descripcion, "[SISTEMA]") === 0) {
                continue;
            }

            // Encontramos el último comentario real
            $comentario_plain = $descripcion;
            break;
        }
    }
}

        // quien comentó (nombre + apellido)
        $comentador = $usuario->get_usuario_x_id($usu_id_comentador);
        $nombreComentador = ($comentador && isset($comentador[0]["usu_nom"]))
            ? $comentador[0]["usu_nom"] . ' ' . $comentador[0]["usu_ape"]
            : '(Usuario)';

        // limpiar y preparar HTML del comentario
        $comentario_html = nl2br(htmlspecialchars(strip_tags($comentario_plain), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

        // generar link encriptado (misma lógica que en ticket_comentario())
        $key = "mi_key_secret";
        $cipher = "aes-256-cbc";
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $cifrado = openssl_encrypt($id, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $ticketEncrypt = base64_encode($iv . $cifrado);
        $link = "https://ticketsver.online/ticketera/view/DetalleTicket/?ID=" . urlencode($ticketEncrypt);

        // --- CONFIGURACION PHPMailer (SES) ---
        $this->isSMTP();
        $this->Host       = 'email-smtp.us-east-1.amazonaws.com';
        $this->Port       = 587;
        $this->SMTPAuth   = true;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->Username   = $this->gcorreo;
        $this->Password   = $this->gcontraseña;
        $this->CharSet    = 'UTF-8';

        // remitente verificado
        $this->setFrom('tickets@ticketsver.online', 'Sistema de Tickets');

        // destinatario: creador del ticket (principal)
        $this->addAddress($correo);
$this->aliasSoporte($correo);

        // opcional: agregar copia al asignado si querés
        if ($asigCorreo) {
            $this->addAddress($asigCorreo);
$this->aliasSoporte($asigCorreo);
        }

        // reply-to hacia quien comentó
        $this->addReplyTo($comentador[0]["usu_correo"] ?? $correo, $nombreComentador);

        $this->isHTML(true);
        $this->Subject = "Ticket Cerrado con Comentario - #" . $id;

        // cargar plantilla y reemplazar placeholders (usa exactamente los mismos tags)
        $cuerpo = file_get_contents(__DIR__ . '/../public/CerrarComentarioTicket.html');
        if ($cuerpo === false) {
            error_log("No se pudo cargar la plantilla CerrarComentarioTicket.html");
            return false;
        }

        $cuerpo = str_replace("xnroticket", $id, $cuerpo);
        $cuerpo = str_replace("lblNomUsu", $usu . ($usu_ape ? " " . $usu_ape : ""), $cuerpo);
        $cuerpo = str_replace("lblTitu", $titulo, $cuerpo);
        $cuerpo = str_replace("lblCate", $categoria, $cuerpo);
        $cuerpo = str_replace("lblComentario", $comentario_html, $cuerpo);
        $cuerpo = str_replace("lblComentador", htmlspecialchars($nombreComentador, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), $cuerpo);
        $cuerpo = str_replace("lblLink", $link, $cuerpo);

        $this->Body = $cuerpo;

        // AltBody en texto plano
        $this->AltBody = "Ticket Cerrado #$id - Comentario: " . strip_tags($comentario_plain);

        try {
            $this->send();
            error_log("Correo enviado correctamente (ticket_comentario_cierre) - ID: " . $tick_id);
            return true;
        } catch (Exception $e) {
            error_log("Error PHPMailer en ticket_comentario_cierre: " . $this->ErrorInfo);
            return false;
        }
    }
}