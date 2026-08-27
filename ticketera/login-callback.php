<?php
require_once("config/conexion.php");
require_once("models/Usuario.php");

// --- Cargar autoload desde include/vendor ---
require_once __DIR__ . '/include/vendor/autoload.php';

// --- Cargar .env usando phpdotenv ---
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad(); // safeLoad() no falla si no existe .env

// --- Iniciar sesión si es necesario ---
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Leer variables desde el entorno ---
$client_id     = $_ENV['CLIENT_ID'] ?? getenv('CLIENT_ID') ?? '';
$client_secret = $_ENV['CLIENT_SECRET'];
$tenant_id     = $_ENV['TENANT_ID'] ?? getenv('TENANT_ID') ?? '';
$redirect_uri  = $_ENV['REDIRECT_URI'] ?? getenv('REDIRECT_URI') ?? '';


// --- 1. Recibir código de autorización ---
if (!isset($_GET['code'])) {
    die("No se recibió código de autorización.");
}
$code = $_GET['code'];

// --- 2. Solicitar token con cURL ---
$token_url = "https://login.microsoftonline.com/$tenant_id/oauth2/v2.0/token";

$data = [
    'client_id' => $client_id,
    'scope' => 'openid email profile',
    'code' => $code,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code',
    'client_secret' => $client_secret
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode != 200) {
    die("Error obteniendo token, código HTTP: $httpcode. Respuesta: $response");
}

$response = json_decode($response, true);
$id_token = $response['id_token'] ?? null;
if (!$id_token) {
    die("No se recibió id_token.");
}

// --- 3. Decodificar JWT ---
list($header, $payload, $signature) = explode('.', $id_token);
$payload_decoded = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

$email = strtolower($payload_decoded['preferred_username'] ?? '');
$first_name = $payload_decoded['given_name'] ?? '';
$last_name  = $payload_decoded['family_name'] ?? '';
$full_name  = $payload_decoded['name'] ?? '';

// Si no tenemos nombre y apellido, tratamos de dividir "name"
if (!$first_name && !$last_name && $full_name) {
    $parts = explode(' ', $full_name, 2);
    $first_name = $parts[0] ?? '';
    $last_name  = $parts[1] ?? '';
}

// Si aún no hay nada, usamos el correo como fallback
if (!$first_name) $first_name = explode('@', $email)[0];
if (!$last_name)  $last_name = '-';

if (!$email) {
    die("No se pudo obtener el correo del usuario.");
}

// --- 4. Verificar dominio @ver.com.ar ---
if (!str_ends_with($email, '@ver.com.ar')) {
    die("Solo se permite cuentas @ver.com.ar.");
}

// --- 5. Loguear o registrar usuario ---
$usuario = new Usuario();
$exist = $usuario->getByEmail($email);

if ($exist) {
    // Usuario ya registrado
    $_SESSION['usu_id'] = $exist['usu_id'];
    $_SESSION['usu_nom'] = $exist['usu_nom'];
    $_SESSION['usu_ape'] = $exist['usu_ape'];
    $_SESSION['usu_correo'] = $exist['usu_correo'];
    $_SESSION['rol_id']     = $exist['rol_id'];
    $_SESSION['sec_id']     = $exist['sec_id'];
    
} else {
    // Registrar usuario automáticamente
    $nuevo_id = $usuario->createFromMicrosoft([
        'usu_nom'    => $first_name,
        'usu_ape'    => $last_name,
        'usu_correo' => $email,
        'usu_pass'   => '', 
        'rol_id'     => 1,
        'sec_id'     => 6
    ]);
    $_SESSION['usu_id'] = $nuevo_id;
    $_SESSION['usu_nom'] = $first_name;
    $_SESSION['usu_ape'] = $last_name;
    $_SESSION['usu_correo'] = $email;
    $_SESSION['rol_id']     = 1;
    $_SESSION['sec_id']     = 6;
}

// --- 6. Redirigir a home ---
header("Location: https://ticketsver.online/ticketera/view/ConsultarTicket/");
exit;
?>