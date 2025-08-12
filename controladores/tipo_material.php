<?php

require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    //crea un arreglo del texto que se le pasa
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("INSERT INTO `proveedor`
    ( `nom_ape_prov`,
     `razon_social_prov`, `telefono_prov`, 
     `ruc_prov`, `direccion_prov`, `email_prov`, `estado`, 
     `cod_ciudad`) VALUES (:nom_ape_prov, :telefono_prov, :ruc_prov, :direccion_prov, :email_prov , :estado, :cod_ciudad)");

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
    $query = $base_datos->conectar()->prepare("SELECT `cod_proveedor`,
     `nom_ape_prov`, `razon_social_prov`,
     `telefono_prov`, `ruc_prov`, `direccion_prov`,
      `email_prov`, `estado`,
      `cod_ciudad` 
      FROM `proveedor`");
    
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
    $query = $base_datos->conectar()->prepare("SELECT `id_insumo`, `descripcion`,
     `costo_compra`, `precio_venta`, `stock`, `stock_minimo`, `marca`, `estado` 
    FROM `insumo`  WHERE id_insumo  = $id ");
    
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
    $query = $base_datos->conectar()->prepare("UPDATE `insumo`
     SET `descripcion`=:descripcion,`costo_compra`=:costo_compra,`precio_venta`=:precio_venta,
     `stock`=:stock,`stock_minimo`=:stock_minimo,`marca`=:marca,`estado`=:estado
     WHERE `id_insumo` = :id_insumo");

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

   

   if (isset($_POST['leer_material_activos'])) {
    leer_material_activos();
}

function leer_material_activos() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare("SELECT `cod_tipo_material`, `descripcion`,
     `uso`, `nivel_resistencia`, `fecha_registro`,
      `estado`
       FROM `tipo_material` 
");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}