<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// DATOS IMAP
$mailbox = '{c1442310.ferozo.com:993/imap/ssl}INBOX';
$username = 'tickets@ticketsver.online';
$password = '1Fb5QLdL5K/i8kU';

$inbox = imap_open($mailbox, $username, $password);

if (!$inbox) {
    die("Error IMAP: " . imap_last_error());
}

echo "<h2>Conectado correctamente</h2>";

$emails = imap_search($inbox, 'ALL');

if (!$emails) {
    die("No se encontraron correos");
}

rsort($emails);

$encontrado = false;

foreach ($emails as $email_number) {

    $overview = imap_fetch_overview($inbox, $email_number, 0);

    $subject = $overview[0]->subject ?? '';

    // Buscar ticket de prueba
    if (stripos($subject, 'Ticket #307') !== false) {

        $from = $overview[0]->from ?? '';

        echo "<hr>";
        echo "<h3>ASUNTO</h3>";
        echo htmlspecialchars($subject);

        echo "<hr>";
        echo "<h3>REMITENTE</h3>";
        echo htmlspecialchars($from);

        echo "<hr>";
        echo "<h3>ESTRUCTURA MIME</h3>";

        $structure = imap_fetchstructure($inbox, $email_number);

        echo "<pre>";
        print_r($structure);
        echo "</pre>";

        echo "<hr>";
        echo "<h3>PARTE 1</h3>";

        echo "<pre>";
        echo htmlspecialchars(imap_fetchbody($inbox, $email_number, "1"));
        echo "</pre>";

        echo "<hr>";
        echo "<h3>PARTE 1.1</h3>";

        echo "<pre>";
        echo htmlspecialchars(imap_fetchbody($inbox, $email_number, "1.1"));
        echo "</pre>";

        echo "<hr>";
        echo "<h3>PARTE 1.2</h3>";

        echo "<pre>";
        echo htmlspecialchars(imap_fetchbody($inbox, $email_number, "1.2"));
        echo "</pre>";

        echo "<hr>";
        echo "<h3>PARTE 2</h3>";

        echo "<pre>";
        echo htmlspecialchars(imap_fetchbody($inbox, $email_number, "2"));
        echo "</pre>";

        echo "<hr>";
        echo "<h3>PARTE 2.1</h3>";

        echo "<pre>";
        echo htmlspecialchars(imap_fetchbody($inbox, $email_number, "2.1"));
        echo "</pre>";

        echo "<hr>";
        echo "<h3>PARTE 2.2</h3>";

        echo "<pre>";
        echo htmlspecialchars(imap_fetchbody($inbox, $email_number, "2.2"));
        echo "</pre>";

        $encontrado = true;
        break;
    }
}

if (!$encontrado) {
    echo "<h3>No se encontró ningún correo con Ticket #307</h3>";
}

imap_close($inbox);
?>