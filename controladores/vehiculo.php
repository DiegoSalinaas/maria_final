<?php

require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    // Convierte la lista JSON a un array asociativo
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    
    // Prepara la consulta SQL
    $query = $base_datos->conectar()->prepare("INSERT INTO `vehiculos`(
        `descripcion`, `modelo`, `cod_marca`, `color`, `placa`, `tipo_vehiculo`, `fecha_ingreso`, `fecha_salida`, `estado`
    ) VALUES (
        :descripcion, :modelo, :cod_marca, :color, :placa, :tipo_vehiculo, :fecha_ingreso, :fecha_salida, :estado
    );"); // Corrige el cierre de la instrucción
    
    // Ejecuta la consulta con los datos decodificados
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
v.cod_vehiculo,
v.descripcion,
v.modelo,
v.color,
v.placa,
v.tipo_vehiculo,
v.fecha_ingreso,
v.fecha_salida,
v.estado,
m.descripcion as descripcion_marcas
From vehiculos v
JOIN marcas m
on m.cod_marca = v.cod_marca");
    
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
    $query = $base_datos->conectar()->prepare("SELECT `cod_vehiculo`, `descripcion`, `modelo`, `cod_marca`, `color`,
     `placa`, `tipo_vehiculo`, `fecha_ingreso`, `fecha_salida`,
      `estado` 
      FROM `vehiculos`  
      WHERE cod_vehiculo  = $id ");
    
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
    $query = $base_datos->conectar()->prepare("UPDATE `vehiculos`
SET 
    `descripcion` = :descripcion,
    `modelo` = :modelo,
    `cod_marca` = :cod_marca,
    `color` = :color,
    `placa` = :placa,
    `tipo_vehiculo` = :tipo_vehiculo,
    `fecha_ingreso` = :fecha_ingreso,
    `fecha_salida` = :fecha_salida,
    `estado` = :estado
WHERE `cod_vehiculo` = :cod_vehiculo;
");

    $query->execute($json_datos);
}

if(isset($_POST['eliminar'])){
    eliminar($_POST['eliminar']);
}

function eliminar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM `vehiculos` where cod_vehiculo = $id");

    $query->execute();
}

if (isset($_POST["leer_descripcion_vehiculo"])) {
    
    leer_descripcion_vehiculo($_POST["leer_descripcion_vehiculo"]);
   }
   function leer_descripcion_vehiculo ($descripcion){
       $base = new DB();
       $query = $base ->conectar()->prepare("SELECT
v.cod_vehiculo,
v.descripcion,
v.modelo,
v.color,
v.placa,
v.tipo_vehiculo,
v.fecha_ingreso,
v.fecha_salida,
v.estado,
m.descripcion as descripcion_marcas
From vehiculos v
JOIN marcas m
on m.cod_marca = v.cod_marca
WHERE CONCAT(cod_vehiculo, v.descripcion, modelo) LIKE '%$descripcion%'
ORDER BY cod_vehiculo DESC
LIMIT 50");
       $query ->execute ();
       
      if ($query->rowCount()) {
           print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
       } else {
           echo '0';
       }
   }

   

   if (isset($_POST['leer_ciudad_activos'])) {
    leer_ciudad_activos();
}

function leer_ciudad_activos() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare(" SELECT `cod_ciudad`, 
    `descripcion_ciud`, `estado` 
    FROM `ciudad`
");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}