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
    $query = $base_datos->conectar()->prepare("INSERT INTO pedido_compra
(`cod_pedido_compra`, `fecha_pedido`, `estado_pedido_compra`, `cod_usuario`, 
`cod_sucursal`, `cod_proveedor`)
VALUES(:cod_pedido_compra, :fecha_pedido, :estado_pedido_compra, 
".$_SESSION['id_user'].", 1, :cod_proveedor)");

    $query->execute($json_datos);
}
//aasda
if (isset($_POST['anular'])) {
    anular($_POST['anular']);
}

function anular($id) {
    //crea un arreglo del texto que se le pasa
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE pedido_compra SET
estado_pedido_compra = 'ANULADO' 
WHERE cod_pedido_compra = $id");

    $query->execute();
}

//aasda
if (isset($_POST['utilizado'])) {
    utilizado($_POST['utilizado']);
}

function utilizado($id) {
    //crea un arreglo del texto que se le pasa
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE pedido_compra SET
estado_pedido_compra = 'UTILIZADO' 
WHERE cod_pedido_compra = $id");

    $query->execute();
}

//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------

if(isset($_POST['cantidad'])){
    cantidad();
}

function cantidad(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
    COUNT(pc.cod_pedido_compra) AS cantidad
FROM 
    pedido_compra pc 

    where  estado_pedido_compra = 'PENDIENTE'

");
    
    $query->execute();

    if ($query->rowCount()) {
       $obj = $query->fetch(PDO::FETCH_OBJ);
        echo $obj->cantidad;
    } else {
        echo '0';
    }
}


if(isset($_POST['leer'])){
    leer();
}

function leer(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
    pc.cod_pedido_compra,
    DATE_FORMAT(pc.fecha_pedido, '%d/%m/%Y') AS fecha_pedido,
    pc.estado_pedido_compra,
    u.usuario_alias,
    u.cod_usuario,
    s.nombre_sucur,
    s.cod_sucursal
FROM 
    pedido_compra pc
JOIN 
    usuario u 
ON 
    u.cod_usuario = pc.cod_usuario 
JOIN 
    sucursal s 
ON 
    s.cod_sucursal = pc.cod_sucursal

");
    
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
    $query = $base_datos->conectar()->prepare("SELECT cod_pedido_compra
from pedido_compra  
order by cod_pedido_compra DESC limit 1");
    
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
    $query = $base_datos->conectar()->prepare("SELECT 
pc.cod_pedido_compra,
pc.fecha_pedido,
pc.estado_pedido_compra,
pc.cod_proveedor,
u.usuario_alias,
u.cod_usuario,
s.nombre_sucur,
s.cod_sucursal
from pedido_compra pc
join usuario u 
on u.cod_usuario = pc.cod_usuario 
JOIN sucursal s 
on s.cod_sucursal = pc.cod_sucursal
 WHERE cod_pedido_compra  = $id ");
    
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
     SET `fecha_pedido`=:fecha_pedido,`estado_pedido_compra`=:estado_pedido_compra,
     `cod_sucursal`= :cod_sucursal, cod_proveedor = :cod_proveedor
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
    pc.estado_pedido_compra,
    u.usuario_alias,
    u.cod_usuario,
    s.nombre_sucur,
    s.cod_sucursal
FROM 
    pedido_compra pc
JOIN 
    usuario u 
ON 
    u.cod_usuario = pc.cod_usuario 
JOIN 
    sucursal s 
ON 
    s.cod_sucursal = pc.cod_sucursal
    WHERE pc.estado_pedido_compra = 'PENDIENTE'

");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}