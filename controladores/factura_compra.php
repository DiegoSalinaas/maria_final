<?php

require_once '../conexion/db.php';

function ok($d){ echo json_encode(['ok'=>true,'data'=>$d], JSON_UNESCAPED_UNICODE); exit; }
function fail($m,$extra=null){ http_response_code(400); echo json_encode(['ok'=>false,'msg'=>$m,'extra'=>$extra]); exit; }

try { $cn = (new DB())->conectar(); }
catch (Throwable $e) { fail('Error de conexión BD', $e->getMessage()); }

/** === SERIE ACTIVA PARA FACTURA COMPRA ===
 * Si tu tabla tiene una columna "tipo", filtrá con AND tipo='COMPRA'
 */
if (isset($_REQUEST['serie_activa'])) {
  $hoy = date('Y-m-d');
  $q = $cn->prepare("
    SELECT id_serie, timbrado, vig_desde, vig_hasta,
           establecimiento, punto_expedicion,
           numero_actual, numero_fin
    FROM factura_serie
    WHERE activo=1
      AND :hoy BETWEEN vig_desde AND vig_hasta
    ORDER BY id_serie DESC
    LIMIT 1
  ");
  $q->execute([':hoy'=>$hoy]);
  $row = $q->fetch(PDO::FETCH_ASSOC);
  if(!$row){ fail('No hay serie activa vigente (Compra)'); }
  ok($row);
}

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    //crea un arreglo del texto que se le pasa
    session_start();
    $json_datos = json_decode($lista, true);
    
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("INSERT INTO compra
    (cod_registro, fecha_compra, cod_orden, 
    cod_proveedor, cod_usuario, nro_factura, timbrado, estado_registro, 
    fecha_vencimiento_timbrado, condicion)
     VALUES (:cod_compra, :fecha_compra, :cod_orden_compra, :cod_proveedor,
     ".$_SESSION['id_user'].", :nro_factura, :timbrado, 'ACTIVO',
         :fecha_venc_timbrado, :condicion); ");

    $query->execute($json_datos);
    
    
     $query2 = $base_datos->conectar()->prepare("UPDATE orden_compra set 
        oc_estado = 'UTILIZADO' 
        WHERE cod_orden = :cod_presupuesto_comp");

    $query2->execute([
        'cod_presupuesto_comp' => $json_datos['cod_orden_compra']
    ]);
    
    
    
}
if (isset($_POST['anular'])) {
    anular($_POST['anular']);
}

function anular($id) {
    //crea un arreglo del texto que se le pasa
    session_start();
    
    
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE  compra SET 
    estado_registro = 'ANULADO'
     WHERE cod_registro = $id");

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

    $where = "";
    $params = [];

    if (!empty($_POST['buscar'])) {
        $where .= " AND (p.pro_razonsocial LIKE :buscar OR fc.nro_factura LIKE :buscar OR fc.fecha_compra LIKE :buscar)";
        $params[':buscar'] = "%" . $_POST['buscar'] . "%";
    }
    if (!empty($_POST['estado'])) {
        $where .= " AND fc.estado_registro = :estado";
        $params[':estado'] = $_POST['estado'];
    }
    if (!empty($_POST['desde'])) {
        $where .= " AND fc.fecha_compra >= :desde";
        $params[':desde'] = $_POST['desde'];
    }
    if (!empty($_POST['hasta'])) {
        $where .= " AND fc.fecha_compra <= :hasta";
        $params[':hasta'] = $_POST['hasta'];
    }

    $sql = "SELECT
fc.cod_registro as cod_compra,
fc.fecha_compra,
fc.condicion,
fc.timbrado,
fc.fecha_vencimiento_timbrado as fecha_venc_timbrado,
fc.nro_factura,
p.cod_proveedor,
p.pro_razonsocial as razon_social_prov,
fc.estado_registro  as estado,
u.cod_usuario,
u.usuario_alias  as nombre_apellido,
sum(dc.cantidad * dc.costo) as total
from compra fc
join detalle_compra dc on dc.cod_compra  = fc.cod_registro
JOIN proveedor p on p.cod_proveedor = fc.cod_proveedor
join usuario  u on u.cod_usuario = fc.cod_usuario
WHERE 1=1 {$where}
group by fc.cod_registro
order by  fc.cod_registro  desc";

    $query = $base_datos->conectar()->prepare($sql);
    $query->execute($params);

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------

if(isset($_POST['leer_activo'])){
    leer_activo();
}

function leer_activo(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
fc.cod_registro as cod_compra,
fc.fecha_compra,
fc.condicion,
fc.timbrado,
fc.fecha_vencimiento_timbrado as fecha_venc_timbrado,
fc.nro_factura,
p.cod_proveedor,
p.pro_razonsocial as razon_social_prov,
fc.estado_registro  as estado,
u.cod_usuario,
u.usuario_alias  as nombre_apellido,
sum(dc.cantidad * dc.costo) as total
from compra fc
join detalle_compra dc on dc.cod_compra  = fc.cod_registro 
JOIN proveedor p 
on p.cod_proveedor = fc.cod_proveedor 
join usuario  u 
on u.cod_usuario = fc.cod_usuario 
WHERE fc.estado_registro = 'ACTIVO'
group by fc.cod_registro
order by  fc.cod_registro  desc 
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

if(isset($_POST['cantidad_compras'])){
    cantidad_compras();
}

function cantidad_compras(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
count(fc.cod_registro) as cantidad
from compra fc
join detalle_compra dc on dc.cod_compra  = fc.cod_registro 
JOIN proveedor p 
on p.cod_proveedor = fc.cod_proveedor 
join usuario  u 
on u.cod_usuario = fc.cod_usuario 
WHERE fc.estado_registro = 'ACTIVO'
order by  fc.cod_registro  desc 
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

if(isset($_POST['leer_activo_remision'])){
    leer_activo_remision();
}

function leer_activo_remision(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
fc.cod_registro as cod_compra,
fc.fecha_compra,
fc.condicion,
fc.timbrado,
fc.fecha_vencimiento_timbrado as fecha_venc_timbrado,
fc.nro_factura,
p.cod_proveedor,
p.pro_razonsocial as razon_social_prov,
fc.estado_registro  as estado,
u.cod_usuario,
u.usuario_alias  as nombre_apellido,
sum(dc.cantidad * dc.costo) as total
from compra fc
join detalle_compra dc on dc.cod_compra  = fc.cod_registro 
JOIN proveedor p 
on p.cod_proveedor = fc.cod_proveedor 
join usuario  u 
on u.cod_usuario = fc.cod_usuario 
WHERE fc.estado_registro = 'ACTIVO' 
group by fc.cod_registro
order by  fc.cod_registro  desc 
");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
// //-----------------------------------------------------------------------------------------------------
// //-----------------------------------------------------------------------------------------------------
// //-----------------------------------------------------------------------------------------------------

if(isset($_POST['ultimo_registro'])){
    ultimo_registro();
}

function ultimo_registro(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT
cod_registro
from compra
order by cod_registro DESC limit 1");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}


// //-----------------------------------------------------------------------------------
// //-----------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------
if(isset($_POST['id'])){
    id($_POST['id']);
}

function id($id){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
fc.cod_registro as cod_compra,
fc.fecha_compra,
fc.condicion,
fc.timbrado,
fc.fecha_vencimiento_timbrado as fecha_venc_timbrado,
fc.nro_factura,
p.cod_proveedor,
p.pro_razonsocial as razon_social_prov,
fc.estado_registro  as estado,
u.cod_usuario,
u.usuario_alias  as nombre_apellido,
sum(dc.cantidad * dc.costo) as total
from compra fc
join detalle_compra dc on dc.cod_compra  = fc.cod_registro 
JOIN proveedor p 
on p.cod_proveedor = fc.cod_proveedor 
join usuario  u 
on u.cod_usuario = fc.cod_usuario 
WHERE fc.cod_registro =   $id ");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

// //----------------------------------------------------------------
// //----------------------------------------------------------------
// //----------------------------------------------------------------
if(isset($_POST['actualizar'])){
    actualizar($_POST['actualizar']);
}

function actualizar($lista){
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE `compras`
     SET `fecha_compra`=:fecha_compra,`condicion`=:condicion ,`timbrado`=:timbrado ,
     `intervalo`=:intervalo , `fecha_venc_timbrado`=:fecha_venc_timbrado, `nro_factura` = :nro_factura
     , `cod_proveedor` = :cod_proveedor, `cod_orden_compra` = :cod_orden_compra, `cod_deposito` = :cod_deposito
     , `id_sucursal` = :id_sucursal, `cod_usuario` = :cod_usuario
     WHERE `cod_compra`=:cod_compra");

    $query->execute($json_datos);
}

if(isset($_POST['eliminar'])){
    eliminar($_POST['eliminar']);
}

function eliminar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM `compras` where cod_compra = $id");

    $query->execute();
}


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