<?php
require_once("../config/conexion.php"); //Conexion con DB
require_once("../models/Categoria.php"); //Conexion con el modelo Categoria
$categoria = new Categoria(); // Declaramos esa clase

switch($_GET["op"]){
    case "combo":
        $datos = $categoria -> get_categoria();
        if(is_array($datos) == true and count($datos) > 0) {
            
        }
}
?>