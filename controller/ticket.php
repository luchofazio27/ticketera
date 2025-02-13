<?php
require_once("../config/conexion.php"); //Conexion con DB
require_once("../models/Ticket.php"); //Conexion con el modelo Ticket
$ticket = new Ticket(); // Declaramos esa clase

switch ($_GET["op"]) { //$_GET es una matriz de variables que se pasan al script actual a través de los parámetros de URL
    case "insert":
        $ticket->insert_ticket($_POST["usu_id"], $_POST["cat_id"], $_POST["tick_titulo"], $_POST["tick_descrip"]); //La línea de código está llamando al método insert_ticket del objeto $ticket y le pasa cuatro parámetros que se toman de un formulario enviado mediante el método POST
        break;
    case "listar_x_usu":
        $datos = $ticket->listar_ticket_x_usu($_POST["usu_id"]); //Llama al metodo "listar_ticket_x_usu" del objeto ticket y le envia parametros
        $data = array(); // Declaramos un array
        foreach ($datos as $row) { // Recorre la variable datos que tiene el listado de tickets
            $sub_array = array(); // Se crea un array que almacena el tick id, cat nom, tick titulo.
            $sub_array[] = $row["tick_id"];
            $sub_array[] = $row["cat_nom"];
            $sub_array[] = $row["tick_titulo"];
            if ($row["tick_estado"] == "Abierto") { // Verificamos el estado del ticket para darle una clase
                $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
            } else {
                $sub_array[] = '<span class="label label-pill label-danger">Cerrado</span>';
            }
            $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"])); // Trae la fecha y la hora de cuando se creo el ticket
            $sub_array[] = '<button type="button" onClick="ver(' . $row["tick_id"] . ');" id="' . $row["tick_id"] . '" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></button>'; // onClick="ver('.$row["tick_id"].');": Este es un evento que se ejecutará cuando se haga clic en el botón
            $data[] = $sub_array;
        };

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data), // Cuenta cuántos elementos hay en el arreglo
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results); // Convierte el arreglo en una cadena JSON
        break;

    case "listar":
        $datos = $ticket->listar_ticket(); //Llama al metodo "listar_ticket" del objeto ticket con la consulta en el modelo
        $data = array(); // Declaramos un array
        foreach ($datos as $row) { // Recorre la variable datos que tiene el listado de tickets
            $sub_array = array(); // Se crea un array que almacena el tick id, cat nom, tick titulo.
            $sub_array[] = $row["tick_id"];
            $sub_array[] = $row["cat_nom"];
            $sub_array[] = $row["tick_titulo"];
            if ($row["tick_estado"] == "Abierto") { // Verificamos el estado del ticket para darle una clase
                $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
            } else {
                $sub_array[] = '<span class="label label-pill label-danger">Cerrado</span>';
            }
            $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"])); // Trae la fecha y la hora de cuando se creo el ticket
            $sub_array[] = '<button type="button" onClick="ver(' . $row["tick_id"] . ');" id="' . $row["tick_id"] . '" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></button>'; // onClick="ver('.$row["tick_id"].');": Este es un evento que se ejecutará cuando se haga clic en el botón
            $data[] = $sub_array;
        };

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data), // Cuenta cuántos elementos hay en el arreglo
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results); // Convierte el arreglo en una cadena JSON
        break;

    case "listardetalle":
        $datos = $ticket->listar_ticketdetalle_x_ticket($_POST["tick_id"]); // Llama a la funcion "listar_ticketdetalle_x_ticket" del objeto ticket con la consulta en el modelo
        ?>
        <?php
        foreach ($datos as $row) { // Recorre la variable datos que almaceno la consulta listar_ticketdetalle_x_ticket
        ?>
            <article class="activity-line-item box-typical">
                <div class="activity-line-date">
                    <?php echo date("d/m/Y", strtotime($row["fech_crea"])); ?>
                </div>
                <header class="activity-line-item-header">
                    <div class="activity-line-item-user">
                        <div class="activity-line-item-user-photo">
                            <a href="#">
                                <img src="../../public/img/photo-64-2.jpg" alt="">
                            </a>
                        </div>
                        <div class="activity-line-item-user-name"><?php echo $row['usu_nom'] . ' ' . $row['usu_ape']; ?></div>
                        <div class="activity-line-item-user-status">
                            <?php
                            if ($row['rol_id'] == 1) {
                                echo 'Usuario';
                            } else {
                                echo 'Sistemas';
                            }
                            ?>
                        </div>
                    </div>
                </header>
                <div class="activity-line-action-list">
                    <section class="activity-line-action">
                        <div class="time"><?php echo date("H:i:s", strtotime($row["fech_crea"])); ?></div>
                        <div class="cont">
                            <div class="cont-in">
                                <p><?php echo $row["tickd_descrip"]; ?></p>
                            </div>
                        </div>
                    </section><!--.activity-line-action-->
                </div><!--.activity-line-action-list-->
            </article><!--.activity-line-item-->
        <?php
        }
        ?>
        <?php
        break;
    case "mostrar";
        $datos = $ticket->listar_ticket_x_id($_POST["tick_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["tick_id"] = $row["tick_id"];
                $output["usu_id"] = $row["usu_id"];
                $output["cat_id"] = $row["cat_id"];
                $output["tick_titulo"] = $row["tick_titulo"];
                $output["tick_descrip"] = $row["tick_descrip"];
                if($row["tick_estado"] == "Abierto"){
                    $output["tick_estado"] = '<span class="label label-pill label-success">Abierto</span>';
                } else {
                    $output["tick_estado"] = '<span class="label label-pill label-danger">Cerrado</span>';
                };
                $output["fech_crea"] = date("d:m:Y H:i:s", strtotime($row["fech_crea"]));
                $output["usu_nom"] = $row["usu_nom"];
                $output["usu_ape"] = $row["usu_ape"];
                $output["cat_nom"] = $row["cat_nom"];
            }
            echo json_encode($output);
        }
        break;
    case "insertdetalle":
            $ticket->insert_ticketdetalle($_POST["tick_id"], $_POST["usu_id"], $_POST["tickd_descrip"]); //La línea de código está llamando al método insert_ticketdetalle del objeto $ticket y le pasa tres parámetros que se toman de un formulario enviado mediante el método POST
            break;    
}
?>