<?php
//Creamos una clase Usuario que se conecta con la clase Conectar para lograr la conexion con la DB

class Usuario extends Conectar
{
    //Creamos la funcion login
    public function login()
    {
        $conectar = parent::conexion();
        parent::set_names();

        if (isset($_POST["enviar"])) {
            $correo = $_POST["usu_correo"];
            $pass = $_POST["usu_pass"];
            $rol   = $_POST["rol_id"];

            if (empty($correo) and empty($pass)) {
                if ($rol == 2) {
                    header("Location:" . Conectar::ruta() . "view/AccesoSoporte/index.php?m=2");
                } else {
                    header("Location:" . Conectar::ruta() . "index.php?m=2");
                }
                exit();
            } else {
                $sql = "SELECT * FROM tm_usuarios WHERE usu_correo=? AND rol_id=? AND est=1";
                $stmt = $conectar->prepare($sql);
                $stmt->bindValue(1, $correo);
                $stmt->bindValue(2, $rol);
                $stmt->execute();
                $resultado = $stmt->fetch();

                if ($resultado) {
                    $textocifrado = $resultado["usu_pass"];
                    $key = "mi_key_secret";
                    $cipher = "aes-256-cbc";

                    $iv_dec = substr(base64_decode($textocifrado), 0, openssl_cipher_iv_length($cipher));
                    $cifradoSinIV = substr(base64_decode($textocifrado), openssl_cipher_iv_length($cipher));
                    $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec);

                    if ($decifrado == $pass) {
                        if (is_array($resultado) and count($resultado) > 0) {
                            $_SESSION["usu_id"] = $resultado["usu_id"];
                            $_SESSION["usu_nom"] = $resultado["usu_nom"];
                            $_SESSION["usu_ape"] = $resultado["usu_ape"];
                            $_SESSION["rol_id"] = $resultado["rol_id"];
                            $_SESSION["sec_id"] = $resultado["sec_id"];


                            header("Location:" . Conectar::ruta() . "view/Home/");
                            exit();
                        } else {
                            if ($rol == 2) {
                                header("Location:" . Conectar::ruta() . "view/AccesoSoporte/index.php?m=1");
                            } else {
                                header("Location:" . Conectar::ruta() . "index.php?m=1");
                            }
                            exit();
                        }
                    }
                }
            }
        }
    }

    public function insert_usuario($usu_nom, $usu_ape, $usu_correo, $usu_pass, $rol_id, $sec_id) //Funcion para crear un usuario
    {
        $key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
        $cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); //Vector de inicialización (IV) necesario para el cifrado
        $cifrado = openssl_encrypt($usu_pass, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $textoCifrado = base64_encode($iv . $cifrado);

        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "INSERT INTO tm_usuarios (usu_id, usu_nom, usu_ape, usu_correo, usu_pass, rol_id, sec_id, fecha_crea, fecha_modi, fecha_elim, est) VALUES (NULL, ?, ?, ?, ?, ?, ?, now(), NULL, NULL, '1')";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_nom); //bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
        $sql->bindValue(2, $usu_ape);
        $sql->bindValue(3, $usu_correo);
        $sql->bindValue(4, $textoCifrado);
        $sql->bindValue(5, $rol_id);
        $sql->bindValue(6, $sec_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
    }

    public function update_usuario($usu_id, $usu_nom, $usu_ape, $usu_correo, $usu_pass, $rol_id, $sec_id)
    { {
            $key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
            $cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); //Vector de inicialización (IV) necesario para el cifrado
            $cifrado = openssl_encrypt($usu_pass, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $textoCifrado = base64_encode($iv . $cifrado);

            $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
            parent::set_names();
            $sql = "UPDATE tm_usuarios SET usu_nom = ?, usu_ape = ?, usu_correo = ?, usu_pass = ?, rol_id = ?, sec_id = ? WHERE usu_id = ?";
            $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
            $sql->bindValue(1, $usu_nom); //bindValue es una función que vincula un valor a un marcador de posición en una instrucción SQL
            $sql->bindValue(2, $usu_ape);
            $sql->bindValue(3, $usu_correo);
            $sql->bindValue(4, $textoCifrado);
            $sql->bindValue(5, $rol_id);
            $sql->bindValue(6, $sec_id);
            $sql->bindValue(7, $usu_id);
            $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
            return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
        }
    }

    public function delete_usuario($usu_id) // Funcion que elimina a un usuario
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "UPDATE tm_usuarios SET est='0', fecha_elim = now() WHERE usu_id = ?";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_usuario() //Funcion que me trae todos los usuarios activos
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "call sp_l_usuario_01()";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_x_rol()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_usuarios WHERE est = 1 AND (rol_id = 2 OR rol_id = 4) ORDER BY usu_nom ASC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    public function get_usuario_x_id($usu_id) //Funcion que trae a un usuario en particular
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "call sp_l_usuario_02(?)";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_total_x_id($usu_id) //Funcion que trae el total de tickets por usuario
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket WHERE usu_id = ?";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_totalabierto_x_id($usu_id) //Funcion que trae el total de tickets abiertos por usuario
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket WHERE usu_id= ? AND tick_estado='Abierto'";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_totalcerrado_x_id($usu_id) //Funcion que trae el total de tickets cerrados por usuario
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket WHERE usu_id= ? AND tick_estado='Cerrado'";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_grafico($usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT tm_categoria.cat_nom as nom,COUNT(*) AS total
            FROM   tm_ticket  JOIN  
                tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id  
            WHERE    
            tm_ticket.est = 1 AND tm_ticket.usu_id = ?
            GROUP BY 
            tm_categoria.cat_nom 
            ORDER BY total DESC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function update_usuario_pass($usu_id, $usu_pass) //Funcion para resetear pass
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "UPDATE tm_usuarios SET usu_pass = ? WHERE usu_id = ?";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_pass);
        $sql->bindValue(2, $usu_id);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_x_correo($usu_correo) //Funcion que trae a un usuario en particular por su correo
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "SELECT * FROM tm_usuarios WHERE usu_correo = ? AND est=1";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_correo);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function get_cambiar_contra_recuperar($usu_correo) //Funcion que trae a un usuario en particular por su correo
    {
        $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
        parent::set_names();
        $sql = "UPDATE tm_usuarios SET usu_pass = CONCAT (SUBSTRING(MD5(RAND()),1,3),LPAD(FLOOR(RAND()*1000),3,'0')) WHERE usu_correo = ?";
        $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
        $sql->bindValue(1, $usu_correo);
        $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
        return $resultado = $sql->fetchAll();
    }

    public function encriptar_nueva_contra($usu_id, $usu_pass) //Funcion para encriptar la nueva contraseña
    { {
            $key = "mi_key_secret"; // Clave de Cifrado (asegúrate de usar una clave segura en un entorno real)
            $cipher = "aes-256-cbc"; //Metodo de Cifrado (puedes usar 'aes-256-cbc' u otros algoritmos soportados por OpenSSL)
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); //Vector de inicialización (IV) necesario para el cifrado
            $cifrado = openssl_encrypt($usu_pass, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $textoCifrado = base64_encode($iv . $cifrado);

            $conectar = parent::conexion(); //Instanciamos el motodo "conexion" del archivo conexion.php con un parent(Se utiliza para acceder a un metodo de una clase derivada)!
            parent::set_names();
            $sql = "UPDATE tm_usuarios SET usu_pass = ? WHERE usu_id = ?";
            $sql = $conectar->prepare($sql); // prepare es una función que prepara una sentencia SQL para ser ejecutada // el símbolo -> es un operador que se usa para acceder a las propiedades y métodos de un objeto
            $sql->bindValue(1, $textoCifrado);
            $sql->bindValue(2, $usu_id);
            $sql->execute(); //->execute() es una función que ejecuta una consulta preparada previamente
            return $resultado = $sql->fetchAll(); //En PHP, fetchAll() es un método que devuelve un array con todas las filas de un conjunto de resultados. Instanciamos la consulta previa al SQL en la variable resultado
        }
    }

    public function getByEmail($email)
    {
        $conectar = parent::conexion();
        $sql = $conectar->prepare("SELECT * FROM tm_usuarios WHERE usu_correo=? LIMIT 1");
        $sql->execute([$email]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function createFromMicrosoft($data)
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Encriptar contraseña vacía (o token fijo)
        $key = "mi_key_secret";
        $cipher = "aes-256-cbc";
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $cifrado = openssl_encrypt($data['usu_pass'], $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $textoCifrado = base64_encode($iv . $cifrado);

        $sql = $conectar->prepare("
        INSERT INTO tm_usuarios 
        (usu_correo, usu_nom, usu_ape, usu_pass, rol_id, sec_id, fecha_crea, est) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), 1)
        ");
        $sql->execute([
            $data['usu_correo'],
            $data['usu_nom'],
            $data['usu_ape'],
            $textoCifrado,
            $data['rol_id'],
            $data['sec_id']
        ]);

        return $conectar->lastInsertId();
    }

    public function forzarRolUsuario($usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE tm_usuarios SET rol_id = 1, est = 1 WHERE usu_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$usu_id]);
    }

    public function update_usuario_sin_pass($usu_id, $usu_nom, $usu_ape, $usu_correo, $rol_id, $sec_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE tm_usuarios 
            SET usu_nom = ?, 
                usu_ape = ?, 
                usu_correo = ?, 
                rol_id = ?,
                sec_id = ?
            WHERE usu_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$usu_nom, $usu_ape, $usu_correo, $rol_id, $sec_id, $usu_id]);

        return $stmt;
    }

    public function get_sectores()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT sec_id, sec_nom FROM tm_sectores ORDER BY sec_nom ASC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_usuario_x_sector($sec_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT usu_id, usu_nom, usu_ape
            FROM tm_usuarios
            WHERE sec_id = ? AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sec_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

public function get_guardias_actuales()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
            g.id,
            g.guardia1 AS guardia1_id,
            g.guardia2 AS guardia2_id,
            g.fecha_modificacion,
            g.celular_guardia1,
            g.celular_guardia2,

            u1.usu_nom AS guardia1_nombre,
            u1.usu_ape AS guardia1_apellido,

            u2.usu_nom AS guardia2_nombre,
            u2.usu_ape AS guardia2_apellido

        FROM tm_guardia_actual g

        LEFT JOIN tm_usuarios u1
            ON g.guardia1 = u1.usu_id

        LEFT JOIN tm_usuarios u2
            ON g.guardia2 = u2.usu_id

        LIMIT 1";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_usuarios_sistemas()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
            usu_id,
            CONCAT(usu_nom,' ',usu_ape) AS nombre
            FROM tm_usuarios
            WHERE sec_id = 4
            AND est = 1
            ORDER BY usu_nom ASC";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizar_guardias($guardia1, $guardia2, $celular1, $celular2)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE tm_guardia_actual
            SET
                guardia1 = ?,
                guardia2 = ?,
                celular_guardia1 = ?,
                celular_guardia2 = ?,
                fecha_modificacion = NOW()
            WHERE id = 1";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $guardia1);
        $sql->bindValue(2, $guardia2);
        $sql->bindValue(3, $celular1);
        $sql->bindValue(4, $celular2);
        $sql->execute();

        return true;
    }
}