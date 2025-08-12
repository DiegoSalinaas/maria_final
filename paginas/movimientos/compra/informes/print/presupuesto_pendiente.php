
<?php
require_once '../../../../../conexion/db.php';

$base_datos = new DB();


$detalle = $base_datos->conectar()->prepare("      SELECT
pc.cod_presupuesto as cod_presupuesto_comp,
pc.fecha_presupuesto as fecha_presu,
pc.fecha_vencimiento,
pc.estado_presupuesto as estado,
pv.pro_razonsocial as nom_ape_prov,
pv.cod_proveedor,
u.usuario_alias as nombre_apellido,
SUM(dp.cantidad * dp.precio_unit) as total
from presupuesto  pc
join proveedor pv 
on pv.cod_proveedor = pc.cod_proveedor
join usuario u 
on u.cod_usuario = pc.cod_usuario 
join det_presupuesto  dp 
on dp.cod_presupuesto  = pc.cod_presupuesto  
WHERE pc.fecha_presupuesto BETWEEN :desde and :hasta and pc.estado_presupuesto = 'PENDIENTE'
group by dp.cod_presupuesto
order by  pc.cod_presupuesto  desc
    ");

$detalle->bindParam(':desde', $_GET['desde']);
$detalle->bindParam(':hasta', $_GET['hasta']);
$detalle->execute();
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Presupuesto de Compra</title>
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
                            <th>Fecha vencimiento</th>
                            <th>Proveedor</th>
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
                                    <td><?= $fila['cod_presupuesto_comp'] ?></td>
                                    <td><?= $fila['fecha_presu'] ?></td>
                                    <td><?= $fila['fecha_vencimiento'] ?></td>
                                    <td><?= $fila['nom_ape_prov'] ?></td>
                                    <td><?= number_format($fila['total'], 0, ',', '.') ?></td>
                                </tr>

                                <?php
                               
                            }
                        }else{
                            echo "<tr><th colspan='4'>NO HAY REGISTROS</th></tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align: left;">Total</th>
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


