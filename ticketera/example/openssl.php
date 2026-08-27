<?php
    $data = "123456";
    /* TODO:Clave de Cifrado (asegúrate de usar una clave segura en un entorno real) */
    $key = "mi_key_secret";
    /* TODO:Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL) */
    $cipher = "aes-256-cbc";
    /* TODO: Vector de inicialización (IV) necesario para el cifrado */
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    /* TODO: Cifrado */
    $cifrado = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    /* Obtener el IV del texto cifrado */
    $textoCifrado = base64_encode($iv . $cifrado);
    /* Obtener el IV del texto cifrado */
    $iv_dec = substr(base64_decode($textoCifrado), 0, openssl_cipher_iv_length($cipher));
    /* Obtener el texto cifrado sin el IV */
    $cifradoSinIV = substr(base64_decode($textoCifrado), openssl_cipher_iv_length($cipher));
    /* TODO: Descifrado */
    $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv);

    echo "Texto Cifrado: " . $textoCifrado;

    echo " <br> Texto Decifrado: " . $decifrado;
    
?>