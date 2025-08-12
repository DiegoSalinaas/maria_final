<?php

require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

if (isset($_POST['ultimo_registro'])) {
    ultimo_registro();
}

if (isset($_POST['leer'])) {
    leer();
}

if (isset($_POST['id'])) {
    id($_POST['id']);
}

if (isset($_POST['actualizar'])) {
    actualizar($_POST['actualizar']);
}

if (isset($_POST['eliminar'])) {
    eliminar($_POST['eliminar']);
}

function guardar($lista) {
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("INSERT INTO ciudad (cod_ciudad, nombre_ciud, estado_ciud) VALUES (:cod_ciudad, :nombre_ciud, :estado_ciud)");
    $query->execute($json_datos);
}

function ultimo_registro() {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT cod_ciudad FROM ciudad ORDER BY cod_ciudad DESC LIMIT 1");
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

function leer() {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT cod_ciudad, nombre_ciud, estado_ciud FROM ciudad");
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

function id($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT cod_ciudad, nombre_ciud, estado_ciud FROM ciudad WHERE cod_ciudad = $id");
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

function actualizar($lista) {
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE ciudad SET nombre_ciud=:nombre_ciud, estado_ciud=:estado_ciud WHERE cod_ciudad=:cod_ciudad");
    $query->execute($json_datos);
}

function eliminar($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM ciudad WHERE cod_ciudad = $id");
    $query->execute();
}
?>
