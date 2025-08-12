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
    $query = $base_datos->conectar()->prepare("INSERT INTO nota_compra
(cod_nota_compra, estado_cre, tipo, fecha_cred, cod_registro, cod_usuario, 
timbrado, fecha_vencimiento, motivo, nro_factura)
VALUES(:cod_nota_compra, :estado, :tipo_nota, :fecha_nota, :cod_compra, ".$_SESSION['id_user'].", 
    :timbrado,:fecha_venc_timbrado, :motivo, :nro_factura);");

    $query->execute($json_datos);
    
    
//    $query2 = $base_datos->conectar()->prepare("UPDATE compras set 
//        estado = 'ACTIVO' 
//        WHERE cod_compra = :cod");
//
//    $query2->execute([
//        'cod' => $json_datos['cod_compra']
//    ]);
}

//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------

if(isset($_POST['leer'])){
    leer();
}

function leer(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("select 
 nc.cod_nota_compra as cod_nota_compra,
 nc.fecha_cred  as fecha_nota,
 nc.timbrado ,
 nc.nro_factura ,
 nc.estado_cre as estado ,
 nc.tipo as tipo_nota,
 nc.motivo ,
 SUM(dnc.costo * dnc.cantidad) as total,
 p.pro_razonsocial  as razon_social_prov
 from nota_compra  nc  
 join deta_nota dnc on dnc.cod_nota  =  nc.cod_nota_compra 
 join compra  c on c.cod_registro  =  nc.cod_registro 
 join proveedor  p on p.cod_proveedor  =  c.cod_proveedor 
 group by nc.cod_nota_compra
 order  by  nc.cod_nota_compra  desc  ");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------

if(isset($_POST['ultimo_registro'])){
    ultimo_registro();
}

function ultimo_registro(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
nc.cod_nota_compra
FROM nota_compra nc
order by nc.cod_nota_compra DESC limit 1");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
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
    $query = $base_datos->conectar()->prepare(" SELECT
nc.cod_nota_compra,
nc.fecha_nota,
nc.timbrado,
nc.fecha_venc_timbrado,
nc.nro_nota,
nc.tipo_nota,
nc.motivo,
nc.estado,
s.suc_descripcion,
u.nombre_apellido,
c.cod_compra

FROM nota_compra nc
 join sucursal s on s.id_sucursal  =  nc.id_sucursal 
 
join usuarios u 
on u.cod_usuario = nc.cod_usuario
join compras c 
on c.cod_compra = nc.cod_compra
 WHERE nc.cod_nota_compra  = $id ");
    
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
    $query = $base_datos->conectar()->prepare("UPDATE `nota_compra`
     SET `cod_nota_compra`=:cod_nota_compra,`fecha_nota`=:fecha_nota ,`timbrado`=:timbrado ,
     `fecha_venc_timbrado`=:fecha_venc_timbrado , `nro_nota`=:nro_nota, `tipo_nota` = :tipo_nota, 
     `motivo` = :motivo,`estado` = :estado,`cod_compra` = :cod_compra,`cod_usuario` = :cod_usuario,`id_sucursal` = :id_sucursal,
     WHERE `cod_orden_compra`=:cod_orden_compra,");

    $query->execute($json_datos);
}

if(isset($_POST['eliminar'])){
    eliminar($_POST['eliminar']);
}

function eliminar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM `nota_compra` where cod_nota_compra = $id");

    $query->execute();
}

if(isset($_POST['anular'])){
    anular($_POST['anular']);
}

function anular($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE `nota_compra` set estado_cre = 'ANULADO' where cod_nota_compra = $id");

    $query->execute();
}

