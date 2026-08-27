<?php
require_once("../../config/conexion.php"); // Conexión con DB
if (isset($_SESSION["usu_id"])) { // Si hay un usuario logueado:
?>
<!DOCTYPE html>
<html>
<?php require_once("../MainHead/head.php"); ?>
<title>Ticketera VER :: Perfil</title>

<body class="with-side-menu">

<?php require_once("../MainHeader/header.php"); ?>
<div class="mobile-menu-left-overlay"></div>
<?php require_once("../MainNav/nav.php"); ?>

<div class="page-content">
    <div class="container-fluid">
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h3>Mi Perfil</h3>
                        <ol class="breadcrumb breadcrumb-simple">
                            <li><a href="#">Home</a></li>
                            <li class="active">Perfil de Usuario</li>
                        </ol>
                    </div>
                </div>
            </div>
        </header>

        <div class="box-typical box-typical-padding">
            <div class="row">

                <!-- Información general del usuario -->
                <div class="col-lg-6">
                    <h5 class="m-t-lg with-border">Información del Usuario</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Nombre</th>
                            <td><?php echo $_SESSION["usu_nom"] . " " . $_SESSION["usu_ape"]; ?></td>
                        </tr>
                        <tr>
    <th>Correo</th>
    <td>
        <?php
        if (isset($_SESSION["usu_correo"]) && !empty($_SESSION["usu_correo"])) {
            echo $_SESSION["usu_correo"];
        } else {
            echo "<em>Cuenta local (sin correo de dominio)</em>";
        }
        ?>
    </td>
</tr>

                        <tr>
                            <th>Rol</th>
                            <td>
                                <?php
                                switch ($_SESSION["rol_id"]) {
                                    case 1: echo "Usuario"; break;
                                    case 2: echo "Administrador"; break;
                                    case 3: echo "Supervisora"; break;
                                    case 4: echo "Visual"; break;
                                    case 5: echo "Soporte"; break;
                                    default: echo "Desconocido"; break;
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Bloque de cambio de contraseña -->
                <div class="col-lg-6">
                    <h5 class="m-t-lg with-border">Seguridad</h5>
                    <p class="text-muted">
                        Tu cuenta está vinculada con Microsoft. Por motivos de seguridad, 
                        el cambio de contraseña debe realizarse directamente desde tu cuenta de Microsoft.
                    </p>

                    <fieldset class="form-group">
                        <label class="form-label semibold">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="txtpass" name="txtpass" value="********" disabled>
                    </fieldset>

                    <fieldset class="form-group">
                        <label class="form-label semibold">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="txtpassnew" name="txtpassnew" value="********" disabled>
                    </fieldset>

                    <button type="button" class="btn btn-rounded btn-inline btn-secondary" disabled>Actualizar</button>
                </div>

            </div><!-- .row -->
        </div><!-- .box-typical -->
    </div><!-- .container-fluid -->
</div><!-- .page-content -->

<?php require_once("../MainJs/js.php"); ?>
<script type="text/javascript" src="mntperfil.js"></script>
<script type="text/javascript" src="../notificacion.js"></script>
</body>
</html>

<?php
} else { // Si no hay usuario logueado, redirige al login
    header("Location:" . Conectar::ruta() . "index.php");
}
?>