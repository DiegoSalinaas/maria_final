<?php

require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    $json_datos = json_decode($lista, true);
    $db = new DB();
    $query = $db->conectar()->prepare("INSERT INTO presupuestos
        (cliente_id, fecha_emision, fecha_vencimiento, total_estimado, estado, observaciones)
        VALUES (:cliente_id, :fecha_emision, :fecha_vencimiento, :total_estimado, :estado, :observaciones)");
    $query->execute($json_datos);
}

if (isset($_POST['leer'])) {
    leer();
}

function leer() {
    $db = new DB();
    $query = $db->conectar()->prepare("SELECT * FROM presupuestos");
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
    $query = $db->conectar()->prepare("SELECT * FROM presupuestos WHERE id = $id");
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
    $query = $db->conectar()->prepare("UPDATE presupuestos SET
        cliente_id=:cliente_id,
        fecha_emision=:fecha_emision,
        fecha_vencimiento=:fecha_vencimiento,
        total_estimado=:total_estimado,
        estado=:estado,
        observaciones=:observaciones
        WHERE id=:id");
    $query->execute($json_datos);
}

if (isset($_POST['eliminar'])) {
    eliminar($_POST['eliminar']);
}

function eliminar($id) {
    $db = new DB();
    $query = $db->conectar()->prepare("DELETE FROM presupuestos WHERE id = $id");
    $query->execute();
}

?>
