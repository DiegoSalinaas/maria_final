<?php

require_once '../conexion/db.php';

// ---------------------------------------------------------------------------
// Create
// ---------------------------------------------------------------------------
if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    $json_datos = json_decode($lista, true);
    $db = new DB();
    $query = $db->conectar()->prepare("INSERT INTO servicio_detalles (id_servicio, descripcion, costo, estado, fecha_realizada) VALUES (:id_servicio, :descripcion, :costo, :estado, :fecha_realizada)");
    $query->execute($json_datos);
}

// ---------------------------------------------------------------------------
// Read all
// ---------------------------------------------------------------------------
if (isset($_POST['leer'])) {
    leer();
}

function leer() {
    $db = new DB();
    $query = $db->conectar()->prepare("SELECT * FROM servicio_detalles");
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

// ---------------------------------------------------------------------------
// Read by id
// ---------------------------------------------------------------------------
if (isset($_POST['id'])) {
    id($_POST['id']);
}

function id($id) {
    $db = new DB();
    $query = $db->conectar()->prepare("SELECT * FROM servicio_detalles WHERE id = $id");
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

// ---------------------------------------------------------------------------
// Update
// ---------------------------------------------------------------------------
if (isset($_POST['actualizar'])) {
    actualizar($_POST['actualizar']);
}

function actualizar($lista) {
    $json_datos = json_decode($lista, true);
    $db = new DB();
    $query = $db->conectar()->prepare("UPDATE servicio_detalles SET id_servicio=:id_servicio, descripcion=:descripcion, costo=:costo, estado=:estado, fecha_realizada=:fecha_realizada WHERE id=:id");
    $query->execute($json_datos);
}

// ---------------------------------------------------------------------------
// Delete
// ---------------------------------------------------------------------------
if (isset($_POST['eliminar'])) {
    eliminar($_POST['eliminar']);
}

function eliminar($id) {
    $db = new DB();
    $query = $db->conectar()->prepare("DELETE FROM servicio_detalles WHERE id = $id");
    $query->execute();
}

?>
