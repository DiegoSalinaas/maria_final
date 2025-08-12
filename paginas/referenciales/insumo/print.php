<?php
require_once '../../../../conexion/db.php';

$base_datos = new DB();
$query = $base_datos->conectar()->prepare("select
            as2.cod_ajuste_stock ,
            as2.tipo_ajuste ,
            as2.fecha_ajuste ,
            as2.estado ,
            s.suc_descripcion ,
            u.nombre_apellido 
            from ajuste_stock as2 
            join sucursal s on s.id_sucursal  = as2.id_sucursal 
            join usuarios u on u.cod_usuario  = as2.cod_usuario
            where as2.cod_ajuste_stock  = :id");

$detalle = $base_datos->conectar()->prepare("select 
 m.nombre_insumo ,
 tm.descripcion  as tipo_insumo,
 dpc.cantidad_nueva as cantidad
 from detalle_ajuste_stock  dpc 
 join insumos m ON m.cod_insumo  = dpc.cod_insumo 
 join tipo_insumo tm on tm.cod_tipo_insumo  = m.cod_tipo_insumo 
 where dpc.cod_ajuste_stock =  :id");

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
        <link href="../../../../assets/plugins/bootstrap/bootstrap.min.css" rel="stylesheet">
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
                <h3>Ajuste de Stock</h3>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <label>Nro de Ajuste:</label>
                    <span><?= $arreglo->cod_ajuste_stock ?></span>
                </div>
               
                <div class="col-md-3 text-right">
                    <label>Fecha:</label>
                    <span><?= $arreglo->fecha_ajuste ?></span>
                </div>
                <div class="col-md-3 ">
                    <label>Usuario:</label>
                    <span><?= $arreglo->nombre_apellido ?></span>
                </div>
             
                <div class="col-md-3 ">
                    <label>Tipo:</label>
                    <span><?= $arreglo->tipo_ajuste ?></span>
                </div>
            </div>
            
            <hr>
            <div class="row">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Descripcion</th>
                            <th>Tipo Insumo</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                       
                        if ($detalle->rowCount()) {
                            foreach ($detalle as $fila) {
                                
                                ?>
                                <tr>
                                    <td><?= $fila['nombre_insumo'] ?></td>
                                    <td><?= $fila['tipo_insumo'] ?></td>
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
