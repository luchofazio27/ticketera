<?php
require_once("../config/conexion.php"); //Conexion con DB
require_once("../models/Categoria.php"); //Conexion con el modelo Categoria
$categoria = new Categoria(); // Declaramos esa clase

switch($_GET["op"]){ //$_GET es una matriz de variables que se pasan al script actual a través de los parámetros de URL
    case "combo":
        $datos = $categoria -> get_categoria(); //Declaramos la variable datos para almacenar la consulta // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        if(is_array($datos) == true and count($datos) > 0) { //si es un array y si tiene datos
            foreach($datos as $row){ // $row es una matriz que puede contener varios valores al mismo tiempo
               $html.="<option value='".$row['cat_id']."'>".$row['cat_nom']."</option>"; //declaramos un html con dichos valores
            }
            echo $html; // Retorna el html
        }
        break;
}
?>