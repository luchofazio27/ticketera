<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Datos de tu app
$client_id = "581ad5a7-6f76-49e0-9ed8-c0c36b988d68";
$tenant_id = "89151d51-690a-40ea-8982-1e6069d0ca18";
$redirect_uri = "https://ticketsver.online/ticketera/login-callback.php";
$scope = "openid profile email";

// Generamos la URL de autorización
$auth_url = "https://login.microsoftonline.com/$tenant_id/oauth2/v2.0/authorize" .
    "?client_id=$client_id" .
    "&response_type=code" .
    "&redirect_uri=" . urlencode($redirect_uri) .
    "&response_mode=query" .
    "&scope=" . urlencode($scope) .
    "&state=12345"; // podes generar un state dinámico si querés seguridad extra

// Redirigir al usuario
header("Location: $auth_url");
exit();