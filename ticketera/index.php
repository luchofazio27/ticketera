<?php
require_once("config/conexion.php");

// Configuración de Microsoft Entra ID
$client_id = "581ad5a7-6f76-49e0-9ed8-c0c36b988d68";
$tenant_id = "89151d51-690a-40ea-8982-1e6069d0ca18";
$redirect_uri = "https://ticketsver.online/ticketera/login-callback.php";

$auth_url = "https://login.microsoftonline.com/$tenant_id/oauth2/v2.0/authorize" .
    "?client_id=$client_id" .
    "&response_type=code" .
    "&redirect_uri=" . urlencode($redirect_uri) .
    "&response_mode=query" .
    "&scope=" . urlencode("openid profile email User.Read");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>TICKETERA VER</title>

    <link href="public/img/logo64.png" rel="icon" type="image/png">
    <link rel="stylesheet" href="public/css/separate/pages/login.min.css">
    <link rel="stylesheet" href="public/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="public/css/lib/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/main.css">
</head>

<body>
    <div class="page-center">
        <div class="page-center-in">
            <div class="container-fluid">
                <h1 class="text-center fw-bold" style="font-weight: bold;">Ticketera VER</h1>
                <form class="sign-box" action="" method="post" id="login_form">
                    <div class="sign-avatar">
                        <img src="public/1.jpg" alt="Logo" id="imgtipo">
                    </div>
                    <header class="sign-title" id="lbltitulo">Acceso Usuario</header>

                    <!-- Botón de inicio de sesión con Microsoft 365 -->
                    <a href="<?php echo $auth_url; ?>" class="btn btn-rounded btn-primary btn-block mt-3" style="background-color:#2F2F9F; border:none;">
                        <i class="fa fa-windows"></i> Iniciar sesión con Microsoft 365
                    </a>
                </form>
            </div>
        </div>
    </div>

    <script src="public/js/lib/jquery/jquery.min.js"></script>
    <script src="public/js/lib/bootstrap/bootstrap.min.js"></script>
    <script src="public/js/app.js"></script>
</body>

</html>