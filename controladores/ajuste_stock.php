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
    $query = $base_datos->conectar()->prepare("INSERT INTO ajuste_stock
(cod_ajuste, fecha, estado_asjus, cod_usuario)
VALUES(:cod_ajuste_stock,  
:fecha_ajuste,  'ACTIVO', ".$_SESSION['id_user'].");");

    $query->execute($json_datos);
}
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
//aasda
if (isset($_POST['anular'])) {
    anular($_POST['anular']);
}

function anular($id) {
    //crea un arreglo del texto que se le pasa
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE  ajuste_stock SET
estado_asjus = 'ANULADO' 
WHERE cod_ajuste = $id");

    $query->execute();
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
as2.cod_ajuste  as cod_ajuste_stock,
as2.fecha as fecha_ajuste,
as2.estado_asjus as estado,
u.usuario_alias  as nombre_apellido
from ajuste_stock as2 
join usuario u on u.cod_usuario  = as2.cod_usuario
order by cod_ajuste desc");
    
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
    $query = $base_datos->conectar()->prepare("select
cod_ajuste 
from ajuste_stock  
order by cod_ajuste desc limit 1");
    
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
    $query = $base_datos->conectar()->prepare("select
as2.cod_ajuste_stock ,
as2.tipo_ajuste ,
as2.fecha_ajuste ,
as2.estado ,
s.suc_descripcion ,
u.nombre_apellido 
from ajuste_stock as2 
join sucursal s on s.id_sucursal  = as2.id_sucursal 
join usuarios u on u.cod_usuario  = as2.cod_usuario
WHERE as2.cod_ajuste_stock = $id 
order by cod_ajuste_stock desc ");
    
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
    $query = $base_datos->conectar()->prepare("UPDATE `pedido_compra`
     SET `fecha_pedido`=:fecha_pedido,`estado`=:estado,
     `cod_usuario`=:cod_usuario,`id_sucursal`=:id_sucursal
     WHERE `cod_pedido_compra` = :cod_pedido_compra");

    $query->execute($json_datos);
}

if(isset($_POST['eliminar'])){
    eliminar($_POST['eliminar']);
}

function eliminar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM `pedido_compra` where cod_pedido_compra = $id");

    $query->execute();
}


if (isset($_POST["leer_descripcion_materiales"])) {
    
    leer_descripcion_materiales($_POST["leer_descripcion_materiales"]);
   }
   function leer_descripcion_materiales ($nombre_apellido){
       $base = new DB();
       $query = $base ->conectar()->prepare("SELECT 
pc.cod_pedido_compra,
pc.fecha_pedido,
pc.estado,
u.nombre_apellido,
u.cod_usuario,
s.suc_descripcion,
s.id_sucursal
from pedido_compra pc
join usuarios u 
on u.cod_usuario = pc.cod_usuario 
JOIN sucursal s 
on s.id_sucursal = pc.id_sucursal
WHERE CONCAT(cod_pedido_compra,' ', nombre_apellido) LIKE '%$nombre_apellido%'");
       $query ->execute ();
       
      if ($query->rowCount()) {
           print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
       } else {
           echo '0';
       }
   }

   if (isset($_POST['leer_pedidos_activos'])) {
    $stmt = $pdo->prepare("SELECT 
            pc.cod_pedido_compra,
            DATE_FORMAT(pc.fecha_pedido, '%d/%m/%Y') AS fecha_pedido,
            pc.estado,
            u.nombre_apellido,
            u.cod_usuario,
            s.suc_descripcion,
            s.id_sucursal
        FROM 
            pedido_compra pc
        JOIN 
            usuarios u ON u.cod_usuario = pc.cod_usuario
        JOIN 
            sucursal s ON s.id_sucursal = pc.id_sucursal
    ");
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------

if(isset($_POST['pedidos_pendientes'])){
    pedidos_pendientes();
}

function pedidos_pendientes(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
    pc.cod_pedido_compra,
    DATE_FORMAT(pc.fecha_pedido, '%d/%m/%Y') AS fecha_pedido,
    pc.estado,
    u.nombre_apellido,
    u.cod_usuario,
    s.suc_descripcion,
    s.id_sucursal
FROM 
    pedido_compra pc
JOIN 
    usuarios u 
ON 
    u.cod_usuario = pc.cod_usuario 
JOIN 
    sucursal s 
ON 
    s.id_sucursal = pc.id_sucursal
    WHERE pc.estado = 'PENDIENTE'

");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}