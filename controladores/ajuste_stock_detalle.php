<?php

require_once '../conexion/db.php';
//aasda
if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    //crea un arreglo del texto que se le pasa
    session_start();
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("INSERT INTO deta_ajuste
(cod_ajuste, cod_insumos, cantidad, tipo)
VALUES(:cod_ajuste_stock, :cod_material, :cantidad_nueva, :tipo );");

    $query->execute($json_datos);
}
