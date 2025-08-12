<?php
require_once '../../../../conexion/db.php';

$base_datos = new DB();
$query = $base_datos->conectar()->prepare("  
  select 
 nc.cod_nota_compra as cod_nota_compra,
 nc.fecha_cred  as fecha_nota,
 nc.fecha_vencimiento as fecha_venc_timbrado,
 nc.timbrado ,
 nc.nro_factura as nro_nota,
 nc.estado_cre as estado ,
 nc.tipo as tipo_nota,
 nc.motivo ,
 SUM(dnc.costo * dnc.cantidad) as total,
 p.pro_razonsocial  as razon_social_prov,
 p.pro_ruc as ruc_prov 
 from nota_compra  nc  
 join deta_nota dnc on dnc.cod_nota  =  nc.cod_nota_compra 
 join compra  c on c.cod_registro  =  nc.cod_registro 
 join proveedor  p on p.cod_proveedor  =  c.cod_proveedor 
  where nc.cod_nota_compra = :id");

$detalle = $base_datos->conectar()->prepare(" select 
 m.descripcion  as nombre_material,
 dpc.cantidad ,
 dpc.costo ,
 IF(m.tipo_iva = 10, dpc.costo * dpc.cantidad, 0) as iva10,
 IF(m.tipo_iva = 5, dpc.costo * dpc.cantidad, 0) as iva5,
 IF(m.tipo_iva = 0, dpc.costo  * dpc.cantidad, 0) as exenta
 from deta_nota   dpc 
 join insumos m ON m.cod_insumos  = dpc.cod_insumo
 where dpc.cod_nota  =  :id");

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
        <title>Factura de Compra</title>
        <link href="../../../../vendors/bootstrap/css/bootstrap.rtl.min.css" rel="stylesheet">
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
                        <th style="width: 60%;"><img src="../../../../img/membrete.png" alt="Membrete"></th>
                        <th style="width: 40%;">
                            <span style="text-align: center; font-size: 11px; line-height: 0;"> Timbrado <br>
                                <?= $arreglo->timbrado ?> <br>
                                Fecha Vencimiento <br>
                                <?= $arreglo->fecha_venc_timbrado ?> <br></span>
                            <span style="font-size: 20px;"> NRO NOTA DE <?=$arreglo->tipo_nota?> <br>
                                <?= $arreglo->nro_nota ?></span>


                        </th>
                    </tr>
                </thead>
            </table>

            <hr>
            <div class="row">
                
                <div class="col-md-3">
                    <label>Fecha:</label>
                    <span><?= $arreglo->fecha_nota ?></span>
                </div>
                
            </div>
            <div class="row">
                <div class="col-md-5 ">
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
                            <th>Costo</th>
                            <th>Exenta</th>
                            <th>I.V.A. 5%</th>
                            <th>I.V.A. 10%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        $totalexenta = 0;
                        $totaliva5 = 0;
                        $totaliva10 = 0;
                        if ($detalle->rowCount()) {
                            foreach ($detalle as $fila) {
                                $totalexenta += intval($fila['exenta']);
                                $totaliva5 += intval($fila['iva5']);
                                $totaliva10 += intval($fila['iva10']);
                                $total += intval($fila['iva10']) + intval($fila['iva5']) + intval($fila['exenta']);
                                
                                ?>
                                <tr>
                                    <td><?= $fila['nombre_material'] ?></td>
                                    <td><?= number_format($fila['cantidad'], 0, ',', '.') ?></td>
                                    <td><?= number_format($fila['costo'], 0, ',', '.') ?></td>
                                    <td><?= number_format($fila['exenta'], 0, ',', '.') ?></td>
                                    <td><?= number_format($fila['iva5'], 0, ',', '.') ?></td>
                                    <td><?= number_format($fila['iva10'], 0, ',', '.') ?></td>
                                </tr>

                                <?php
                               
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="text-align: left;">Total</th>
                            <th><?= number_format($totalexenta, 0, ',', '.') ?></th>
                            <th><?= number_format($totaliva5, 0, ',', '.') ?></th>
                            <th><?= number_format($totaliva10, 0, ',', '.') ?></th>
                        </tr>
                        <tr style="font-size: 12px;">
                            <th colspan="1" style="text-align: left;">Impuestos</th>
                            <th colspan="2" >I.V.A. 5% (<?= number_format(round($totaliva5 / 21), 0, ',', '.')  ?>) </th>
                            <th colspan="2"> I.V.A. 10% (<?= number_format(round($totaliva10 / 11), 0, ',', '.') ?>) </th>
                            <th > TOTAL I.V.A. (<?= number_format(round($totaliva10 / 11) + round($totaliva5 / 21), 0, ',', '.') ?>) </th>
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
