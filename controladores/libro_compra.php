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
    $query = $base_datos->conectar()->prepare("INSERT INTO libro_compras
(cod_libro_com,  id_compra, iva5, iva10, exenta, gravada5, gravada10, total)
VALUES((SELECT COALESCE(MAX(c.cod_libro_com), 0) + 1 FROM libro_compras c), :cod_compra, 
:iva5, :iva10,  :exenta, :grav5, :grav10, :total);");

    $query->execute($json_datos);
    

}