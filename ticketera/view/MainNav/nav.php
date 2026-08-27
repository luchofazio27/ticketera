<?php
$rol_id = $_SESSION["rol_id"];
?>

<nav class="side-menu">
    <ul class="side-menu-list">

        <!-- Siempre visible -->

 <li class="blue-dirty">
    <a href="..\NuevoTicket\">
        <span class="glyphicon glyphicon-plus" style="color:green"></span>
        <span class="lbl" style="color:black;font-weight:bold;">Nuevo Ticket</span>
    </a>
</li>

        <li class="blue-dirty">
            <a href="..\ConsultarTicket\">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Consultar Ticket</span>
            </a>
        </li>

        <li class="blue-dirty">
            <a href="..\HistorialTicket\">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Historial Ticket</span>
            </a>
        </li>

<li class="blue-dirty">
            <a href="..\Guardias\">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Guardias</span>
            </a>
        </li>

        <?php if ($rol_id == 2): // Administrador 
        ?>
            <li class="blue-dirty"><a href="..\MntUsuario\"><span class="glyphicon glyphicon-wrench"></span><span class="lbl">Mant. Usuario</span></a></li>
            <li class="blue-dirty"><a href="..\MntLocal\"><span class="glyphicon glyphicon-wrench"></span><span class="lbl">Mant. Locales</span></a></li>
            <li class="blue-dirty"><a href="..\MntPrioridad\"><span class="glyphicon glyphicon-wrench"></span><span class="lbl">Mant. Prioridad</span></a></li>
            <li class="blue-dirty"><a href="..\MntCategoria\"><span class="glyphicon glyphicon-wrench"></span><span class="lbl">Mant. Categoria</span></a></li>
            <li class="blue-dirty"><a href="..\MntSubCategoria\"><span class="glyphicon glyphicon-wrench"></span><span class="lbl">Mant. Sub Categoria</span></a></li>
            <li class="blue-dirty"><a href="..\MntSector\"><span class="glyphicon glyphicon-wrench"></span><span class="lbl">Mant. Sectores</span></a></li>
            <li class="blue-dirty"><a href="..\GuardiasAdmin\"><span class="glyphicon glyphicon-wrench"></span><span class="lbl">Mant. Guardias</span></a></li>
            <li class="blue-dirty"><a href="..\Home\"><span class="glyphicon glyphicon-stats"></span><span class="lbl">Estadistica</span></a></li>
        <?php endif; ?>



    </ul>
</nav><!--.side-menu-->