<?php

require_once '../conexion/db.php';
//aasda
if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    //crea un arreglo del texto que se le pasa
    session_start();
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("INSERT INTO detalle_compra
        (cod_compra, cod_insumos, cantidad, costo)
     VALUES (:cod_compra,:cod_producto,:cantidad,:costo)");

    $query->execute($json_datos);
}

//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------

// if(isset($_POST['leer'])){
//     leer();
// }

// function leer(){
//     $base_datos = new DB();
//     $query = $base_datos->conectar()->prepare("SELECT 
// fc.cod_compra,
// fc.fecha_compra,
// fc.condicion,
// fc.timbrado,
// fc.intervalo,
// fc.fecha_venc_timbrado,
// fc.nro_factura,
// p.cod_proveedor,
// p.nom_ape_prov,
// oc.cod_orden_compra,
// d.cod_deposito,
// d.nombre_deposito,
// s.id_sucursal,
// s.suc_descripcion,
// u.cod_usuario,
// u.nombre_apellido
// from compras fc
// JOIN proveedor p 
// on p.cod_proveedor = fc.cod_proveedor
// join orden_compra oc  
// on oc.cod_orden_compra = fc.cod_orden_compra
// join deposito d 
// on d.cod_deposito = fc.cod_deposito 
// join sucursal s
// on s.id_sucursal = fc.id_sucursal 
// join usuarios u 
// on u.cod_usuario = fc.cod_usuario
// order by  fc.cod_compra  desc 
// ");
    
//     $query->execute();

//     if ($query->rowCount()) {
//         print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
//     } else {
//         echo '0';
//     }
// }
// // //-----------------------------------------------------------------------------------------------------
// // //-----------------------------------------------------------------------------------------------------
// // //-----------------------------------------------------------------------------------------------------

// if(isset($_POST['ultimo_registro'])){
//     ultimo_registro();
// }

// function ultimo_registro(){
//     $base_datos = new DB();
//     $query = $base_datos->conectar()->prepare("SELECT
// cod_compra
// from compras
// order by pccod_compra DESC limit 1");
    
//     $query->execute();

//     if ($query->rowCount()) {
//         print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
//     } else {
//         echo '0';
//     }
// }


// // //-----------------------------------------------------------------------------------
// // //-----------------------------------------------------------------------------------
// //-----------------------------------------------------------------------------------
 if(isset($_POST['id'])){
     id($_POST['id']);
 }

 function id($id){
     $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("select
 m.cod_insumos  as cod_producto,
 m.descripcion  as nombre_producto,
 dpc.cantidad ,
 dpc.costo as costo,
 dpc.cantidad  * dpc.costo  as total,
 m.tipo_iva,
 IF(m.tipo_iva = 10, dpc.costo * dpc.cantidad, 0) as iva10,
 IF(m.tipo_iva = 5, dpc.costo * dpc.cantidad, 0) as iva5,
 IF(m.tipo_iva = 0, dpc.costo  * dpc.cantidad, 0) as exenta
 from detalle_compra   dpc 
 join insumos m ON m.cod_insumos  = dpc.cod_insumos  
 where dpc.cod_compra =  $id ");
    
     $query->execute();

     if ($query->rowCount()) {
         print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
     } else {
         echo '0';
     }
 }

// // //----------------------------------------------------------------
// // //----------------------------------------------------------------
// // //----------------------------------------------------------------
// if(isset($_POST['actualizar'])){
//     actualizar($_POST['actualizar']);
// }

// function actualizar($lista){
//     $json_datos = json_decode($lista, true);
//     $base_datos = new DB();
//     $query = $base_datos->conectar()->prepare("UPDATE `compras`
//      SET `fecha_compra`=:fecha_compra,`condicion`=:condicion ,`timbrado`=:timbrado ,
//      `intervalo`=:intervalo , `fecha_venc_timbrado`=:fecha_venc_timbrado, `nro_factura` = :nro_factura
//      , `cod_proveedor` = :cod_proveedor, `cod_orden_compra` = :cod_orden_compra, `cod_deposito` = :cod_deposito
//      , `id_sucursal` = :id_sucursal, `cod_usuario` = :cod_usuario
//      WHERE `cod_compra`=:cod_compra");

//     $query->execute($json_datos);
// }

// if(isset($_POST['eliminar'])){
//     eliminar($_POST['eliminar']);
// }

// function eliminar($id){
   
//     $base_datos = new DB();
//     $query = $base_datos->conectar()->prepare("DELETE FROM `compras` where cod_compra = $id");

//     $query->execute();
// }


// if (isset($_POST["leer_descripcion_materiales"])) {
    
//     leer_descripcion_materiales($_POST["leer_descripcion_materiales"]);
//    }
//    function leer_descripcion_materiales ($nombre_apellido){
//        $base = new DB();
//        $query = $base ->conectar()->prepare("SELECT 
// pc.cod_pedido_compra,
// pc.fecha_pedido,
// pc.estado,
// u.nombre_apellido,
// u.cod_usuario,
// s.suc_descripcion,
// s.id_sucursal
// from pedido_compra pc
// join usuarios u 
// on u.cod_usuario = pc.cod_usuario 
// JOIN sucursal s 
// on s.id_sucursal = pc.id_sucursal
// WHERE CONCAT(cod_pedido_compra,' ', nombre_apellido) LIKE '%$nombre_apellido%'");
//        $query ->execute ();
       
//       if ($query->rowCount()) {
//            print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
//        } else {
//            echo '0';
//        }
//    }