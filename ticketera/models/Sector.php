<?php
class Sector extends Conectar
{

    // Insertar un nuevo sector
    public function insert_sector($sec_nom, $sec_descr, $es_soporte, $sec_correo_ticket)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_sectores (sec_nom, es_soporte, sec_descr, sec_correo_ticket, est) VALUES (?, ?, ?, ?, '1')";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sec_nom);
        $sql->bindValue(2, $es_soporte);
        $sql->bindValue(3, $sec_descr);
$sql->bindValue(4, $sec_correo_ticket);
        $sql->execute();
        return $conectar->lastInsertId();
    }

    // Actualizar un sector existente
    public function update_sector($sec_id, $sec_nom, $sec_descr, $es_soporte, $sec_correo_ticket)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_sectores SET sec_nom=?, es_soporte=?, sec_descr=?, sec_correo_ticket=? WHERE sec_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sec_nom);
        $sql->bindValue(2, $es_soporte);
        $sql->bindValue(3, $sec_descr);
$sql->bindValue(4, $sec_correo_ticket);
        $sql->bindValue(5, $sec_id);
        return $sql->execute();
    }

    // Eliminar sector (baja lógica)
    public function delete_sector($sec_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_sectores SET est='0' WHERE sec_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sec_id);
        return $sql->execute();
    }

    // Mostrar un sector por ID
    public function get_sector_x_id($sec_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_sectores WHERE sec_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sec_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    // Listar todos los sectores activos
    public function get_sectores()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_sectores WHERE est='1' ORDER BY sec_nom ASC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_sectores_soporte()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_sectores WHERE est = 1 AND es_soporte = 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}