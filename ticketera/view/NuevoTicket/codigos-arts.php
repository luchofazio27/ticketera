<?php
require_once("../../config/conexion.php");
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once("../MainHead/head.php"); ?>
    <title>Ticketera VER :: Códigos Arquitectura y Visual</title>

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
                                <h3>Materiales Arquitectura y Visual</h3>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="#">Home</a></li>
                                    <li class="active">Listado de artículos</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="box-typical box-typical-padding">
                    <p class="lead">
                        Materiales utilizados para la <strong>exhibición</strong> y <strong>comunicación visual</strong> de productos en locales.
                    </p>

                    <div class="row mb-3">
                        <div class="col-md-12 text-center">
                            <a href="#comu" class="btn btn-rounded btn-primary m-1">Comunicación</a>
                            <a href="#exhi" class="btn btn-rounded btn-success m-1">Exhibición</a>
                            <a href="#ot" class="btn btn-rounded btn-info m-1">Otros</a>
                        </div>
                    </div>

                    <!-- ==================== COMUNICACIÓN ==================== -->
                    <h4 id="comu" class="mt-4">📢 Comunicación</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Imagen</th>
                                    <th>Ubicación</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>PPE</th>
                                    <td>PORTAPRECIO ESTANTE negro</td>
                                    <td><img style="width:100px" src="../../public/img/ppe.png" alt=""></td>
                                    <td>Estantes</td>
                                    <td>Se completa con una placa doblada 15x21/A4/A3</td>
                                </tr>
                                <tr>
                                    <th>PPM</th>
                                    <td>PORTAPRECIO MESA negro</td>
                                    <td><img style="width:100px" src="../../public/img/ppm.png" alt=""></td>
                                    <td>Mesas</td>
                                    <td>Se completa con una placa doblada 15x21</td>
                                </tr>
                                <tr>
                                    <th>PPP</th>
                                    <td>PORTAPRECIO PERCHERO negro</td>
                                    <td><img style="width:100px" src="../../public/img/ppp.png" alt=""></td>
                                    <td>Percheros</td>
                                    <td>Se completa con una placa doblada 15x21</td>
                                </tr>
                                <tr>
                                    <th>PPV</th>
                                    <td>PORTAPRECIO VIDRIERA COBREADO</td>
                                    <td><img style="width:100px" src="../../public/img/ppv.png" alt=""></td>
                                    <td>Vidriera</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>PPD</th>
                                    <td>PORTAPRECIO DENIM (chapa L)</td>
                                    <td><img style="width:100px" src="../../public/img/ppd.png" alt=""></td>
                                    <td>Mueble DENIM</td>
                                    <td>Se completa con imán de calce</td>
                                </tr>
                                <tr>
                                    <th>MP</th>
                                    <td>MARCO CON PIE</td>
                                    <td><img style="width:100px" src="../../public/img/mp.png" alt=""></td>
                                    <td>Mesa DENIM</td>
                                    <td>Se completa con una placa simple A4</td>
                                </tr>
                                <tr>
                                    <th>PMA4</th>
                                    <td>PORTARRETRATO MADERA A4</td>
                                    <td><img style="width:100px" src="../../public/img/pma4.png" alt=""></td>
                                    <td>Vidriera y Mueble caja</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>SCA3</th>
                                    <td>SOPORTE COMUNICACION A3 negro</td>
                                    <td><img style="width:100px" src="../../public/img/sca3.png" alt=""></td>
                                    <td>Ingreso local</td>
                                    <td>Se completa con dos placas simples A3</td>
                                </tr>
                                <tr>
                                    <th>PROMO</th>
                                    <td>SOPORTE HAY PROMO</td>
                                    <td><img style="width:100px" src="../../public/img/soporte_promo.png" alt=""></td>
                                    <td>Vidriera</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>CALCE</th>
                                    <td>SOPORTE CALCE JEANS</td>
                                    <td><img style="width:100px" src="../../public/img/soporte_calce.png" alt=""></td>
                                    <td>Jeanero</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>PDA3</th>
                                    <td>PLACA SIMPLE PET TAMAÑO A3</td>
                                    <td><img style="width:100px" src="../../public/img/pda3.png" alt=""></td>
                                    <td>PPE</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>PDA4</th>
                                    <td>PLACA DOBLADA PET TAMAÑO A4</td>
                                    <td><img style="width:100px" src="../../public/img/ppe.png" alt=""></td>
                                    <td>PPE</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>PD1521</th>
                                    <td>PLACA SIMPLE PET TAMAÑO 15x21 cm</td>
                                    <td><img style="width:100px" src="../../public/img/pd1521.png" alt=""></td>
                                    <td>PPE/PPP/PPM</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>PSA3</th>
                                    <td>PLACA SIMPLE PET A3</td>
                                    <td><img style="width:100px" src="../../public/img/psa3.png" alt=""></td>
                                    <td>SCA3</td>
                                    <td>Van dos por soporte</td>
                                </tr>
                                <tr>
                                    <th>TLV</th>
                                    <td>TOTEM ACRILICO PARA LEGALES</td>
                                    <td><img style="width:100px" src="../../public/img/tlv.png" alt=""></td>
                                    <td>Vidriera</td>
                                    <td>Comunicación legal</td>
                                </tr>
                                <tr>
                                    <th>AMKH</th>
                                    <td>ACRILICO MUEBLE K HORIZONTAL</td>
                                    <td><img style="width:100px" src="../../public/img/amkh.png" alt=""></td>
                                    <td>Mueble K Cobre</td>
                                    <td>Pueden contener comunicación de ambos lados</td>
                                </tr>
                                <tr>
                                    <th>AMKV</th>
                                    <td>ACRILICO MUEBLE K VERTICAL</td>
                                    <td><img style="width:100px" src="../../public/img/amkv.png" alt=""></td>
                                    <td>Mueble K Cobre</td>
                                    <td>Pueden contener comunicación de ambos lados</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ==================== EXHIBICIÓN ==================== -->
                    <h4 id="exhi" style="margin-top: 50px; padding-top: 10px;">🛍️ Exhibición</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Imagen</th>
                                    <th>Ubicación</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>T1</th>
                                    <td>PERCHERO MESA COBREADO</td>
                                    <td><img style="width:100px" src="../../public/img/t1.png" alt=""></td>
                                    <td>Mesas</td>
                                    <td>Es el más alto. Altura 120 cm</td>
                                </tr>
                                <tr>
                                    <th>T2</th>
                                    <td>PERCHERO MESA COBREADO</td>
                                    <td><img style="width:100px" src="../../public/img/t2.png" alt=""></td>
                                    <td>Mesas</td>
                                    <td>Es el intermedio. Altura 80 cm</td>
                                </tr>
                                <tr>
                                    <th>T3</th>
                                    <td>PERCHERO MESA COBREADO</td>
                                    <td><img style="width:100px" src="../../public/img/t3.png" alt=""></td>
                                    <td>Mesas</td>
                                    <td>Es el más bajo. Altura 60 cm</td>
                                </tr>
                                <!-- ... seguir igual ... -->
                            </tbody>
                        </table>
                    </div>

                    <!-- ==================== OTROS ==================== -->
                    <h4 id="ot" style="margin-top: 50px; padding-top: 10px;">🔧 Otros</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Imagen</th>
                                    <th>Ubicación</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>CMC</th>
                                    <td>COMPLEMENTO MUEBLE CAJA</td>
                                    <td><img style="width:100px" src="../../public/img/cmc.png" alt=""></td>
                                    <td>Mueble Caja</td>
                                    <td>Uno por Computadora</td>
                                </tr>
                                <tr>
                                    <th>PP</th>
                                    <td>PORTAPERCHAS</td>
                                    <td><img style="width:100px" src="../../public/img/pp.png" alt=""></td>
                                    <td>Depósito / Zona probadores</td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div><!--.box-typical-->

            </div><!--.container-fluid-->
        </div><!--.page-content-->

        <?php require_once("../MainJs/js.php"); ?>
        <script type="text/javascript" src="../notificacion.js"></script>
    </body>
    </html>

<?php
} else {
    header("Location:" . Conectar::ruta() . "index.php");
}
?>
