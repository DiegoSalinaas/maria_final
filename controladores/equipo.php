<?php

require_once '../conexion/db.php';


if(isset($_POST['ultimo_registro'])){
    ultimo_registro();
}

function ultimo_registro(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
cod_equipo
FROM equipo
order by cod_equipo DESC limit 1");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}


if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    //crea un arreglo del texto que se le pasa
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("INSERT INTO `equipo`(`cod_equipo`, "
            . "`estado`, `id_tipo_equipo`, `id_color`, `id_modelo`) "
            . "VALUES (:cod_equipo,:estado,:id_tipo_equipo, :id_color,"
            . ":id_modelo)");

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
    $query = $base_datos->conectar()->prepare("SELECT e.`cod_equipo`, e.`estado`, 
te.nombre_tipo, c.descripcion, m.descripcion as nom_modelo 
FROM `equipo` e
JOIN tipo_equipo te
ON e.id_tipo_equipo = te.cod_tipo
JOIN color c
ON c.cod_color = e.id_color
JOIN modelo m
ON m.id_modelo = e.id_modelo");
    
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
    $query = $base_datos->conectar()->prepare("SELECT `cod_equipo`, `estado`, "
            . "`id_tipo_equipo`, `id_color`, `id_modelo` FROM `equipo` "
            . "WHERE `cod_equipo` = $id ");
    
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
    $query = $base_datos->conectar()->prepare("UPDATE `equipo` SET "
            . "`estado`=:estado,`id_tipo_equipo`=:id_tipo_equipo, "
            . "`id_color`=:id_color,`id_modelo`=:id_modelo WHERE "
            . "`cod_equipo`=:cod_equipo");

    $query->execute($json_datos);
}

if(isset($_POST['eliminar'])){
    eliminar($_POST['eliminar']);
}

function eliminar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM `equipo` where cod_equipo = $id");

    $query->execute();
}

if(isset($_POST['desactivar'])){
    desactivar($_POST['desactivar']);
}

function desactivar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE `equipo` SET estado = 'DESACTIVADO' where cod_equipo = $id");

    $query->execute();
}

if (isset($_POST["leer_descripcion"])) {
    
    leer_descripcion($_POST["leer_descripcion"]);
   }
   function leer_descripcion ($descripcion){
       $base = new DB();
       $query = $base ->conectar()->prepare("SELECT id_insumo, descripcion, costo_compra, precio_venta, stock, stock_minimo, marca, estado
FROM insumo
WHERE CONCAT(id_insumo, descripcion, costo_compra, precio_venta, stock, stock_minimo, marca, estado) LIKE '%$descripcion%'
ORDER BY id_insumo DESC
LIMIT 50");
       $query ->execute ();
       
      if ($query->rowCount()) {
           print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
       } else {
           echo '0';
       }
   }

   

   if (isset($_POST['leer_activos_tipo'])) {
    leer_activos_tipo();
}

function leer_activos_tipo() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare("SELECT `cod_tipo`, `nombre_tipo`, 
        `estado` FROM `tipo_equipo` WHERE estado = 'ACTIVO'
");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
   if (isset($_POST['leer_activos_equipo'])) {
    leer_activos_equipo();
}

function leer_activos_equipo() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare("SELECT `cod_color`, `descripcion` FROM `color`
");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
   if (isset($_POST['leer_activos_modelo'])) {
    leer_activos_modelo();
}

function leer_activos_modelo() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare("SELECT `id_modelo`, `descripcion`, `estado` FROM `modelo` WHERE estado = 'ACTIVO'");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}