<?php

require_once '../conexion/db.php';

if (isset($_POST['leer_sucursal_activos'])) {
    leer_sucursal_activos();
}

function leer_sucursal_activos() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare("SELECT `id_sucursal`, `suc_descripcion`, `suc_estado`
     FROM `sucursal`
");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}