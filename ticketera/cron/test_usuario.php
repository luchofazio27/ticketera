<?php

require_once("../config/conexion.php");

try {

    $db = new PDO(
        "mysql:host=localhost;dbname=c1442310_tickets;charset=utf8mb4",
        "c1442310_tickets",
        "nawuDE55sa"
    );

    $mail = "Ver.Abasto@ver.com.ar";

    $sql = "SELECT usu_id, usu_nom, usu_ape, usu_correo
            FROM tm_usuarios
            WHERE usu_correo = ?";

    $stmt = $db->prepare($sql);
    $stmt->execute([$mail]);

    echo "<pre>";
    print_r($stmt->fetch(PDO::FETCH_ASSOC));
    echo "</pre>";

} catch (Exception $e) {

    echo $e->getMessage();

}