
<?php
require_once '../../../../../conexion/db.php';

$base_datos = new DB();


$detalle = $base_datos->conectar()->prepare("select 
 c.cod_registro as cod_compra,
 c.fecha_compra ,
  c.estado_registro as estado,
 c.condicion ,
 c.nro_factura,
 sum(dc.cantidad * dc.costo) as total,
 p.pro_razonsocial as razon_social_prov
 from compra c  
 join detalle_compra dc on dc.cod_compra  = c.cod_registro 
 join proveedor p on p.cod_proveedor  = c.cod_proveedor 
 WHERE c.fecha_compra BETWEEN :desde and :hasta  and c.estado_registro = 'ACTIVO'
 group  by c.cod_registro 
 order by  c.cod_registro  desc 
    ");

$detalle->bindParam(':desde', $_GET['desde']);
$detalle->bindParam(':hasta', $_GET['hasta']);
$detalle->execute();
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Factura de Compra</title>
        <link href="../../../../../vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <style>
            @media print {
                .no-print {
                    display: none;
                }
                .print-only {
                    display: block;
                }
                body {
                    font-size: 12pt;
                    margin: 0;
                }
                .container {
                    width: 100%;
                    margin: 0;
                    padding: 0;
                }
            }
            body {
                margin: 20px;
                font-family: Arial, sans-serif;
                background-color: #fff;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .header img {
                width: 80%;
                margin-bottom: 20px;
            }
            .header h3 {
                font-weight: bold;
            }
            .table th, .table td {
                vertical-align: middle;
                text-align: center;
            }
            .table tfoot {
                font-weight: bold;
                font-size: 18px;
            }
            .total-general {
                text-align: right;
                font-size: 24px;
                margin-top: 20px;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <table class="table">
                <style>
                    br{
                         display: block;
                        margin-top: 2px; /* Ajusta el espacio encima */
                        margin-bottom: 2px; /* Ajusta el espacio debajo */
                    }
                </style>
                <thead>
                    <tr>
                        <th style="width: 100%;"><img src="../../../../img/membrete.png" alt="Membrete"></th>
                       
                    </tr>
                </thead>
            </table>

            <hr>
            <div class="row">
                
                <div class="col-md-3">
                    <label>Fecha desde:</label>
                    <span><?= $_GET['desde'] ?></span>
                </div>
                <div class="col-md-3">
                    <label>Fecha hasta:</label>
                    <span><?= $_GET['hasta'] ?></span>
                </div>
                
            </div>
         

            <hr>
            <div class="row">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nro</th>
                            <th>Fecha</th>
                            <th>Nro. Factura</th>
                            <th>Proveedor</th>
                            <th>Condicion</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        
                        if ($detalle->rowCount()) {
                            foreach ($detalle as $fila) {
                                
                                $total += intval($fila['total']);
                                
                                ?>
                                <tr>
                                    <td><?= $fila['cod_compra'] ?></td>
                                    <td><?= $fila['fecha_compra'] ?></td>
                                    <td><?= $fila['nro_factura'] ?></td>
                                    <td><?= $fila['razon_social_prov'] ?></td>
                                    <td><?= $fila['condicion'] ?></td>
                                    <td><?= number_format($fila['total'], 0, ',', '.') ?></td>
                                </tr>

                                <?php
                               
                            }
                        }else{
                            echo "<tr><th colspan='6'>NO HAY REGISTROS</th></tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" style="text-align: left;">Total</th>
                            <th> <?=number_format($total, 0, ',', '.')?></th>
                        </tr>
                    </tfoot>
                   

                </table>
            </div>

        </div>

        <script src="../../../../../assets/plugins/bootstrap/bootstrap.min.js"></script>
        <script>
//            window.print();
        </script>
    </body>
</html>


