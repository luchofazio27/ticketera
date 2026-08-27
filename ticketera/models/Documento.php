<?php
class Documento extends Conectar{
    public function insert_documento($tick_id,$doc_nom){
        $conectar=parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        $sql="INSERT INTO td_documento (doc_id,tick_id,doc_nom,fech_crea,est) VALUES (null,?,?,now(),1);";
        $sql = $conectar -> prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindParam(1, $tick_id); // bindParam es una función de PHP que vincula variables con marcadores de posición en una sentencia SQL. Se utiliza para pasar variables, no valores. 
        $sql->bindParam(2, $doc_nom);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
    }

    public function get_documento_x_ticket($tick_id){
        $conectar=parent::conexion();
        $sql ="SELECT * FROM td_documento WHERE tick_id=?";
        $sql = $conectar -> prepare($sql);
        $sql->bindParam(1, $tick_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(pdo::FETCH_ASSOC);
    }

    public function insert_documento_detalle($tickd_id,$det_nom){
        $conectar=parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        $sql="INSERT INTO td_documento_detalle (det_id,tickd_id,det_nom,est) VALUES (null,?,?,1);";
        $sql = $conectar -> prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindParam(1, $tickd_id); // bindParam es una función de PHP que vincula variables con marcadores de posición en una sentencia SQL. Se utiliza para pasar variables, no valores. 
        $sql->bindParam(2, $det_nom);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
    }

    public function get_documento_detalle_x_ticket($tickd_id){
        $conectar=parent::conexion();
        $sql ="SELECT * FROM td_documento_detalle WHERE tickd_id=?";
        $sql = $conectar -> prepare($sql);
        $sql->bindParam(1, $tickd_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(pdo::FETCH_ASSOC);
    }
}
?>