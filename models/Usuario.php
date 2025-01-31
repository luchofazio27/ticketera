<?php
//Creamos una clase Usuario que se conecta con la clase Conectar para lograr la conexion con la DB

class Usuario extends Conectar{

    //Creamos la funcion login
    public function login(){
        $conectar=parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)
        parent::set_names();
        if(isset($_POST["enviar"])){ //isset() es una función que comprueba si una variable está definida y tiene un valor distinto de NULL. Devuelve true si la variable existe y false si no
                 //$_POST es una variable que recolecta los datos que se envían a través del método HTTP POST. Se trata de una variable global, es decir, que está disponible en cualquier parte del script
                 $correo = $_POST["usu_correo"]; // Instancia en una variable el correo que se envia
                 $pass = $_POST["usu_pass"];
                 if(empty($correo) and empty($pass)) { // Si estan vacios el correo y la pass
                    header("Location:".Conectar::ruta()."index.php?m=2"); //header() envía encabezados HTTP sin formato a un navegador o cliente
                    exit();
                 } else {
                    $sql = "SELECT * FROM tm_usuarios WHERE usu_correo=? AND usu_pass=? AND est=1"; //$sql es una función que se utiliza para enviar consultas al lenguaje de consulta estructurado (SQL)
                    $stmt = $conectar -> prepare($sql); //stmt prepare es una función que prepara una sentencia SQL para ser ejecutada
                    $stmt -> bindValue(1, $correo); // stmt::bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
                    $stmt -> bindValue(2, $pass);
                    $stmt -> execute(); //stmt->execute() es una función que ejecuta una consulta preparada previamente
                    $resultado = $stmt -> fetch(); //stmt fetch es una función que se utiliza para obtener una fila de resultados de un conjunto de resultados asociado a un objeto PDOStatement
                    if(is_array($resultado) and count($resultado) > 0) { //Preguntamos si devolvio un array y si el resultado es mayor a 0
                        $_SESSION["usu_id"] = $resultado["usu_id"]; //Creamos las variables de session
                        $_SESSION["usu_nom"] = $resultado["usu_nom"];//Respetamos los nombres de las columnas en la DB
                        $_SESSION["usu_ape"] = $resultado["usu_ape"];
                        header("Location:".Conectar::ruta()."view/Home/");
                        exit();
                    } else {
                        header("Location:".Conectar::ruta()."index.php?m=1");
                        exit();
                    }
                 }
        }
        }
}

?>