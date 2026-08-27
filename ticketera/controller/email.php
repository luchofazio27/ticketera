<?php
require_once("../config/conexion.php");
require_once("../models/Email.php");

$email = new Email();

$key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
$cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); //Vector de inicialización (IV) necesario para el cifrado

// Verificamos que exista "op" en GET antes de usarlo
if (isset($_GET["op"])) {
    switch ($_GET["op"]) {
        case "ticket_abierto":
            if (isset($_POST["tick_id"])) {
                $email->ticket_abierto($_POST["tick_id"]);
            }
            break;

        case "ticket_cerrado":
            $iv_dec = substr(base64_decode($_POST["tick_id"]), 0, openssl_cipher_iv_length($cipher)); /* Obtener el IV del texto cifrado */
            $cifradoSinIV = substr(base64_decode($_POST["tick_id"]), openssl_cipher_iv_length($cipher)); /* Obtener el texto cifrado sin el IV */
            $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec); /* TODO: Descifrado */
            $email->ticket_cerrado($decifrado, $_POST["usu_id"]);
            break;

        case "ticket_asignado":
            if (isset($_POST["tick_id"])) {
                $email->ticket_asignado($_POST["tick_id"]);
            }
            break;

        case "recuperar_contra":
            if (isset($_POST["usu_correo"])) {
                $email->recuperar_contrasena($_POST["usu_correo"]);
            }
            break;
    }
} else {
    error_log("Llamada a controller/email.php sin parámetro 'op'");
}
