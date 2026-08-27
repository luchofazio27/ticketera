<?php
require_once("../config/conexion.php");
require_once("../models/Documento.php");
$documento = new Documento();

$key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
$cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)

switch ($_GET["op"]) {
    case "listar":
        $iv_dec = substr(base64_decode($_POST["tick_id"]), 0, openssl_cipher_iv_length($cipher)); /* Obtener el IV del texto cifrado */
        $cifradoSinIV = substr(base64_decode($_POST["tick_id"]), openssl_cipher_iv_length($cipher)); /* Obtener el texto cifrado sin el IV */
        $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec); /* TODO: Descifrado */
        $datos = $documento->get_documento_x_ticket($decifrado); //Llama al metodo "get_documento_x_ticket" del objeto documento con la consulta en el modelo
        $data = array(); // Declaramos un array
        foreach ($datos as $row) { // Recorre la variable datos que tiene el listado de tickets
            $sub_array = array(); // Se crea un array
            $sub_array[] = '<a href="../../public/document/'.$decifrado.'/'.$row["doc_nom"].'" target="_blank">'.$row["doc_nom"].'</a>'; // creamos un href con la ruta donde se encuentran los archivos, los busca en la carpeta document con el tick id y el nombre.
            $sub_array[] = '<a type="button" href="../../public/document/'.$decifrado.'/'.$row["doc_nom"].'" target="_blank" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></i></a>'; //Agregamos un button para ver el documento
            $data[] = $sub_array; //retornamos la data
         } 

         $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data), // Cuenta cuántos elementos hay en el arreglo
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results); // Convierte el arreglo en una cadena JSON
        break;
}
?>