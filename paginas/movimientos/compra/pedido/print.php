<?php
require_once '../../../../conexion/db.php';

$base_datos = new DB();
$query = $base_datos->conectar()->prepare("SELECT 
    pc.cod_pedido_compra as nro,
    DATE_FORMAT(pc.fecha_pedido, '%d/%m/%Y') AS fecha_pedido,
    pc.estado_pedido_compra,
    u.usuario_alias,
    u.cod_usuario,
    s.nombre_sucur,
    s.cod_sucursal
FROM 
    pedido_compra pc
JOIN 
    usuario u 
ON 
    u.cod_usuario = pc.cod_usuario 
JOIN 
    sucursal s 
ON 
    s.cod_sucursal = pc.cod_sucursal
            where pc.cod_pedido_compra  = :id");

$detalle = $base_datos->conectar()->prepare("select 
 m.descripcion,
 dpc.cantidad
 from det_pedido_compra dpc 
 join insumos m ON m.cod_insumos  = dpc.cod_insumos
 where dpc.cod_pedido_compra = :id");

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
        <title>Pedido de Compra</title>
<!--        <link href="../../../../assets/plugins/bootstrap/bootstrap.min.css" rel="stylesheet">-->
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
                <!--<img src="../../../../img/membrete.png" alt="Membrete">-->
                <h3>Pedido de Compra</h3>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <label>Nro de Pedido:</label>
                    <span><?= $arreglo->nro ?></span>
                </div>
               
                <div class="col-md-4 ">
                    <label>Fecha:</label>
                    <span><?= $arreglo->fecha_pedido ?></span>
                </div>
                <div class="col-md-4 text-right">
                    <label>Usuario:</label>
                    <span><?= $arreglo->usuario_alias ?></span>
                </div>
                
            </div>
            
            <hr>
            <div class="row">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Descripcion</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                       
                        if ($detalle->rowCount()) {
                            foreach ($detalle as $fila) {
                                
                                ?>
                                <tr>
                                    <td><?= $fila['descripcion'] ?></td>
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
        
        <!--<script src="../../../../assets/plugins/bootstrap/bootstrap.min.js"></script>-->
        <script>
            window.print();
        </script>
    </body>
</html>
