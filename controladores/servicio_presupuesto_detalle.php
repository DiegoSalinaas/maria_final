<?php

require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    $json_datos = json_decode($lista, true);
    $db = new DB();
    $query = $db->conectar()->prepare("INSERT INTO detalle_presupuesto
        (presupuesto_id, descripcion, cantidad, precio_unitario, subtotal)
        VALUES (:presupuesto_id, :descripcion, :cantidad, :precio_unitario, :subtotal)");
    $query->execute($json_datos);
}

if (isset($_POST['leer'])) {
    leer();
}

function leer() {
    $db = new DB();
    $query = $db->conectar()->prepare("SELECT * FROM detalle_presupuesto");
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

if (isset($_POST['id'])) {
    id($_POST['id']);
}

function id($id) {
    $db = new DB();
    $query = $db->conectar()->prepare("SELECT * FROM detalle_presupuesto WHERE id = $id");
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

if (isset($_POST['actualizar'])) {
    actualizar($_POST['actualizar']);
}

function actualizar($lista) {
    $json_datos = json_decode($lista, true);
    $db = new DB();
    $query = $db->conectar()->prepare("UPDATE detalle_presupuesto SET
        presupuesto_id=:presupuesto_id,
        descripcion=:descripcion,
        cantidad=:cantidad,
        precio_unitario=:precio_unitario,
        subtotal=:subtotal
        WHERE id=:id");
    $query->execute($json_datos);
}

if (isset($_POST['eliminar'])) {
    eliminar($_POST['eliminar']);
}

function eliminar($id) {
    $db = new DB();
    $query = $db->conectar()->prepare("DELETE FROM detalle_presupuesto WHERE id = $id");
    $query->execute();
}

?>
