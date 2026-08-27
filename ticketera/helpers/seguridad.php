<?php
// helpers/seguridad.php
require_once(__DIR__ . '/../config/conexion.php');

class SeguridadHelper {

    /**
     * Verifica si el usuario tiene sesión activa.
     * Si no está logueado, redirige al login.
     */
    public static function verificarSesion() {
        if (!isset($_SESSION["usu_id"])) {
            header("Location:" . Conectar::ruta() . "index.php");
            exit();
        }
    }

    /**
     * Verifica si el usuario tiene permiso para acceder.
     * @param array $rolesPermitidos - IDs de roles con permiso (por ejemplo [2,4])
     */
    public static function verificarAcceso($rolesPermitidos = []) {
        self::verificarSesion(); // Primero aseguramos que haya sesión

        if (!in_array($_SESSION["rol_id"], $rolesPermitidos)) {
            header("Location:" . Conectar::ruta() . "view/404/");
            exit();
        }
    }

    /**
     * Verifica si el usuario tiene uno de los roles indicados.
     * Devuelve true o false (para mostrar/ocultar elementos en el menú, etc.)
     */
    public static function tieneRol($rolesPermitidos = []) {
        return (isset($_SESSION["rol_id"]) && in_array($_SESSION["rol_id"], $rolesPermitidos));
    }
}
?>
