<?php
class Categoria extends Conectar
{

    public function get_categoria()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT c.cat_id, c.cat_nom, s.sec_nom 
            FROM tm_categoria c
            INNER JOIN tm_sectores s ON c.sec_id = s.sec_id
            WHERE c.est = 1
            ORDER BY s.sec_nom ASC, c.cat_nom ASC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_categoria($cat_nom, $sec_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "INSERT INTO tm_categoria (cat_id, cat_nom, sec_id, est) VALUES (NULL, ?, ?, '1')";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $cat_nom); //bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
        $sql->bindValue(2, $sec_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function update_categoria($cat_id, $cat_nom, $sec_id)
    { {
            $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
            parent::set_names();
            $sql = "UPDATE tm_categoria SET cat_nom = ?, sec_id = ? WHERE cat_id = ?";
            $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
            $sql->bindValue(1, $cat_nom); //bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
            $sql->bindValue(2, $sec_id);
            $sql->bindValue(3, $cat_id);
            $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
            return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
        }
    }

    public function delete_categoria($cat_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "UPDATE tm_categoria SET est=0 WHERE cat_id = ?";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $cat_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_categoria_x_id($cat_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT * FROM tm_categoria WHERE cat_id = ?";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $cat_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_categoria_x_nom($cat_nom, $sec_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT * FROM tm_categoria WHERE cat_nom = ? AND sec_id = ? AND est=1";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $cat_nom);
        $sql->bindValue(2, $sec_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_categoria_x_sector($sec_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT cat_id, cat_nom 
            FROM tm_categoria 
            WHERE est = 1 AND sec_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sec_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
