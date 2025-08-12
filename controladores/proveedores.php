<?php

require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    //crea un arreglo del texto que se le pasa
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("INSERT INTO `proveedor`"
            . "(`cod_proveedor`, `pro_razonsocial`, `pro_ruc`, `pro_telef`, "
            . "`estado_proveedor`, `cod_ciudad`) VALUES (:cod_proveedor,:pro_razonsocial,"
            . ":pro_ruc,:pro_telef,:estado_proveedor,:cod_ciudad)");

    $query->execute($json_datos);
}

if(isset($_POST['ultimo_registro'])){
    ultimo_registro();
}

function ultimo_registro(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
cod_proveedor
FROM proveedor
order by cod_proveedor DESC limit 1");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------

if (isset($_POST['leer'])) {
    leer();
}

function leer() {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT p.`cod_proveedor`, 
        p.`pro_razonsocial`, p.`pro_ruc`, p.`pro_telef`, 
        p.`estado_proveedor`, p.`cod_ciudad`, c.nombre_ciud 
        FROM `proveedor` p 
JOIN ciudad c
ON c.cod_ciudad = p.cod_ciudad");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

if (isset($_POST['leer_activo'])) {
    leer_activo();
}

function leer_activo() {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT `cod_proveedor`, "
            . "`pro_razonsocial`, `pro_ruc`, `pro_telef`, `estado_proveedor`, "
            . "`cod_ciudad` FROM `proveedor` WHERE estado_proveedor = 'ACTIVO'");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

//-----------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------
if (isset($_POST['id'])) {
    id($_POST['id']);
}

function id($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT p.`cod_proveedor`, 
        p.`pro_razonsocial`, p.`pro_ruc`, p.`pro_telef`, 
        p.`estado_proveedor`, p.`cod_ciudad`, c.nombre_ciud 
        FROM `proveedor` p 
JOIN ciudad c
ON c.cod_ciudad = p.cod_ciudad 
        WHERE cod_proveedor  = $id ");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

//----------------------------------------------------------------
//----------------------------------------------------------------
//----------------------------------------------------------------
if (isset($_POST['actualizar'])) {
    actualizar($_POST['actualizar']);
}

function actualizar($lista) {
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE `proveedor` SET "
            . "`pro_razonsocial`=:pro_razonsocial,`pro_ruc`=:pro_ruc,"
            . "`pro_telef`=:pro_telef,`estado_proveedor`=:estado_proveedor,"
            . "`cod_ciudad`=:cod_ciudad WHERE `cod_proveedor`=:cod_proveedor");

    $query->execute($json_datos);
}

if (isset($_POST['eliminar'])) {
    eliminar($_POST['eliminar']);
}

function eliminar($id) {

    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM `proveedor` where cod_proveedor = $id");

    $query->execute();
}

if (isset($_POST['desactivar'])) {
    desactivar($_POST['desactivar']);
}

function desactivar($id) {

    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE `proveedor` SET `estado_proveedor`='DESACTIVADO' WHERE `cod_proveedor`= $id");

    $query->execute();
}

if (isset($_POST['leer_descripcion_proveedores'])) {
    leer_descripcion_proveedores($_POST['leer_descripcion_proveedores']);
}

function leer_descripcion_proveedores($busqueda) {
    $base = new DB();
    $query = $base->conectar()->prepare("SELECT 
        cod_proveedor,
        pro_razonsocial,
        pro_ruc,
        pro_telef,
        estado_proveedor,
        cod_ciudad
    FROM proveedor
    WHERE CONCAT(cod_proveedor, ' ', pro_razonsocial, estado_proveedor) LIKE :busqueda
    ORDER BY cod_proveedor DESC
    LIMIT 50");
    
    $query->bindValue(':busqueda', "%$busqueda%", PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
