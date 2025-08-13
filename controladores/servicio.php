<?php

require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    $json_datos = json_decode($lista, true);
    $db = new DB();
    $query = $db->conectar()->prepare("INSERT INTO servicios
        (cliente_id, presupuesto_id, fecha_inicio, fecha_fin, estado, descripcion, costo_final)
        VALUES (:cliente_id, :presupuesto_id, :fecha_inicio, :fecha_fin, :estado, :descripcion, :costo_final)");
    $query->execute($json_datos);
}

if (isset($_POST['leer'])) {
    leer();
}

function leer() {
    $db = new DB();
    $query = $db->conectar()->prepare("SELECT * FROM servicios");
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
    $query = $db->conectar()->prepare("SELECT * FROM servicios WHERE id = $id");
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
    $query = $db->conectar()->prepare("UPDATE servicios SET
        cliente_id=:cliente_id,
        presupuesto_id=:presupuesto_id,
        fecha_inicio=:fecha_inicio,
        fecha_fin=:fecha_fin,
        estado=:estado,
        descripcion=:descripcion,
        costo_final=:costo_final
        WHERE id=:id");
    $query->execute($json_datos);
}

if (isset($_POST['eliminar'])) {
    eliminar($_POST['eliminar']);
}

function eliminar($id) {
    $db = new DB();
    $query = $db->conectar()->prepare("DELETE FROM servicios WHERE id = $id");
    $query->execute();
}

?>
