<?php
class Subcategoria extends Conectar
{

    public function get_subcategoria($cat_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT * FROM tm_subcategoria WHERE cat_id=? AND est=1";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $cat_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }
    public function get_subcategoria_all()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
        tm_subcategoria.cats_id,
        tm_subcategoria.cat_id,
        tm_subcategoria.cats_nom,
        tm_categoria.cat_nom
        FROM tm_subcategoria INNER JOIN
        tm_categoria ON tm_subcategoria.cat_id = tm_categoria.cat_id
        WHERE tm_subcategoria.est=1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* TODO:Insert */

    public function insert_subcategoria($cat_id, $cats_nom, $secs_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_subcategoria (cats_id,cat_id,cats_nom, secs_id,est) VALUES (NULL,?,?,?,'1');";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cat_id);
        $sql->bindValue(2, $cats_nom);
        $sql->bindValue(3, $secs_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    public function update_subcategoria($cats_id, $cat_id, $cats_nom, $secs_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_subcategoria set
                cat_id = ?,
                cats_nom = ?,
                secs_id = ?
                WHERE
                cats_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cat_id);
        $sql->bindValue(2, $cats_nom);
        $sql->bindValue(3, $secs_id);
        $sql->bindValue(4, $cats_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    public function delete_subcategoria($cats_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_subcategoria SET
                est = 0
                WHERE 
                cats_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cats_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    public function get_subcategoria_x_id($cats_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_subcategoria WHERE cats_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cats_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_subcategoria_x_nom($cats_nom,$cat_id)
            {
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT * FROM tm_subcategoria WHERE cats_nom = ? AND cat_id = ? AND est = 1";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $cats_nom);
        $sql->bindValue(2, $cat_id);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }
}
