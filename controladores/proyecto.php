<?php
require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("INSERT INTO `producto`(`cod_producto`, `nombre`, `descripcion`, `precio`, `cod_marca`, `estado`) VALUES (:cod_producto,:nombre,:descripcion,:precio,:cod_marca,:estado)");
    $query->execute($json_datos);
}

if (isset($_POST['ultimo_registro'])) {
    ultimo_registro();
}

function ultimo_registro() {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT cod_producto FROM producto ORDER BY cod_producto DESC LIMIT 1");
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

if (isset($_POST['leer_marcas'])) {
    leer_marcas();
}

function leer_marcas() {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT cod_marca, descripcion, estado FROM marcas WHERE estado = 'ACTIVO'");
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

if (isset($_POST['leer'])) {
    leer();
}

function leer() {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT p.cod_producto, p.nombre, p.descripcion, p.precio, m.descripcion AS marca, p.estado FROM producto p JOIN marcas m ON m.cod_marca = p.cod_marca");
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
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT cod_producto, nombre, descripcion, precio, cod_marca, estado FROM producto WHERE cod_producto = $id");
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
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE producto SET nombre=:nombre, descripcion=:descripcion, precio=:precio, cod_marca=:cod_marca, estado=:estado WHERE cod_producto=:cod_producto");
    $query->execute($json_datos);
}

if (isset($_POST['eliminar'])) {
    eliminar($_POST['eliminar']);
}

function eliminar($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM producto WHERE cod_producto = $id");
    $query->execute();
}

if (isset($_POST['desactivar'])) {
    desactivar($_POST['desactivar']);
}

function desactivar($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE producto SET estado='DESACTIVADO' WHERE cod_producto = $id");
    $query->execute();
}


if(isset($_POST['leer_descripcion_proyecto'])){
    leer_descripcion_proyecto($_POST['leer_descripcion_proyecto']);
}

function leer_descripcion_proyecto($busqueda) {
    $base = new DB();
    $query = $base->conectar()->prepare("SELECT 
        cod_producto,
        nombre,
        descripcion,
        precio,
        cod_marca,
        estado
    FROM producto
    WHERE CONCAT(cod_producto, ' ', nombre, ' ', descripcion, ' ', estado) LIKE :busqueda
    ORDER BY cod_producto DESC
    LIMIT 50");

    $query->bindValue(':busqueda', "%$busqueda%", PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

