<?php
class Prioridad extends Conectar{
    
    public function get_prioridad(){
        $conectar=parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql ="SELECT * FROM tm_prioridad WHERE est=1";
        $sql = $conectar -> prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql -> execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql -> fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function insert_prioridad($prio_nom)
    {
        $conectar=parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "INSERT INTO tm_prioridad (prio_id, prio_nom, est) VALUES (NULL, ?, '1')";
        $sql = $conectar -> prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $prio_nom);//bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
        $sql -> execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql -> fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function update_prioridad($prio_id, $prio_nom)
    {
        {
            $conectar=parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
            parent::set_names();
            $sql = "UPDATE tm_prioridad SET prio_nom = ? WHERE prio_id = ?";
            $sql = $conectar -> prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
            $sql->bindValue(1, $prio_nom); //bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
            $sql->bindValue(2, $prio_id);
            $sql -> execute(); //->execute() es una función que ejecuta una consulta preparada previamente
            return $resultado = $sql -> fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
        }
    }

    public function delete_prioridad($prio_id)
    {
        $conectar=parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "UPDATE tm_prioridad SET est=0 WHERE prio_id = ?";
        $sql = $conectar -> prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $prio_id);
        $sql -> execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql -> fetchAll();
    }

    public function get_prioridad_x_id($prio_id)
    {
        $conectar=parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT * FROM tm_prioridad WHERE prio_id = ?";
        $sql = $conectar -> prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $prio_id);
        $sql -> execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql -> fetchAll();
    }

    public function get_prioridad_x_nom($prio_nom)
    {
        $conectar=parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT * FROM tm_prioridad WHERE prio_nom = ? AND est=1";
        $sql = $conectar -> prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $prio_nom);
        $sql -> execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql -> fetchAll();
    }
}
?>