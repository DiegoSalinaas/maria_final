
<?php
require_once '../../../../../conexion/db.php';

$base_datos = new DB();


$detalle = $base_datos->conectar()->prepare(" SELECT 
    pc.cod_pedido_compra,
    DATE_FORMAT(pc.fecha_pedido, '%d/%m/%Y') AS fecha_pedido,
    pc.estado_pedido_compra as estado,
    u.usuario_alias as nombre_apellido,
    u.cod_usuario
FROM 
    pedido_compra pc
JOIN 
    usuario u 
ON 
    u.cod_usuario = pc.cod_usuario 
    WHERE pc.fecha_pedido BETWEEN :desde and :hasta and pc.estado_pedido_compra = 'ANULADO'");

$detalle->bindParam(':desde', $_GET['desde']);
$detalle->bindParam(':hasta', $_GET['hasta']);
$detalle->execute();
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Pedido de Compra</title>
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
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        
                        if ($detalle->rowCount()) {
                            foreach ($detalle as $fila) {
                                
//                                $total += intval($fila['iva10']) + intval($fila['iva5']) + intval($fila['exenta']);
                                
                                ?>
                                <tr>
                                    <td><?= $fila['cod_pedido_compra'] ?></td>
                                    <td><?= $fila['fecha_pedido'] ?></td>
                                    <td><?= $fila['nombre_apellido'] ?></td>
                                </tr>

                                <?php
                               
                            }
                        }else{
                            echo "<tr><th colspan='4'>NO HAY REGISTROS</th></tr>";
                        }
                        ?>
                    </tbody>
                   

                </table>
            </div>

        </div>

        <script src="../../../../../assets/plugins/bootstrap/bootstrap.min.js"></script>
        <script>
//            window.print();
        </script>
    </body>
</html>


