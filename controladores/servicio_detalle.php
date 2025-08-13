<?php

require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    $json_datos = json_decode($lista, true);
    $db = new DB();
    $query = $db->conectar()->prepare("INSERT INTO detalle_servicio
        (servicio_id, descripcion, cantidad, costo_unitario, subtotal)
        VALUES (:servicio_id, :descripcion, :cantidad, :costo_unitario, :subtotal)");
    $query->execute($json_datos);
}

if (isset($_POST['leer'])) {
    leer();
}

function leer() {
    $db = new DB();
    $query = $db->conectar()->prepare("SELECT * FROM detalle_servicio");
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
    $query = $db->conectar()->prepare("SELECT * FROM detalle_servicio WHERE id = $id");
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
    $query = $db->conectar()->prepare("UPDATE detalle_servicio SET
        servicio_id=:servicio_id,
        descripcion=:descripcion,
        cantidad=:cantidad,
        costo_unitario=:costo_unitario,
        subtotal=:subtotal
        WHERE id=:id");
    $query->execute($json_datos);
}

if (isset($_POST['eliminar'])) {
    eliminar($_POST['eliminar']);
}

function eliminar($id) {
    $db = new DB();
    $query = $db->conectar()->prepare("DELETE FROM detalle_servicio WHERE id = $id");
    $query->execute();
}

?>
