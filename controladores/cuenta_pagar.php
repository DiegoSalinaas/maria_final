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
    $query = $base_datos->conectar()->prepare("INSERT INTO cuenta_pagar
(cod_cuenta_pagar, cod_compra, monto_pagar, fecha_vencimiento, saldo, estado)
VALUES((SELECT COALESCE(MAX(c.cod_cuenta_pagar), 0) + 1 FROM cuenta_pagar c), 
:cod_compra, :monto_pagar, :fecha_pago, :saldo, :estado);");

    $query->execute($json_datos);
    

}