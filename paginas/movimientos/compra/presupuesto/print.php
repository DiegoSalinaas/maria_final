<?php
require_once '../../../../conexion/db.php';

$base_datos = new DB();
$query = $base_datos->conectar()->prepare("SELECT
pc.cod_presupuesto,
pc.fecha_presupuesto,
pc.fecha_vencimiento,
pc.estado_presupuesto,
pv.pro_razonsocial,
pv.pro_ruc,
pv.cod_proveedor,
u.usuario_alias,
SUM(dp.cantidad * dp.precio_unit) as total
from presupuesto pc
join proveedor pv 
on pv.cod_proveedor = pc.cod_proveedor
join usuario u 
on u.cod_usuario = pc.cod_usuario
join det_presupuesto dp 
on dp.cod_presupuesto  = pc.cod_presupuesto 
where pc.cod_presupuesto =  :id
group by dp.cod_presupuesto
order by  pc.cod_presupuesto  desc");

$detalle = $base_datos->conectar()->prepare("select 
 dpc.cod_insumos,
 m.descripcion,
 dpc.cantidad,
 dpc.precio_unit,
 (dpc.cantidad *  dpc.precio_unit) as total
 from det_presupuesto dpc 
 join insumos m ON m.cod_insumos  = dpc.cod_insumos 
 where dpc.cod_presupuesto = :id");
//$detalle = $base_datos->conectar()->prepare("select 
// m.nombre_material ,
// tm.descripcion  as tipo_material,
// dpc.cantidad ,
// dpc.costo ,
// dpc.cantidad  * dpc.costo  as total
// from detalle_presupuesto  dpc 
// join materiales m ON m.cod_material  = dpc.cod_material 
// join tipo_material tm on tm.cod_tipo_material  = m.cod_tipo_material 
// where dpc.cod_presupuesto_comp = :id");

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
        <title>Presupuesto de Compra</title>
       <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
                <h3>Presupuesto de Compra</h3>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <label>Nro de Presupuesto:</label>
                    <span><?= $arreglo->cod_presupuesto ?></span>
                </div>
               
                <div class="col-md-3 text-right">
                    <label>Fecha:</label>
                    <span><?= $arreglo->fecha_presupuesto ?></span>
                </div>
                <div class="col-md-3 text-right">
                    <label>Fecha vencimiento:</label>
                    <span><?= $arreglo->fecha_vencimiento ?></span>
                </div>
                <div class="col-md-3 ">
                    <label>Proveedor:</label>
                    <span><?= $arreglo->pro_razonsocial ?></span>
                </div>
                <div class="col-md-3 ">
                    <label>Ruc:</label>
                    <span><?= $arreglo->pro_ruc ?></span>
                </div>
            </div>
            
            <hr>
            <div class="row">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Descripcion</th>
                            <th>Cantidad</th>
                            <th>Costo</th>
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
                                    <td><?= $fila['descripcion'] ?></td>
                                    <td><?= number_format($fila['cantidad'], 0, ',', '.') ?></td>
                                    <td><?= number_format($fila['precio_unit'], 0, ',', '.') ?></td>
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
        
        <!--<script src="../../../../assets/plugins/bootstrap/bootstrap.min.js"></script>-->
        <script>
            window.print();
        </script>
    </body>
</html>
