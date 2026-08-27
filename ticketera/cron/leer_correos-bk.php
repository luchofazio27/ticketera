<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config/conexion.php");
require_once("../models/Usuario.php");
require_once("../models/Ticket.php");
require_once("../models/Email.php");
require_once("../models/Documento.php");

echo "<h2>Inicio proceso</h2>";

$mailbox = '{c1442310.ferozo.com:993/imap/ssl}INBOX';
$username = 'tickets@ticketsver.online';
$password = '1Fb5QLdL5K/i8kU';

$inbox = imap_open($mailbox, $username, $password);

if (!$inbox) {
    die("Error IMAP: " . imap_last_error());
}

echo "IMAP OK<br><br>";

$emails = imap_search($inbox, 'UNSEEN');

if (!$emails) {
    die("No hay correos sin leer");
}

$usuario = new Usuario();
$ticket = new Ticket();
$email = new Email();
$documento = new Documento();

foreach ($emails as $email_number) {

    $overview = imap_fetch_overview($inbox, $email_number, 0);

    $subject = $overview[0]->subject ?? '';
    $from    = $overview[0]->from ?? '';

    echo "<hr>";
    echo "ASUNTO: " . htmlspecialchars($subject) . "<br>";
    echo "REMITENTE: " . htmlspecialchars($from) . "<br>";

    // Buscar número de ticket en asunto
    if (!preg_match('/#(\d+)/', $subject, $matches)) {

        echo "No se encontró ticket<br>";

        imap_setflag_full(
            $inbox,
            $email_number,
            "\\Seen"
        );

        continue;
    }

    $ticket_id = (int)$matches[1];

    echo "Ticket detectado: {$ticket_id}<br>";

    // Obtener correo remitente
    if (!preg_match('/<([^>]+)>/', $from, $correoMatch)) {

        echo "No se pudo obtener correo<br>";

        imap_setflag_full(
            $inbox,
            $email_number,
            "\\Seen"
        );

        continue;
    }

    $correo = strtolower(trim($correoMatch[1]));

    echo "Correo: {$correo}<br>";

    $datosUsuario = $usuario->get_usuario_x_correo($correo);

    if (empty($datosUsuario)) {

        echo "Usuario no encontrado<br>";

        imap_setflag_full(
            $inbox,
            $email_number,
            "\\Seen"
        );

        continue;
    }

    $usu_id = $datosUsuario[0]["usu_id"];

    echo "Usuario ID: {$usu_id}<br>";

    // ==========================
    // OBTENER CUERPO DEL MAIL
    // ==========================

    $estructura = imap_fetchstructure($inbox, $email_number);


    // ========================================
// OBTENER TEXTO DEL MENSAJE
// ========================================

$body = '';

// Outlook con adjuntos
if (
    isset($estructura->parts[0]) &&
    isset($estructura->parts[0]->parts[0])
) {

    $body = imap_fetchbody(
        $inbox,
        $email_number,
        '1.1'
    );

    $encoding =
        $estructura->parts[0]
                   ->parts[0]
                   ->encoding;
}
// Correo simple
else {

    $body = imap_fetchbody(
        $inbox,
        $email_number,
        1
    );

    $encoding =
        $estructura->encoding ?? 0;
}

// Decodificar
switch ($encoding) {

    case 3:
        $body = base64_decode($body);
        break;

    case 4:
        $body = quoted_printable_decode($body);
        break;
}

// Si Outlook envió el cuerpo nuevamente en Base64, decodificar una segunda vez
if (preg_match('/^[A-Za-z0-9+\/=\r\n]+$/', trim($body))) {

    $segunda = base64_decode($body, true);

    if ($segunda !== false) {
        $body = $segunda;
    }
}

    // Convertir los <br> en saltos de línea
$body = preg_replace('/<br\s*\/?>/i', "\n", $body);

// Convertir cierre de párrafos y divs en saltos
$body = preg_replace('/<\/p>/i', "\n", $body);
$body = preg_replace('/<\/div>/i', "\n", $body);

// Quitar el resto del HTML
$comentario = strip_tags($body);

// Eliminar referencias CID de imágenes
$comentario = preg_replace('/\[cid:[^\]]+\]/i', '', $comentario);

// Normalizar saltos
$comentario = str_replace("\r\n", "\n", $comentario);
$comentario = str_replace("\r", "\n", $comentario);

// Eliminar espacios al final de cada línea
$comentario = preg_replace('/[ \t]+$/m', '', $comentario);

// Eliminar líneas en blanco repetidas
$comentario = preg_replace("/\n{3,}/", "\n\n", $comentario);

$comentario = trim($comentario);

// Eliminar la firma típica de Outlook
$firma = preg_split(
    '/^(Saludos|Gracias|Atentamente|Saludos cordiales|Luciano Fazio|Dpto\. de Sistemas)/mi',
    $comentario
);

$comentario = trim($firma[0]);

$comentario = mb_convert_encoding(
    $comentario,
    'UTF-8',
    mb_detect_encoding(
        $comentario,
        'UTF-8, ISO-8859-1, Windows-1252',
        true
    )
);

    // Normalizar saltos de línea
    $comentario = str_replace("\r\n", "\n", $comentario);
    $comentario = str_replace("\r", "\n", $comentario);

// ==========================
// DETECTAR REACCIONES OUTLOOK
// ==========================

$esReaccion = false;
$tipoReaccion = "";

if (
    stripos($comentario, 'reacted to your message') !== false
) {

    $esReaccion = true;

    $nombreUsuario = trim(
        $datosUsuario[0]["usu_nom"] . " " .
        $datosUsuario[0]["usu_ape"]
    );

    $reaccion = "Reacción";

    if (stripos($comentario, '[like]') !== false) {
        $reaccion = "Me gusta";
        $tipoReaccion = "LIKE";
    }
    elseif (stripos($comentario, '[heart]') !== false) {
        $reaccion = "Corazón";
        $tipoReaccion = "HEART";
    }
    elseif (stripos($comentario, '[celebrate]') !== false) {
        $reaccion = "Celebración";
        $tipoReaccion = "CELEBRATE";
    }
    elseif (stripos($comentario, '[laugh]') !== false) {
        $reaccion = "Risa";
        $tipoReaccion = "LAUGH";
    }
    elseif (stripos($comentario, '[surprised]') !== false) {
        $reaccion = "Sorpresa";
        $tipoReaccion = "SURPRISED";
    }
    elseif (stripos($comentario, '[sad]') !== false) {
        $reaccion = "Triste";
        $tipoReaccion = "SAD";
    }

    $comentario =
        "[SISTEMA][REACCION][" . $tipoReaccion . "] " .
        $nombreUsuario .
        " reaccionó con '" . $reaccion . "' al comentario.";
}
    else {

        // ==========================
        // ELIMINAR HISTORIAL DEL MAIL
        // ==========================

        $patrones = [
    '/^De:.*$/mi',
    '/^From:.*$/mi',
    '/^Enviado:.*$/mi',
    '/^Sent:.*$/mi',
    '/^Para:.*$/mi',
    '/^To:.*$/mi',
    '/^CC:.*$/mi',
    '/^Asunto:.*$/mi',
    '/^Subject:.*$/mi',
    '/^________________________________.*$/mi'
];

foreach ($patrones as $patron) {

    if (preg_match($patron, $comentario, $m, PREG_OFFSET_CAPTURE)) {

        $comentario = substr(
            $comentario,
            0,
            $m[0][1]
        );

        break;
    }
}

$comentario = trim($comentario);
    }

    // Evitar comentarios vacíos
    if (empty(trim($comentario))) {

        echo "Comentario vacío. Se omite.<br>";

        imap_setflag_full(
            $inbox,
            $email_number,
            "\\Seen"
        );

        continue;
    }

    echo "<pre>";
    echo htmlspecialchars($comentario);
    echo "</pre>";

    // Simular usuario común
    $_SESSION['rol_id'] = 1;

// ========================================
// INSERTAR COMENTARIO O EVENTO
// ========================================

if ($esReaccion) {

    $datosDetalle = $ticket->insert_ticketdetalle_sistema(
        $ticket_id,
        $usu_id,
        $comentario
    );

} else {

    $datosDetalle = $ticket->insert_ticketdetalle(
        $ticket_id,
        $usu_id,
        $comentario,
        true
    );
}

$tickd_id = $datosDetalle[0]["tickd_id"];

    echo "Comentario insertado<br>";
}

$ticket->update_ticket_en_progreso(
    $ticket_id
);

// ========================================
// PROCESAR ADJUNTOS DEL CORREO
// ========================================

if (
    isset($estructura->parts) &&
    count($estructura->parts) > 0
) {

    foreach ($estructura->parts as $i => $parte) {

        $nombreArchivo = "";

        if (
            isset($parte->dparameters)
        ) {

            foreach ($parte->dparameters as $obj) {

                if (
                    strtolower($obj->attribute) == "filename"
                ) {

                    $nombreArchivo = $obj->value;
                }
            }
        }

        if (
            empty($nombreArchivo) &&
            isset($parte->parameters)
        ) {

            foreach ($parte->parameters as $obj) {

                if (
                    strtolower($obj->attribute) == "name"
                ) {

                    $nombreArchivo = $obj->value;
                }
            }
        }

        if (empty($nombreArchivo)) {
            continue;
        }

        echo "Adjunto encontrado: " .
             htmlspecialchars($nombreArchivo) .
             "<br>";

        $contenido = imap_fetchbody(
            $inbox,
            $email_number,
            $i + 1
        );

        switch ($parte->encoding) {

            case 3:
                $contenido = base64_decode($contenido);
                break;

            case 4:
                $contenido = quoted_printable_decode($contenido);
                break;
        }

        $ruta =
            "../public/document_detalle/" .
            $tickd_id .
            "/";

        if (!file_exists($ruta)) {

            mkdir(
                $ruta,
                0777,
                true
            );
        }

        $nombreArchivo = preg_replace(
            '/[^A-Za-z0-9._-]/',
            '_',
            $nombreArchivo
        );

        file_put_contents(
            $ruta . $nombreArchivo,
            $contenido
        );

        $documento->insert_documento_detalle(
            $tickd_id,
            $nombreArchivo
        );

        echo "Adjunto guardado<br>";
    }
}

// ========================================
// ENVIAR MAIL SOLO PARA COMENTARIOS
// ========================================

if (!$esReaccion) {

    if (
        $email->ticket_comentario(
            $ticket_id,
            $usu_id
        )
    ) {
        echo "Mail enviado<br>";
    } else {
        echo "Error enviando mail<br>";
    }

} else {

    echo "No se envía mail porque es una reacción<br>";
}

echo "Comentario insertado<br>";

    imap_setflag_full(
        $inbox,
        $email_number,
        "\\Seen"
    );

    echo "Correo marcado como leído<br>";
}

imap_close($inbox);

echo "<br><h3>Proceso finalizado</h3>";