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
    $query = $base_datos->conectar()->prepare("INSERT INTO `presupuesto`"
            . "(`cod_presupuesto`, `fecha_presupuesto`, `fecha_vencimiento`, "
            . "`estado_presupuesto`, `cod_pedido_compra`, `cod_proveedor`, "
            . "`cod_sucursal`, `cod_usuario`) VALUES (:cod_presupuesto,:fecha_presupuesto,"
            . ":fecha_vencimiento,:estado_presupuesto,:cod_pedido_compra,:cod_proveedor,"
            . "".$_SESSION['id_user'].","
            . "".$_SESSION['cod_sucursal'].")");
//    $query = $base_datos->conectar()->prepare("INSERT INTO `presupuesto`
//    (`cod_presupuesto`, `fecha_presupuesto`, `fecha_vencimiento`, 
//    `estado_presupuesto`, `cod_pedido_compra`, `cod_proveedor`, 
//    `cod_sucursal`, `cod_usuario`)
//     VALUES (:cod_presupuesto, :fecha_presupuesto,
//     :fecha_vencimiento, :estado_presupuesto, :cod_pedido_compra,
//     :cod_proveedor, ".$_SESSION['cod_usuario'].", ".$_SESSION['cod_sucursal'].")");

    $query->execute($json_datos);
    
    // $query2 = $base_datos->conectar()->prepare("UPDATE pedido_compra SET estado = 'PRESUPUESTADO'"
    //         . " WHERE cod_pedido_compra = :id_pedido ");

    // $query2->execute([
    //     'id_pedido' => $json_datos['cod_pedido_compra']
    // ]);
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
pc.cod_presupuesto,
pc.fecha_presupuesto,
pc.fecha_vencimiento,
pc.estado_presupuesto,
pv.pro_razonsocial,
pv.cod_proveedor,
u.usuario_alias,
SUM(dp.cantidad * dp.precio_unit) as total
from presupuesto pc
join proveedor pv 
on pv.cod_proveedor = pc.cod_proveedor
join usuario u 
on u.cod_usuario = pc.cod_usuario
join det_presupuesto dp 
on dp.cod_presupuesto  = pc.cod_presupuesto 
group by dp.cod_presupuesto
order by  pc.cod_presupuesto  desc 
");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
if(isset($_POST['cantidad'])){
    cantidad();
}

function cantidad(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT
COUNT(cod_presupuesto) AS cantidad

from presupuesto 
WHERE estado_presupuesto = 'PENDIENTE'
");
    
    $query->execute();

    if ($query->rowCount()) {
        $obj = $query->fetch(PDO::FETCH_OBJ);
        echo $obj->cantidad;
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
cod_presupuesto
from presupuesto
order by cod_presupuesto DESC limit 1");
    
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
pc.cod_presupuesto,
pc.fecha_presupuesto,
pc.fecha_vencimiento,
pc.estado_presupuesto,
pv.pro_razonsocial,
pv.cod_proveedor,
u.usuario_alias,
SUM(dp.cantidad * dp.precio_unit) as total
from presupuesto pc
join proveedor pv 
on pv.cod_proveedor = pc.cod_proveedor
join usuario u 
on u.cod_usuario = pc.cod_usuario
join det_presupuesto dp 
on dp.cod_presupuesto  = pc.cod_presupuesto
 WHERE pc.cod_presupuesto  = $id ");
    
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
    $query = $base_datos->conectar()->prepare("UPDATE `presupuesto_compra`
     SET `fecha_presu`=:fecha_presu,
     `fecha_vencimiento`=:fecha_vencimiento,`estado`=:estado ,`cod_pedido_compra`=:cod_pedido_compra ,
     `cod_proveedor`=:cod_proveedor , `estado`=:estado
     WHERE `cod_presupuesto_comp` = :cod_presupuesto_comp");

    $query->execute($json_datos);
}

if(isset($_POST['eliminar'])){
    eliminar($_POST['eliminar']);
}

function eliminar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM `presupuesto_compra` where cod_presupuesto_comp = $id");

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
   
   if(isset($_POST['presupuesto_pendientes'])){
    presupuesto_pendientes();
}

function presupuesto_pendientes(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT
pc.cod_presupuesto,
pc.fecha_presupuesto,
pc.fecha_vencimiento,
pc.estado_presupuesto,
pv.pro_razonsocial,
pv.cod_proveedor,
u.usuario_alias,
SUM(dp.cantidad * dp.precio_unit) as total
from presupuesto pc
join proveedor pv 
on pv.cod_proveedor = pc.cod_proveedor
join usuario u 
on u.cod_usuario = pc.cod_usuario
join det_presupuesto dp 
on dp.cod_presupuesto  = pc.cod_presupuesto 
WHERE pc.estado_presupuesto = 'APROBADO'
group  by pc.cod_presupuesto
");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}


if (isset($_POST['anular'])) {
    $or_com_cod = $_POST['anular'];
    
    $base_datos = new DB();

    $query = "UPDATE presupuesto SET estado_presupuesto = 'ANULADO' WHERE cod_presupuesto = :pre_cod";
    $stmt = $base_datos->conectar()->prepare($query);
     $stmt->execute([
         'pre_cod' => $or_com_cod
     ]);
    
    $query_select = $base_datos->conectar()->prepare("SELECT f.cod_pedido_compra FROM presupuesto f "
            . "WHERE f.cod_presupuesto = $or_com_cod");

    $query_select->execute();
    $arr = $query_select->fetch(PDO::FETCH_ASSOC);
    echo "PREDIDO DE COMPRA ".$arr['cod_pedido_compra'];
    
    
    $query2 = "UPDATE pedido_compra SET estado_pedido_compra = 'PENDIENTE' "
            . "WHERE cod_pedido_compra = :cod";
    $stmt2 = $base_datos->conectar()->prepare($query2);
    $stmt2->execute([
         'cod' => $arr['cod_pedido_compra']
     ]);

   
}

if (isset($_POST['aprobar'])) {
    aprobar($_POST['aprobar']);
}

function aprobar($id) {
    //crea un arreglo del texto que se le pasa
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE presupuesto SET
estado_presupuesto = 'APROBADO' 
WHERE cod_presupuesto = $id");

    $query->execute();
}