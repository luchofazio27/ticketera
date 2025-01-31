<?php
class Categoria extends Conectar{
    
    public function get_categoria(){
        $conectar=parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)
        parent::set_names();
        $sql ="SELECT * FROM tm_categoria WHERE est=1";
        $sql = $conectar -> prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada
        $sql -> execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql -> fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }
}
?>