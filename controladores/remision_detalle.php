<?php
require_once '../conexion/db.php';
//aasda
if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    //crea un arreglo del texto que se le pasa
   
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("INSERT INTO det_nota_remision
(cod_remision, cod_insumos, cantidad, cantidad_factura)
VALUES(:cod_nota_remision, :cod_producto, :cantidad,  :cantidad_factura);");

    $query->execute($json_datos);
    

}

