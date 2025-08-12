<?php
require_once '../../../../conexion/db.php';

$base_datos = new DB();
$query = $base_datos->conectar()->prepare("select 
nr.cod_remision_comp  as cod_nota_remision,
nr.fecha  as fecha_registro,
nr.nro_nota ,
p.pro_razonsocial as razon_social_prov,
nr.estado_remision_com as estado ,
nr.punto_salida ,
nr.punto_llegada ,
nr.motivo ,
nr.chofer  as nom_ape,
u.usuario_alias as usuario
from nota_remision_compra  nr 
join compra c on c.cod_registro  = nr.cod_registro 
join proveedor   p on p.cod_proveedor  = c.cod_proveedor 
join usuario u on u.cod_usuario  = nr.cod_usuario  
where nr.cod_remision_comp  = :id
order by nr.cod_remision_comp  desc ");

$detalle = $base_datos->conectar()->prepare("select 
 m.descripcion as nombre_insumo,
 dpc.cantidad as cantidad,
 dpc.cantidad_factura 
 from det_nota_remision  dpc 
 join insumos m ON m.cod_insumos  = dpc.cod_insumos 
 where dpc.cod_remision  =  :id");

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
        <title>Ajuste de Stock</title>
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
                <h3>Nota de Remision</h3>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <label>Nro de Ajuste:</label>
                    <span><?= $arreglo->cod_nota_remision ?></span>
                </div>
               
                <div class="col-md-3">
                    <label>Fecha:</label>
                    <span><?= $arreglo->fecha_registro ?></span>
                </div>
                <div class="col-md-3 ">
                    <label>Usuario:</label>
                    <span><?= $arreglo->usuario ?></span>
                </div>
                <div class="col-md-3 ">
                    <label>Proveedor:</label>
                    <span><?= $arreglo->razon_social_prov ?></span>
                </div>
                <div class="col-md-3 ">
                    <label>Chofer:</label>
                    <span><?= $arreglo->nom_ape ?></span>
                </div>
                <div class="col-md-3 ">
                    <label>Punto salida:</label>
                    <span><?= $arreglo->punto_salida ?></span>
                </div>
                <div class="col-md-3 ">
                    <label>Punto llegada:</label>
                    <span><?= $arreglo->punto_llegada ?></span>
                </div>
                <div class="col-md-3 ">
                    <label>Motivo:</label>
                    <span><?= $arreglo->motivo ?></span>
                </div>
            </div>
            
            <hr>
            <div class="row">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Descripcion</th>
                            <th>Cantidad Factura</th>
                            <th>Cantidad recibida</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                       
                        if ($detalle->rowCount()) {
                            foreach ($detalle as $fila) {
                                
                                ?>
                                <tr>
                                    <td><?= $fila['nombre_insumo'] ?></td>
                                    <td><?= number_format($fila['cantidad_factura'], 0, ',', '.') ?></td>
                                    <td><?= number_format($fila['cantidad'], 0, ',', '.') ?></td>
                                </tr>

                                <?php
//                                $total += (is_numeric($fila['precio_unitario']) * $fila['cantidad']) ? $fila['precio_unitario'] : 0;
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
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
