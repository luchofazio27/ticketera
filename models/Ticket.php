<?php
class Ticket extends Conectar
{

    public function insert_ticket($usu_id, $cat_id, $tick_titulo, $tick_descrip)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "INSERT INTO tm_ticket (tick_id, usu_id, cat_id, tick_titulo, tick_descrip, tick_estado, fech_crea, est) VALUES (NULL, ?, ?, ?, ?,'Abierto',now(), '1');"; // Consulta a la DB
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_id); // stmt::bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
        $sql->bindValue(2, $cat_id);
        $sql->bindValue(3, $tick_titulo);
        $sql->bindValue(4, $tick_descrip);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function listar_ticket_x_usu($usu_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT 
        tm_ticket.tick_id,
        tm_ticket.usu_id,
        tm_ticket.cat_id,
        tm_ticket.tick_titulo,
        tm_ticket.tick_descrip,
        tm_ticket.tick_estado,
        tm_ticket.fech_crea,
        tm_usuarios.usu_nom,
        tm_usuarios.usu_ape,
        tm_categoria.cat_nom
        FROM tm_ticket 
        INNER join tm_categoria on tm_ticket.cat_id = tm_categoria.cat_id
        INNER join tm_usuarios on tm_ticket.usu_id = tm_usuarios.usu_id 
        WHERE tm_ticket.est=1 AND tm_usuarios.usu_id=?"; // Consulta a la DB
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_id); // stmt::bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function listar_ticket()
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT 
        tm_ticket.tick_id,
        tm_ticket.usu_id,
        tm_ticket.cat_id,
        tm_ticket.tick_titulo,
        tm_ticket.tick_descrip,
        tm_ticket.tick_estado,
        tm_ticket.fech_crea,
        tm_usuarios.usu_nom,
        tm_usuarios.usu_ape,
        tm_categoria.cat_nom
        FROM tm_ticket 
        INNER join tm_categoria on tm_ticket.cat_id = tm_categoria.cat_id
        INNER join tm_usuarios on tm_ticket.usu_id = tm_usuarios.usu_id 
        WHERE tm_ticket.est=1"; // Consulta a la DB
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }
}
