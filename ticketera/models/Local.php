<?php
class Local extends Conectar
{

    public function get_locales_activos()
    {
        $conectar = parent::conexion();
        $sql = "SELECT loc_id, loc_nom FROM tm_locales WHERE est = 1 ORDER BY loc_nom";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_local_x_id($loc_id)
    {
        $conectar = parent::conexion();
        $sql = "SELECT * FROM tm_locales WHERE loc_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $loc_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_local($loc_nom)
    {
        $conectar = parent::conexion();
        $sql = "INSERT INTO tm_locales (loc_nom, est) VALUES (?, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $loc_nom);
        $sql->execute();
    }

    public function update_local($loc_id, $loc_nom)
    {
        $conectar = parent::conexion();
        $sql = "UPDATE tm_locales SET loc_nom=? WHERE loc_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $loc_nom);
        $sql->bindValue(2, $loc_id);
        $sql->execute();
    }

    public function delete_local($loc_id)
    {
        $conectar = parent::conexion();
        $sql = "UPDATE tm_locales SET est=0 WHERE loc_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $loc_id);
        $sql->execute();
    }
}
