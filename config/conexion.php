<?php
//En este archivo logramos la conexion con la DB
session_start();

class Conectar{
    protected $dbh;

    protected function Conexion(){
        try {
            $conectar = $this->dbh = new PDO("mysql:local=localhost;dbname=db_ticketera","root",""); //PDO (PHP Data Objects) es una extensión de PHP que permite acceder a bases de datos
            return $conectar;
        } catch (Exception $e) {
            print "¡Error BD!: " . $e->getMessage() . "<br/>";
            die();
        }

    }

    public function set_names(){
        return $this->dbh->query("SET NAMES 'utf8'");
    }

    public static function ruta(){
        return "http://localhost/TICKETERA/";
    }
}
?>