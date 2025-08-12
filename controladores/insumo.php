<?php

require_once '../conexion/db.php';


if(isset($_POST['ultimo_registro'])){
    ultimo_registro();
}

function ultimo_registro(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
cod_insumos
FROM insumos
order by cod_insumos DESC limit 1");
    
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
    $query = $base_datos->conectar()->prepare("INSERT INTO `insumos`(`cod_insumos`, "
            . "`descripcion`, `cod_marca_insumos`, `tipo_iva`, `cantidad`, "
            . "`estado_insumos`, `costo`, `precio`) VALUES (:cod_insumos,"
            . ":descripcion,:cod_marca_insumos,:tipo_iva,:cantidad,:estado_insumos,"
            . ":costo,:precio)");

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
    $query = $base_datos->conectar()->prepare("SELECT i.`cod_insumos`,
        i.`descripcion`, mi.descripcion_marca, i.`tipo_iva`,
        i.`cantidad`, i.`estado_insumos`, i.`costo`, i.`precio` FROM insumos i
JOIN marca_insumos mi
ON i.cod_marca_insumos = mi.cod_marca_insumos");
    
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
    $query = $base_datos->conectar()->prepare("SELECT `cod_insumos`, "
            . "`descripcion`, `cod_marca_insumos`, `tipo_iva`, "
            . "`cantidad`, `estado_insumos`, `costo`, `precio` "
            . "FROM `insumos` WHERE `cod_insumos` = $id ");
    
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
    $query = $base_datos->conectar()->prepare("UPDATE `insumos` SET "
            . "`descripcion`=:descripcion,`cod_marca_insumos`=:cod_marca_insumos,"
            . "`tipo_iva`=:tipo_iva,`cantidad`=:cantidad,"
            . "`estado_insumos`=:estado_insumos,`costo`=:costo,"
            . "`precio`=:precio WHERE `cod_insumos`=:cod_insumos");

    $query->execute($json_datos);
}

if(isset($_POST['eliminar'])){
    eliminar($_POST['eliminar']);
}

function eliminar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM `insumos` where cod_insumos = $id");

    $query->execute();
}

if(isset($_POST['desactivar'])){
    desactivar($_POST['desactivar']);
}

function desactivar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE `insumos` SET estado_insumos = 'DESACTIVADO' where cod_insumos = $id");

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

   

   if (isset($_POST['leer_activos'])) {
    leer_ciudad_activos();
}

function leer_ciudad_activos() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare("SELECT `cod_insumos`, 
        `descripcion`, `cod_marca_insumos`, `tipo_iva`, `cantidad`, 
        `estado_insumos`, `costo`, `precio` FROM `insumos` WHERE estado_insumos = 
        'ACTIVO'
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
   if (isset($_POST['leer_activos_marca'])) {
    leer_activos_marca();
}

function leer_activos_marca() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare("SELECT `cod_marca`, `descripcion`, `estado` FROM `marcas`
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
   if (isset($_POST['leer_activos_impuesto'])) {
    leer_activos_impuesto();
}

function leer_activos_impuesto() {
    $lista = [
        ['tipo_iva' => 0, 'descripcion' => 'EXENTO'],
        ['tipo_iva' => 5, 'descripcion' => 'I.V.A. 5%'],
        ['tipo_iva' => 10, 'descripcion' => 'I.V.A. 10%']
    ];
    print_r(json_encode($lista));
}
