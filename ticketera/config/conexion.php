<?php
//En este archivo logramos la conexion con la DB
session_start();

class Conectar{
    protected $dbh;

    protected function Conexion(){
        try {
            $conectar = $this->dbh = new PDO("mysql:local=localhost;dbname=c1442310_tickets;charset=utf8mb4","c1442310_tickets","nawuDE55sa"); //PDO (PHP Data Objects) es una extensión de PHP que permite acceder a bases de datos
            return $conectar;
        } catch (Exception $e) {
            print "¡Error BD!: " . $e->getMessage() . "<br/>";
            die();
        }

    }

    public function set_names(){
        return $this->dbh->query("SET NAMES 'utf8mb4'");
    }

    public static function ruta(){
        return "http://ticketsver.online/ticketera/";
    }
}
?>