<?php

require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    //crea un arreglo del texto que se le pasa
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("INSERT INTO `materiales`(
     `nombre_material`, `descripcion_material`,
      `costo_material`, `precio_material`, `fecha_ingreso`,
       `estado`, `cod_tipo_material`, `cod_marca`) 
    VALUES (:nombre_material , :descripcion_material , :costo_material,  :precio_material , :fecha_ingreso,
    :estado, :cod_tipo_material, :cod_marca)");

    $query->execute($json_datos);
}

//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------

if(isset($_POST['leer'])){
    leer();
}

function leer(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT
    m.cod_material,
    m.nombre_material,
    m.descripcion_material,
    m.costo_material,
    m.precio_material,
    m.fecha_ingreso,
    m.estado,
    m.cod_marca,
    tm.cod_tipo_material,
    tm.descripcion AS descripcion_tipo_material,
    mr.descripcion AS descripcion_marca
FROM materiales m
JOIN tipo_material tm ON tm.cod_tipo_material = m.cod_tipo_material
JOIN marcas mr ON mr.cod_marca = m.cod_marca");
    
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
if(isset($_POST['id'])){
    id($_POST['id']);
}

function id($id){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT
    m.cod_material,
    m.nombre_material,
    m.descripcion_material,
    m.costo_material,
    m.precio_material,
     m.cod_marca,
    tm.cod_tipo_material,
    m.fecha_ingreso,
    m.estado,
    tm.descripcion AS descripcion_tipo_material,
    mr.descripcion AS descripcion_marca
FROM materiales m
JOIN tipo_material tm ON tm.cod_tipo_material = m.cod_tipo_material
JOIN marcas mr ON mr.cod_marca = m.cod_marca 
 WHERE cod_material  = $id ");
    
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
if(isset($_POST['actualizar'])){
    actualizar($_POST['actualizar']);
}

function actualizar($lista){
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE `materiales`
     SET `nombre_material`=:nombre_material,`descripcion_material`=:descripcion_material,`costo_material`=:costo_material,
     `precio_material`=:precio_material,`fecha_ingreso`=:fecha_ingreso,`estado`=:estado,`cod_tipo_material`=:cod_tipo_material, 
     cod_marca = :cod_marca
     WHERE `cod_material` = :cod_material");

    $query->execute($json_datos);
}

if(isset($_POST['eliminar'])){
    eliminar($_POST['eliminar']);
}

function eliminar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM `insumo` where id_insumo = $id");

    $query->execute();
}


if (isset($_POST["leer_descripcion_materiales"])) {
    
    leer_descripcion_materiales($_POST["leer_descripcion_materiales"]);
   }
   function leer_descripcion_materiales ($nombre_material){
       $base = new DB();
       $query = $base ->conectar()->prepare("SELECT
    m.cod_material,
    m.nombre_material,
    m.descripcion_material,
    m.costo_material,
    m.precio_material,
     m.cod_marca,
    tm.cod_tipo_material,
    m.fecha_ingreso,
    m.estado,
    tm.descripcion AS descripcion_tipo_material,
    mr.descripcion AS descripcion_marca
FROM materiales m
JOIN tipo_material tm ON tm.cod_tipo_material = m.cod_tipo_material
JOIN marcas mr ON mr.cod_marca = m.cod_marca 
WHERE CONCAT(cod_material,' ', nombre_material) LIKE '%$nombre_material%'");
       $query ->execute ();
       
      if ($query->rowCount()) {
           print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
       } else {
           echo '0';
       }
   }

   if (isset($_POST['leer_materiales_activos'])) {
    leer_materiales_activos();
}

function leer_materiales_activos() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare("SELECT cod_material, nombre_material, descripcion_material, costo_material, precio_material, fecha_ingreso, estado, cod_tipo_material, cod_marca
FROM materiales
");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}



   if (isset($_POST['leer_material_activos'])) {
    leer_materiales_activos();
}

function leer_material_activos() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare("SELECT
m.cod_material,
m.nombre_material,
m.descripcion_material,
m.costo_material,
m.precio_material,
m.fecha_ingreso,
m.estado,
m.cod_marca,
tm.descripcion
FROM materiales m 
JOIN tipo_material tm 
on tm.cod_tipo_material = m.cod_tipo_material
");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}