<?php
class Notificacion extends Conectar
{

    public function get_notificacion_x_usu($usu_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT * FROM tm_notificacion WHERE usu_id=? AND est=2 limit 1"; //Declaramos una variable sql que contiene una consulta SQL. En este caso, selecciona todos los campos de la tabla tm_notificacion donde el usu_id coincide con el valor proporcionado y el estado (est) es igual a 2. El uso de "limit 1" asegura que solo se devuelva un registro.
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_id); //bindValue es un método que vincula un valor a un parámetro de una sentencia SQL preparada. El primer parámetro es el índice del parámetro (1 en este caso) y el segundo es el valor que se va a vincular.
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function get_notificacion_x_usu2($usu_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT * FROM tm_notificacion WHERE usu_id=? ORDER BY not_id DESC"; //Declaramos una variable sql que contiene una consulta SQL. En este caso, selecciona todos los campos de la tabla tm_notificacion donde el usu_id coincide con el valor proporcionado y el estado (est) es igual a 2. El uso de "limit 1" asegura que solo se devuelva un registro.
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_id); //bindValue es un método que vincula un valor a un parámetro de una sentencia SQL preparada. El primer parámetro es el índice del parámetro (1 en este caso) y el segundo es el valor que se va a vincular.
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function update_notificacion_estado($not_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "UPDATE tm_notificacion SET est=1 WHERE not_id=? "; //Declaramos una variable sql que contiene una consulta SQL. En este caso, selecciona todos los campos de la tabla tm_notificacion donde el usu_id coincide con el valor proporcionado y el estado (est) es igual a 2. El uso de "limit 1" asegura que solo se devuelva un registro.
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $not_id); //bindValue es un método que vincula un valor a un parámetro de una sentencia SQL preparada. El primer parámetro es el índice del parámetro (1 en este caso) y el segundo es el valor que se va a vincular.
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function update_notificacion_estado_read($not_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "UPDATE tm_notificacion SET est=0 WHERE not_id=? "; //Declaramos una variable sql que contiene una consulta SQL. En este caso, selecciona todos los campos de la tabla tm_notificacion donde el usu_id coincide con el valor proporcionado y el estado (est) es igual a 2. El uso de "limit 1" asegura que solo se devuelva un registro.
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $not_id); //bindValue es un método que vincula un valor a un parámetro de una sentencia SQL preparada. El primer parámetro es el índice del parámetro (1 en este caso) y el segundo es el valor que se va a vincular.
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function notificar_soportistas_por_ticket($tick_id, $mensaje = null)
    {
        $conectar = parent::conexion();
        parent::set_names();



        // Obtener el sector soportista del ticket
        $sql = "SELECT soporte_sec_id FROM tm_ticket WHERE tick_id = ? AND est = 1";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$tick_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['soporte_sec_id'])) {
            $soporte_sec_id = $row['soporte_sec_id'];

            // Insertar notificación a todos los usuarios con rol 5 del sector soportista
            $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                SELECT u.usu_id, CONCAT(?, ?), ?, 2
                FROM tm_usuarios u
                WHERE u.sec_id = ? AND u.rol_id = 5 AND u.est = 1";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$mensaje, $tick_id, $tick_id, $soporte_sec_id]);
        }
    }
}
