<?php
class Ticket extends Conectar
{

    public function insert_ticket(
    $usu_id,
    $cat_id,
    $cats_id,
    $tick_titulo,
    $tick_descrip,
    $prio_id,
    $soporte_sec_id,
    $usu_crea
)
    {
        $conectar = parent::conexion();
        parent::set_names();

        // 🔹 1) Insertar el ticket nuevo
        $sql = "INSERT INTO tm_ticket 
(
    tick_id,
    usu_id,
    usu_crea,
    cat_id,
    cats_id,
    tick_titulo,
    tick_descrip,
    tick_estado,
    fech_crea,
    usu_asig,
    soporte_sec_id,
    fech_asig,
    prio_id,
    est,
    leido_usuario,
    leido_soporte
) 
VALUES (
    NULL,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    'Abierto',
    NOW(),
    NULL,
    ?,
    NULL,
    ?,
    '1',
    0,
    0
);";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $usu_id);
$stmt->bindValue(2, $usu_crea);
$stmt->bindValue(3, $cat_id);
$stmt->bindValue(4, $cats_id);
$stmt->bindValue(5, $tick_titulo);
$stmt->bindValue(6, $tick_descrip);
$stmt->bindValue(7, $soporte_sec_id);
$stmt->bindValue(8, $prio_id);
        try {
    $stmt->execute();
} catch (PDOException $e) {
    die("ERROR INSERT TICKET: " . $e->getMessage());
}

        // 🔹 2) Obtener el ID del ticket recién creado
        $stmt1 = $conectar->prepare("SELECT LAST_INSERT_ID() AS tick_id");
        $stmt1->execute();
        $resultado = $stmt1->fetch(PDO::FETCH_ASSOC);
        $tick_id = $resultado['tick_id'];

        // 🔹 3) Notificar a todos los ADMINISTRADORES (rol_id = 2)
        $sql_admin = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                  SELECT u.usu_id, CONCAT('Nuevo ticket creado. Ticket Nro: ', :tick_id), :tick_id, 2
                  FROM tm_usuarios u
                  WHERE u.rol_id = 2 AND u.est = 1;";
        $stmt_admin = $conectar->prepare($sql_admin);
        $stmt_admin->bindValue(':tick_id', $tick_id);
        $stmt_admin->execute();

        // 🔹 4) Notificar a todos los SOPORTISTAS del sector asignado (rol_id = 5)
        $sql_soporte = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                    SELECT u.usu_id, CONCAT('Nuevo ticket asignado a su sector. Ticket Nro: ', :tick_id), :tick_id, 2
                    FROM tm_usuarios u
                    WHERE u.rol_id = 5 AND u.sec_id = :sec_id AND u.est = 1;";
        $stmt_soporte = $conectar->prepare($sql_soporte);
        $stmt_soporte->bindValue(':tick_id', $tick_id);
        $stmt_soporte->bindValue(':sec_id', $soporte_sec_id);
        $stmt_soporte->execute();

        // 🔹 5) Retornar el ticket creado
        return [["tick_id" => $tick_id]];
    }

public function listar_ticket_x_usu($usu_id)
{
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "SELECT 
                t.tick_id,
                t.usu_id,
                u.usu_nom,
                u.usu_ape,
                s.sec_nom,
                c.cat_nom,
                p.prio_nom,
                t.tick_titulo,
                t.tick_estado,
                t.usu_asig,
                t.fech_crea,
                t.fech_cierre,
                t.soporte_sec_id,
                t.leido_usuario,
                t.leido_soporte,

                COALESCE(
                    ult.ultimo_movimiento,
                    t.fech_crea
                ) AS ultimo_movimiento

            FROM tm_ticket t

            INNER JOIN tm_usuarios u 
                ON t.usu_id = u.usu_id

            INNER JOIN tm_sectores s 
                ON u.sec_id = s.sec_id

            INNER JOIN tm_categoria c 
                ON t.cat_id = c.cat_id

            INNER JOIN tm_prioridad p 
                ON t.prio_id = p.prio_id

            LEFT JOIN (
                SELECT 
                    tick_id,
                    MAX(fech_crea) AS ultimo_movimiento
                FROM td_ticketdetalle
                WHERE est = 1
                GROUP BY tick_id
            ) ult
                ON ult.tick_id = t.tick_id

            WHERE 
                t.usu_id = ?
                AND t.est = 1

            ORDER BY 
                COALESCE(ult.ultimo_movimiento, t.fech_crea) DESC,
                t.tick_id DESC";

    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $usu_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        tm_ticket.fech_cierre,
        tm_ticket.usu_asig,
        tm_ticket.fech_asig,
        tm_usuarios.usu_nom,
        tm_usuarios.usu_ape,
        tm_categoria.cat_nom,
        tm_ticket.prio_id,
        tm_prioridad.prio_nom
        FROM tm_ticket 
        INNER join tm_categoria on tm_ticket.cat_id = tm_categoria.cat_id
        INNER join tm_usuarios on tm_ticket.usu_id = tm_usuarios.usu_id
        INNER join tm_prioridad on tm_ticket.prio_id = tm_prioridad.prio_id
        WHERE tm_ticket.est=1"; // Consulta a la DB
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function listar_ticketdetalle_x_ticket($tick_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT
        td_ticketdetalle.tickd_id,
        td_ticketdetalle.tickd_descrip,
        td_ticketdetalle.fech_crea,
        tm_usuarios.usu_nom,
        tm_usuarios.usu_ape,
        tm_usuarios.rol_id,
        tm_sectores.sec_nom
        FROM
        td_ticketdetalle
        INNER JOIN tm_usuarios on td_ticketdetalle.usu_id = tm_usuarios.usu_id
        INNER JOIN tm_sectores on tm_usuarios.sec_id = tm_sectores.sec_id
        WHERE
        tick_id=?
        ORDER BY td_ticketdetalle.fech_crea ASC, td_ticketdetalle.tickd_id ASC"; // Consulta a la DB
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $tick_id); // stmt::bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function listar_ticket_x_id($tick_id)
{
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "SELECT 
        tm_ticket.tick_id,
        tm_ticket.usu_id,
        tm_ticket.usu_crea,
        tm_ticket.cat_id,
        tm_ticket.cats_id,
        tm_ticket.tick_titulo,
        tm_ticket.tick_descrip,
        tm_ticket.tick_estado,
        tm_ticket.fech_crea,
        tm_ticket.fech_cierre,
        tm_ticket.usu_asig,
        tm_ticket.soporte_sec_id,

        -- SOLICITANTE
        u_solicitante.usu_nom AS usu_nom,
        u_solicitante.usu_ape AS usu_ape,
        u_solicitante.usu_correo AS usu_correo,

        -- CREADOR
        u_creador.usu_nom AS usu_crea_nom,
        u_creador.usu_ape AS usu_crea_ape,

        tm_categoria.cat_nom,
        tm_subcategoria.cats_nom,

        tm_ticket.prio_id,
        tm_prioridad.prio_nom,

        tm_sectores.sec_nom AS soporte_sec_nom,
        tm_sectores.sec_correo_ticket

    FROM tm_ticket

    LEFT JOIN tm_categoria 
        ON tm_ticket.cat_id = tm_categoria.cat_id

    LEFT JOIN tm_subcategoria 
        ON tm_ticket.cats_id = tm_subcategoria.cats_id

    -- Usuario que recibe el soporte
    LEFT JOIN tm_usuarios u_solicitante
        ON tm_ticket.usu_id = u_solicitante.usu_id

    -- Usuario que creó/cargó el ticket
    LEFT JOIN tm_usuarios u_creador
        ON tm_ticket.usu_crea = u_creador.usu_id

    LEFT JOIN tm_prioridad 
        ON tm_ticket.prio_id = tm_prioridad.prio_id

    LEFT JOIN tm_sectores 
        ON tm_ticket.soporte_sec_id = tm_sectores.sec_id

    WHERE
        tm_ticket.est = 1
        AND tm_ticket.tick_id = ?";

    $sql = $conectar->prepare($sql);
    $sql->bindValue(1, $tick_id);
    $sql->execute();

    return $resultado = $sql->fetchAll();
}

    public function insert_ticketdetalle($tick_id, $usu_id, $tickd_descrip, $suppress_notifications = false)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);
        foreach ($datos as $row) {
            $usu_asig = $row['usu_asig'];        // usuario asignado
            $usu_crea = $row['usu_id'];          // creador del ticket
            $soporte_sec_id = $row['soporte_sec_id']; // sector soportista original
        }

        // Si no suprimimos notificaciones, crear las que correspondan (comportamiento actual)
        if (!$suppress_notifications) {
            if ($_SESSION['rol_id'] == 1) {
                if (!empty($usu_asig)) {
                    $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                        VALUES ($usu_asig, CONCAT('Tiene una nueva respuesta del usuario en el ticket Nro: ', $tick_id), $tick_id, 2)";
                    $conectar->prepare($sql)->execute();
                } elseif (!empty($soporte_sec_id)) {
                    $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                        SELECT usu_id, CONCAT('Nuevo comentario del usuario en ticket Nro: ', $tick_id), $tick_id, 2
                        FROM tm_usuarios WHERE sec_id = $soporte_sec_id AND est = 1";
                    $conectar->prepare($sql)->execute();
                }
            } else {
                if (!empty($usu_crea)) {
                    $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                        VALUES ($usu_crea, CONCAT('Tiene una nueva respuesta de soporte en el ticket Nro: ', $tick_id), $tick_id, 2)";
                    $conectar->prepare($sql)->execute();
                }
            }

            // Notificar a soportistas del sector
            require_once("../models/Notificacion.php");
            $noti = new Notificacion();
            // Solo notificar al sector si quien responde/cierra es un usuario común
            if ($_SESSION['rol_id'] == 1) {
                require_once("../models/Notificacion.php");
                $noti = new Notificacion();
                $noti->notificar_soportistas_por_ticket($tick_id, $mensaje = 'Tiene una nueva respuesta en el Ticket Nro: ');
            }
        }

        // Insertar detalle (siempre)
        $sql = "INSERT INTO td_ticketdetalle (tick_id, usu_id, tickd_descrip, fech_crea, est)
            VALUES (?, ?, ?, NOW(), '1')";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$tick_id, $usu_id, $tickd_descrip]);

// 🔹 MARCAR COMO NO LEÍDO PARA EL OTRO
// Si comenta SOPORTE (admin o soportista)
if (in_array($_SESSION['rol_id'], [2,5])) {
    // El usuario tiene algo nuevo
    $sql = "UPDATE tm_ticket SET leido_usuario = 0 WHERE tick_id = ?";
} else {
    // El soporte tiene algo nuevo
    $sql = "UPDATE tm_ticket SET leido_soporte = 0 WHERE tick_id = ?";
}

$stmt = $conectar->prepare($sql);
$stmt->execute([$tick_id]);

// 🔹 AUTOASIGNACION AUTOMATICA DE SOPORTE
// Solo si el ticket aún no tiene asignado un soportista
if (empty($usu_asig)) {

    // Verificar si el usuario que comenta pertenece a un sector de soporte
    $sqlSoporte = "SELECT s.es_soporte, u.sec_id
                   FROM tm_usuarios u
                   INNER JOIN tm_sectores s ON u.sec_id = s.sec_id
                   WHERE u.usu_id = ?";
    $stmtSoporte = $conectar->prepare($sqlSoporte);
    $stmtSoporte->execute([$usu_id]);
    $datosSoporte = $stmtSoporte->fetch(PDO::FETCH_ASSOC);

    if ($datosSoporte && $datosSoporte['es_soporte'] == 1) {

        $sec_usuario = $datosSoporte['sec_id'];

        // Verificar que el sector coincida con el sector destino del ticket
        if ($sec_usuario == $soporte_sec_id) {

            // Asignar el ticket al soportista que respondió
            $sqlAsignar = "UPDATE tm_ticket
                           SET usu_asig = ?, 
                               fech_asig = NOW(),
                               tick_estado = 'En Progreso'
                           WHERE tick_id = ?";
            $stmtAsignar = $conectar->prepare($sqlAsignar);
            $stmtAsignar->execute([$usu_id, $tick_id]);
        }
    }
}

        $sql1 = $conectar->prepare("SELECT LAST_INSERT_ID() AS tickd_id");
        $sql1->execute();
        return $sql1->fetchAll(PDO::FETCH_ASSOC);
    }


    public function notify_ticket_cerrado($tick_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);

        if (!$datos || count($datos) == 0) {
            return false;
        }

        foreach ($datos as $row) {
            $usu_asig = $row['usu_asig'];
            $usu_crea = $row['usu_id'];
            $soporte_sec_id = $row['soporte_sec_id'];
        }

        if ($_SESSION['rol_id'] == 1) {
            if (!empty($usu_asig)) {
                $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                    VALUES (?, CONCAT('El usuario ha cerrado el ticket Nro: ', ?), ?, 2)";
                $stmt = $conectar->prepare($sql);
                $stmt->execute([$usu_asig, $tick_id, $tick_id]);
            } else {
                $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                    SELECT usu_id, CONCAT('El usuario ha cerrado el ticket Nro: ', ?), ?, 2
                    FROM tm_usuarios WHERE sec_id = ? AND est = 1";
                $stmt = $conectar->prepare($sql);
                $stmt->execute([$tick_id, $tick_id, $soporte_sec_id]);
            }
        } else {
            $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                VALUES (?, CONCAT('Soporte ha cerrado el ticket Nro: ', ?), ?, 2)";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$usu_crea, $tick_id, $tick_id]);
        }

        // Notificar a soportistas (igual que en la otra función)
        require_once("../models/Notificacion.php");
        $noti = new Notificacion();
        // Solo notificar al sector si quien responde/cierra es un usuario común
        if ($_SESSION['rol_id'] == 1) {
            require_once("../models/Notificacion.php");
            $noti = new Notificacion();
            $noti->notificar_soportistas_por_ticket($tick_id, $mensaje = 'Tiene una nueva respuesta en el Ticket Nro: ');
        }


        return true;
    }

    public function insert_ticketdetalle_cerrar($tick_id, $usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);
        foreach ($datos as $row) {
            $usu_asig = $row['usu_asig'];
            $usu_crea = $row['usu_id'];
            $soporte_sec_id = $row['soporte_sec_id'];
        }

        // 🔔 Notificaciones
        if ($_SESSION['rol_id'] == 1) {
            // Usuario común cierra → notificar al asignado o al sector
            if (!empty($usu_asig)) {
                $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                    VALUES (?, CONCAT('El usuario ha cerrado el ticket Nro: ', ?), ?, 2)";
                $stmt = $conectar->prepare($sql);
                $stmt->execute([$usu_asig, $tick_id, $tick_id]);
            } else {
                $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                    SELECT usu_id, CONCAT('El usuario ha cerrado el ticket Nro: ', ?), ?, 2
                    FROM tm_usuarios WHERE sec_id = ? AND est = 1";
                $stmt = $conectar->prepare($sql);
                $stmt->execute([$tick_id, $tick_id, $soporte_sec_id]);
            }
        } else {
            // Soporte cierra → notificar al creador
            $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                VALUES (?, CONCAT('Soporte ha cerrado el ticket Nro: ', ?), ?, 2)";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$usu_crea, $tick_id, $tick_id]);
        }

        // 🔹 Llamar a la función global para notificar a los soportistas
        require_once("../models/Notificacion.php");
        $noti = new Notificacion();
        $noti->notificar_soportistas_por_ticket($tick_id, $mensaje = 'Se cerro el Ticket Nro: ');

        // 🔹 Registrar detalle
        $sql = $conectar->prepare("CALL sp_i_ticketdetalle_01(?, ?)");
        $sql->execute([$tick_id, $usu_id]);

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_ticketdetalle_reabrir($tick_id, $usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($tick_id);
        foreach ($datos as $row) {
            $usu_asig = $row['usu_asig'];
            $usu_crea = $row['usu_id'];
            $soporte_sec_id = $row['soporte_sec_id'];
        }

        // 🔔 Notificaciones
        if ($_SESSION['rol_id'] == 1) {
            // Usuario reabre → notificar al asignado o al sector
            if (!empty($usu_asig)) {
                $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                    VALUES (?, CONCAT('El usuario ha reabierto el ticket Nro: ', ?), ?, 2)";
                $stmt = $conectar->prepare($sql);
                $stmt->execute([$usu_asig, $tick_id, $tick_id]);
            } else {
                $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                    SELECT usu_id, CONCAT('El usuario ha reabierto el ticket Nro: ', ?), ?, 2
                    FROM tm_usuarios WHERE sec_id = ? AND est = 1";
                $stmt = $conectar->prepare($sql);
                $stmt->execute([$tick_id, $tick_id, $soporte_sec_id]);
            }
        } else {
            // Soporte reabre → notificar al creador
            $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                VALUES (?, CONCAT('Soporte ha reabierto el ticket Nro: ', ?), ?, 2)";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$usu_crea, $tick_id, $tick_id]);
        }

        // 🔹 Llamar a la función global para notificar a los soportistas
        require_once("../models/Notificacion.php");
        $noti = new Notificacion();
        $noti->notificar_soportistas_por_ticket($tick_id, $mensaje = 'Se reabrió el Ticket Nro: ');

        // 🔹 Registrar detalle
        $sql = "INSERT INTO td_ticketdetalle (tick_id, usu_id, tickd_descrip, fech_crea, est)
            VALUES (?, ?, 'Ticket Re-Abierto...', NOW(), '1')";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$tick_id, $usu_id]);

        $sql1 = $conectar->prepare("SELECT LAST_INSERT_ID() AS tickd_id");
        $sql1->execute();
        return $sql1->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_ticket($tick_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "UPDATE tm_ticket SET tick_estado = 'Cerrado', fech_cierre = now() WHERE tick_id = ?"; // Consulta a la DB
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $tick_id); // stmt::bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function update_ticket_en_progreso($tick_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Solo actualizamos si el estado actual es 'Abierto'
        $sql = "UPDATE tm_ticket 
            SET tick_estado = 'En Progreso' 
            WHERE tick_id = ? AND tick_estado = 'Abierto'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();

        return $sql->rowCount(); // Devolvemos cuántas filas se actualizaron (0 o 1)
    }

    public function update_ticket_asignacion($tick_id, $usu_asig = null, $soporte_sec_id = null)
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Actualiza los campos de asignación del ticket
        $sql = "UPDATE tm_ticket 
            SET usu_asig = ?, soporte_sec_id = ?, fech_asig = NOW()
            WHERE tick_id = ?";
        $stmt = $conectar->prepare($sql);

        $usu_asig_val = ($usu_asig === '' ? null : $usu_asig);
        $soporte_sec_id_val = ($soporte_sec_id === '' ? null : $soporte_sec_id);

        $stmt->bindValue(1, $usu_asig_val);
        $stmt->bindValue(2, $soporte_sec_id_val);
        $stmt->bindValue(3, $tick_id);
        $stmt->execute();

        // 🔔 1️⃣ Si se asigna a un usuario específico → notificarlo
        if (!empty($usu_asig_val)) {
            $sql1 = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                 VALUES (?, CONCAT('Se le ha asignado el ticket Nro: ', ?), ?, 2)";
            $sql1 = $conectar->prepare($sql1);
            $sql1->bindValue(1, $usu_asig_val);
            $sql1->bindValue(2, $tick_id);
            $sql1->bindValue(3, $tick_id);
            $sql1->execute();
        }

        // 🔔 2️⃣ Si se asigna a un sector soportista → notificar a todos los soportistas de ese sector
        if (!empty($soporte_sec_id_val)) {
            $sql2 = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, est)
                 SELECT usu_id, CONCAT('Nuevo ticket asignado al sector: ', (SELECT sec_nom FROM tm_sectores WHERE sec_id = ?)), ?, 2
                 FROM tm_usuarios
                 WHERE rol_id = 5 AND sec_id = ? AND est = 1";
            $stmt2 = $conectar->prepare($sql2);
            $stmt2->bindValue(1, $soporte_sec_id_val);
            $stmt2->bindValue(2, $tick_id);
            $stmt2->bindValue(3, $soporte_sec_id_val);
            $stmt2->execute();
        }

        return true;
    }

    public function get_ticket_total() //Funcion que trae el total de tickets
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_ticket_totalabierto() //Funcion que trae el total de tickets abiertos
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket WHERE tick_estado='Abierto'";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_ticket_totalcerrado() //Funcion que trae el total de tickets cerrados
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket WHERE tick_estado='Cerrado'";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_ticket_grafico()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT tm_categoria.cat_nom as nom,COUNT(*) AS total
            FROM   tm_ticket  JOIN  
                tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id  
            WHERE    
            tm_ticket.est = 1
            GROUP BY 
            tm_categoria.cat_nom 
            ORDER BY total DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function reabrir_ticket($tick_id)
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "UPDATE tm_ticket SET tick_estado = 'Abierto' WHERE tick_id = ?"; // Consulta a la DB
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $tick_id); // stmt::bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

public function filtrar_ticket($tick_titulo, $cat_id, $prio_id)
{
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "SELECT 
        tm_ticket.tick_id,
        tm_ticket.usu_id,
        tm_ticket.cat_id,
        tm_ticket.cats_id,
        tm_ticket.tick_titulo,
        tm_ticket.tick_descrip,
        tm_ticket.tick_estado,
        tm_ticket.fech_crea,
        tm_ticket.fech_cierre,
        tm_ticket.usu_asig,
        tm_ticket.fech_asig,
        tm_usuarios.usu_nom,
        tm_usuarios.usu_ape,
        tm_categoria.cat_nom,
        tm_subcategoria.cats_nom,
        tm_subcategoria.secs_id AS soporte_secs_id,
        tm_ticket.prio_id,
        tm_prioridad.prio_nom,
        tm_sectores.sec_nom,
        tm_ticket.soporte_sec_id,
        tm_ticket.leido_usuario,
        tm_ticket.leido_soporte,
        tm_subcategoria.secs_id AS soporte_secs_id_subcat,

        COALESCE(
            MAX(td_ticketdetalle.fech_crea),
            tm_ticket.fech_crea
        ) AS ultimo_movimiento

        FROM tm_ticket

        INNER JOIN tm_usuarios 
            ON tm_ticket.usu_id = tm_usuarios.usu_id

        INNER JOIN tm_categoria 
            ON tm_ticket.cat_id = tm_categoria.cat_id

        INNER JOIN tm_subcategoria 
            ON tm_ticket.cats_id = tm_subcategoria.cats_id

        INNER JOIN tm_prioridad 
            ON tm_ticket.prio_id = tm_prioridad.prio_id

        INNER JOIN tm_sectores 
            ON tm_usuarios.sec_id = tm_sectores.sec_id

        LEFT JOIN td_ticketdetalle
            ON tm_ticket.tick_id = td_ticketdetalle.tick_id
            AND td_ticketdetalle.est = 1

        WHERE tm_ticket.est = 1";

    if (!empty($tick_titulo)) {
        $sql .= " AND tm_ticket.tick_titulo LIKE ?";
    }

    if (!empty($cat_id)) {
        $sql .= " AND tm_ticket.cat_id = ?";
    }

    if (!empty($prio_id)) {
        $sql .= " AND tm_ticket.prio_id = ?";
    }

    $sql .= "
        GROUP BY
            tm_ticket.tick_id,
            tm_ticket.usu_id,
            tm_ticket.cat_id,
            tm_ticket.cats_id,
            tm_ticket.tick_titulo,
            tm_ticket.tick_descrip,
            tm_ticket.tick_estado,
            tm_ticket.fech_crea,
            tm_ticket.fech_cierre,
            tm_ticket.usu_asig,
            tm_ticket.fech_asig,
            tm_usuarios.usu_nom,
            tm_usuarios.usu_ape,
            tm_categoria.cat_nom,
            tm_subcategoria.cats_nom,
            tm_subcategoria.secs_id,
            tm_ticket.prio_id,
            tm_prioridad.prio_nom,
            tm_sectores.sec_nom,
            tm_ticket.soporte_sec_id,
            tm_ticket.leido_usuario,
            tm_ticket.leido_soporte

        ORDER BY
            ultimo_movimiento DESC,
            tm_ticket.tick_id DESC
    ";

    $stmt = $conectar->prepare($sql);

    $i = 1;

    if (!empty($tick_titulo)) {
        $stmt->bindValue($i++, "%" . $tick_titulo . "%");
    }

    if (!empty($cat_id)) {
        $stmt->bindValue($i++, $cat_id);
    }

    if (!empty($prio_id)) {
        $stmt->bindValue($i++, $prio_id);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function get_calendar_all()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
            tm_ticket.tick_id as id,
            concat(tm_usuarios.usu_nom,' ',tm_usuarios.usu_ape) as title,
            tm_ticket.fech_crea as start,
            CASE
            WHEN tm_ticket.tick_estado = 'Abierto' THEN 'green'
            WHEN tm_ticket.tick_estado = 'Cerrado' THEN 'red'
            ELSE 'white'
            END as color
            FROM 
            tm_ticket
            INNER JOIN tm_usuarios on tm_ticket.usu_id = tm_usuarios.usu_id";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_calendar_usu($usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
            tm_ticket.tick_id as id,
            concat(tm_usuarios.usu_nom,' ',tm_usuarios.usu_ape) as title,
            tm_ticket.fech_crea as start,
            CASE
            WHEN tm_ticket.tick_estado = 'Abierto' THEN 'green'
            WHEN tm_ticket.tick_estado = 'Cerrado' THEN 'red'
            ELSE 'white'
            END as color
            FROM 
            tm_ticket
            INNER JOIN tm_usuarios on tm_ticket.usu_id = tm_usuarios.usu_id
            WHERE tm_ticket.usu_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function filtrar_ticket_x_usuario($usu_asig)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "call filtrar_ticket_x_usuario(?)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_asig);
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
        // opcional: $sql->closeCursor(); // si luego llamás otro SP en la misma conexión
        return $resultado;
    }

    // Obtiene total de tickets del sector de la supervisora
    public function total_ticket_supervisora($sec_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) as TOTAL 
            FROM tm_ticket 
            INNER JOIN tm_usuarios ON tm_ticket.usu_id = tm_usuarios.usu_id
            WHERE tm_ticket.est=1 AND tm_usuarios.sec_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sec_id);
        $sql->execute();
        return $sql->fetch();
    }

    // Total de tickets abiertos
    public function total_abierto_supervisora($sec_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) as TOTAL 
            FROM tm_ticket 
            INNER JOIN tm_usuarios ON tm_ticket.usu_id = tm_usuarios.usu_id
            WHERE tm_ticket.est=1 AND tm_ticket.tick_estado='Abierto' AND tm_usuarios.sec_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sec_id);
        $sql->execute();
        return $sql->fetch();
    }

    // Total de tickets cerrados
    public function total_cerrado_supervisora($sec_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) as TOTAL 
            FROM tm_ticket 
            INNER JOIN tm_usuarios ON tm_ticket.usu_id = tm_usuarios.usu_id
            WHERE tm_ticket.est=1 AND tm_ticket.tick_estado='Cerrado' AND tm_usuarios.sec_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sec_id);
        $sql->execute();
        return $sql->fetch();
    }

    // Datos para gráfico (total por categoría, por ejemplo)
    public function grafico_supervisora($sec_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT tm_categoria.cat_nom as nom, COUNT(*) as total
            FROM tm_ticket
            INNER JOIN tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id
            INNER JOIN tm_usuarios ON tm_ticket.usu_id = tm_usuarios.usu_id
            WHERE tm_ticket.est=1 AND tm_usuarios.sec_id=?
            GROUP BY tm_categoria.cat_nom";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sec_id);
        $sql->execute();
        return $sql->fetchAll();
    }

    // Tickets para calendario
    public function calendar_supervisora($sec_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT tm_ticket.tick_id as id,
                   concat(tm_usuarios.usu_nom,' ',tm_usuarios.usu_ape) as title,
                   tm_ticket.fech_crea as start,
                   CASE
                       WHEN tm_ticket.tick_estado='Abierto' THEN 'green'
                       WHEN tm_ticket.tick_estado='Cerrado' THEN 'red'
                       ELSE 'white'
                   END as color
            FROM tm_ticket
            INNER JOIN tm_usuarios ON tm_ticket.usu_id = tm_usuarios.usu_id
            WHERE tm_ticket.est=1 AND tm_usuarios.sec_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sec_id);
        $sql->execute();
        return $sql->fetchAll();
    }

    public function filtrar_historial_ticket($tick_titulo, $cat_id, $prio_id)
            {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT 
        tm_ticket.tick_id,
        tm_ticket.usu_id,
        tm_ticket.cat_id,
        tm_ticket.tick_titulo,
        tm_ticket.tick_descrip,
        tm_ticket.tick_estado,
        tm_ticket.fech_crea,
        tm_ticket.fech_cierre,
        tm_ticket.usu_asig,
        tm_ticket.fech_asig,
        tm_usuarios.usu_nom,
        tm_usuarios.usu_ape,
        tm_categoria.cat_nom,
        tm_ticket.prio_id,
        tm_prioridad.prio_nom,
        tm_sectores.sec_nom
             FROM tm_ticket
             INNER JOIN tm_usuarios ON tm_ticket.usu_id = tm_usuarios.usu_id
             INNER JOIN tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id
             INNER JOIN tm_prioridad ON tm_ticket.prio_id = tm_prioridad.prio_id
             INNER JOIN tm_sectores ON tm_usuarios.sec_id = tm_sectores.sec_id
             WHERE tm_ticket.est=1";

             if (!empty($tick_titulo)) {
                $sql .= " AND tm_ticket.tick_titulo LIKE ?";
             }
             if (!empty($cat_id)) {
            $sql .= " AND tm_ticket.cat_id = ?";
             }
             if (!empty($prio_id)) {
            $sql .= " AND tm_ticket.prio_id = ?";
             }

             $sql .= " ORDER BY tm_ticket.tick_id DESC";

             $stmt = $conectar->prepare($sql);

             // Aquí hay que bindear los parámetros según estén seteados
             $i = 1;
             if (!empty($tick_titulo)) {
            $stmt->bindValue($i++, "%" . $tick_titulo . "%");
             }
             if (!empty($cat_id)) {
            $stmt->bindValue($i++, $cat_id);
             }
             if (!empty($prio_id)) {
            $stmt->bindValue($i++, $prio_id);
             }

             $stmt->execute();
             return $stmt->fetchAll();
    }

    // Tickets dirigidos a un sector soportista
    public function listar_ticket_x_sector_destino($sec_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT 
                t.tick_id,
                t.tick_titulo,
                t.tick_descrip,
                t.tick_estado,
                t.fech_crea,
                t.fech_cierre,
                t.usu_id,
                t.cat_id,
                t.cats_id,
                t.sec_id AS destino_sector,
                c.cat_nom,
                s.cats_nom,
                u.usu_nom,
                u.usu_ape,
                p.prio_nom,
                sec.sec_nom
            FROM tm_ticket t
            INNER JOIN tm_usuarios u ON t.usu_id = u.usu_id
            INNER JOIN tm_categoria c ON t.cat_id = c.cat_id
            LEFT JOIN tm_subcategoria s ON t.cats_id = s.cats_id
            INNER JOIN tm_prioridad p ON t.prio_id = p.prio_id
            INNER JOIN tm_sectores sec ON t.sec_id = sec.sec_id
            WHERE t.est = 1 AND t.sec_id = ?
            ORDER BY t.tick_id DESC";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $sec_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // En models/Ticket.php (dentro de la clase Ticket)
    public function get_ultimo_detalle($tick_id)
    {
        $conectar = parent::conexion(); // Ticket extiende Conectar, esto funciona aquí
        $sql = "SELECT tickd_descrip
            FROM td_ticketdetalle 
            WHERE tick_id = ? 
            ORDER BY tickd_id DESC 
            LIMIT 1";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$tick_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // puede ser false si no hay detalle
    }

    public function listar_ticket_creado_por_usuario($usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT * FROM tm_ticket 
            INNER JOIN tm_usuarios ON tm_ticket.usu_id = tm_usuarios.usu_id
            INNER JOIN tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id
            INNER JOIN tm_prioridad ON tm_ticket.prio_id = tm_prioridad.prio_id
            INNER JOIN tm_sectores ON tm_usuarios.sec_id = tm_sectores.sec_id
            WHERE tm_ticket.usu_id = ?";

        $query = $conectar->prepare($sql);
        $query->bindValue(1, $usu_id);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

public function marcar_como_leido($tick_id, $rol_id)
{
    $conectar = parent::conexion();
    parent::set_names();

    // Soporte = rol 2 (admin) y 5 (soportista)
    if (in_array($rol_id, [2,5])) {
        $sql = "UPDATE tm_ticket SET leido_soporte = 1 WHERE tick_id = ?";
    } else {
        $sql = "UPDATE tm_ticket SET leido_usuario = 1 WHERE tick_id = ?";
    }

    $stmt = $conectar->prepare($sql);
    $stmt->execute([$tick_id]);
}

public function obtener_estado_lectura($tick_id)
{
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "SELECT 
                tick_id,
                leido_usuario,
                leido_soporte
            FROM tm_ticket
            WHERE tick_id = ?";

    $stmt = $conectar->prepare($sql);
    $stmt->execute([$tick_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function insert_ticketdetalle_sistema($tick_id, $usu_id, $mensaje)
{
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "INSERT INTO td_ticketdetalle
            (tick_id, usu_id, tickd_descrip, fech_crea, est)
            VALUES (?, ?, ?, NOW(), '1')";

    $stmt = $conectar->prepare($sql);
    $stmt->execute([
        $tick_id,
        $usu_id,
        $mensaje
    ]);

    return true;
}
}