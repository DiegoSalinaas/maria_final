<?php
require_once '../../../../conexion/db.php';

$base_datos = new DB();
$query = $base_datos->conectar()->prepare(" SELECT
LPAD(pc.cod_orden , 7, '0') as nro,
pc.oc_fecha_emision as fecha_orden,
pc.oc_estado as estado,
pv.pro_razonsocial  as razon_social_prov,
pv.pro_ruc as ruc_prov,
pv.pro_razonsocial as nom_ape_prov,
pv.cod_proveedor,
u.usuario_alias as nombre_apellido,
SUM(dp.cantidad * dp.prec_uni) as total
from orden_compra pc
join proveedor pv 
on pv.cod_proveedor = pc.cod_proveedor 
join usuario  u 
on u.cod_usuario = pc.cod_usuario 
join det_orden  dp 
on dp.cod_orden  = pc.cod_orden 
where pc.cod_orden =  :id");

$detalle = $base_datos->conectar()->prepare("select
 p.nombre  as nombre_producto,
 dpc.cantidad ,
 dpc.prec_uni as costo,
 dpc.cantidad  * dpc.prec_uni  as total
 from det_orden  dpc
 join producto p ON p.cod_producto  = dpc.cod_producto
 where dpc.cod_orden =:id");

$query->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$detalle->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$query->execute();
$detalle->execute();
$arreglo = $query->fetch(PDO::FETCH_OBJ);
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Orden de Compra</title>
        <link href="../../../../vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
            <div class="header">
                <img src="../../../../img/membrete.png" alt="Membrete">
                <h3>Orden de Compra</h3>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <label>Nro de Presupuesto:</label>
                    <span><?= $arreglo->nro ?></span>
                </div>
               
                <div class="col-md-3 text-right">
                    <label>Fecha:</label>
                    <span><?= $arreglo->fecha_orden ?></span>
                </div>
                <div class="col-md-3 ">
                    <label>Proveedor:</label>
                    <span><?= $arreglo->razon_social_prov ?></span>
                </div>
                <div class="col-md-3 ">
                    <label>Ruc:</label>
                    <span><?= $arreglo->ruc_prov ?></span>
                </div>
            </div>
            
            <hr>
            <div class="row">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Descripcion</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                       $total = 0;
                        if ($detalle->rowCount()) {
                            foreach ($detalle as $fila) {
                                
                                ?>
                                <tr>
                                    <td><?= $fila['nombre_producto'] ?></td>
                                    <td><?= number_format($fila['cantidad'], 0, ',', '.') ?></td>
                                    <td><?= number_format($fila['costo'], 0, ',', '.') ?></td>
                                    <td><?= number_format($fila['total'], 0, ',', '.') ?></td>
                                </tr>

                                <?php
                               $total += intval($fila['total']);
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="text-align: left;">Total</th>
                            <th><?=number_format($total, 0, ',', '.')?></th>
                        </tr>
                    </tfoot>

                </table>
            </div>

        </div>
        
        <script src="../../../../assets/plugins/bootstrap/bootstrap.min.js"></script>
        <script>
            window.print();
        </script>
    </body>
</html>
