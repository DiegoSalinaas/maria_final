<?php

require_once '../conexion/db.php';

// if (isset($_POST['guardar'])) {
//     guardar($_POST['guardar']);
// }

// function guardar($lista) {
//     //crea un arreglo del texto que se le pasa
//     $json_datos = json_decode($lista, true);
//     $base_datos = new DB();
//     $query = $base_datos->conectar()->prepare("INSERT INTO `marcas`( `descripcion`, `estado`)
//      VALUES (:descripcion, :estado)");

//     $query->execute($json_datos);
// }

//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------

if(isset($_POST['leer_deposito_activo'])){
    leer_deposito_activo();
}

function leer_deposito_activo(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
d.cod_deposito,
d.nombre_deposito,
d.direccion_dep,
d.capacidad_dep,
d.stock_actual,
d.estado,
d.cod_ciudad,
c.descripcion_ciud
FROM deposito d
join ciudad c 
on c.cod_ciudad = d.cod_ciudad
WHERE d.estado = 'ACTIVO'");
    
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
d.cod_deposito,
d.nombre_deposito,
d.direccion_dep,
d.capacidad_dep,
d.stock_actual,
d.estado,
d.cod_ciudad,
c.descripcion_ciud
FROM deposito d
join ciudad c 
on c.cod_ciudad = d.cod_ciudad  
    WHERE cod_marca  = $id ");
    
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
// if(isset($_POST['actualizar'])){
//     actualizar($_POST['actualizar']);
// }

// function actualizar($lista){
//     $json_datos = json_decode($lista, true);
//     $base_datos = new DB();
//     $query = $base_datos->conectar()->prepare("UPDATE `marcas`
//      SET `descripcion`=:descripcion,`estado`=:estado
//      WHERE `cod_marca` = :cod_marca");

//     $query->execute($json_datos);
// }

// if(isset($_POST['eliminar'])){
//     eliminar($_POST['eliminar']);
// }

// function eliminar($id){
   
//     $base_datos = new DB();
//     $query = $base_datos->conectar()->prepare("DELETE FROM `marcas` where cod_marca = $id");

//     $query->execute();
// }


   

//    if (isset($_POST['leer_marcas_activos'])) {
//     leer_marcas_activos();
// }

// function leer_marcas_activos() {
// //    $json_datos = json_decode($lista, true);
//     $base_datos = new DB();

//     $query = $base_datos->conectar()->prepare("SELECT `cod_marca`, 
//     `descripcion`, `estado` 
//     FROM `marcas`
// ");

//     $query->execute();

//     if ($query->rowCount()) {
//         print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
//     } else {
//         echo '0';
//     }
// }