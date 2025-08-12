<?php
require_once '../conexion/db.php';
header('Content-Type: application/json; charset=utf-8');
session_start();

if (isset($_POST['guardar']))          { guardar($_POST['guardar']); exit; }
if (isset($_POST['leer']))             { leer(); exit; }
if (isset($_POST['leer_buscar']))      { leer_buscar($_POST['leer_buscar']); exit; }
if (isset($_POST['ultimo_registro']))  { ultimo_registro(); exit; }
if (isset($_POST['anular']))           { anular((int)$_POST['anular']); exit; }
if (isset($_POST['pendientes']))       { Orden_pendientes(); exit; }
if (isset($_POST['id']))               { traer_cabecera((int)$_POST['id']); exit; }
if (isset($_POST['guardar_detalles'])) { guardar_detalles($_POST['guardar_detalles']); exit; }

/* ===== GUARDAR CABECERA (sin cod_orden) ===== */
function guardar($raw){
  try{
    $obj = json_decode($raw, true);
    if (!$obj) throw new Exception('JSON inválido');

    $fecha = $obj['fecha_orden'] ?? null;
    $estado = $obj['estado'] ?? 'PENDIENTE';
    $cod_proveedor = (int)($obj['cod_proveedor'] ?? 0);
    $cod_presupuesto = (int)($obj['cod_presupuesto_comp'] ?? 0);
    $cod_usuario = (int)($_SESSION['id_user'] ?? 0);

    if(!$fecha) throw new Exception('Fecha requerida');
    if($cod_proveedor<=0) throw new Exception('Proveedor requerido');
    if($cod_usuario<=0) throw new Exception('Sesión sin id_user');

    $db = new DB();
    $cn = $db->conectar();
    $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $st = $cn->prepare("
      INSERT INTO orden_compra (oc_fecha_emision, oc_estado, cod_presupuesto, cod_proveedor, cod_usuario)
      VALUES (:fecha, :estado, :cod_presu, :cod_prov, :cod_user)
    ");
    $st->execute([
      ':fecha'    => $fecha,
      ':estado'   => $estado,
      ':cod_presu'=> $cod_presupuesto,
      ':cod_prov' => $cod_proveedor,
      ':cod_user' => $cod_usuario
    ]);

    $id = (int)$cn->lastInsertId();

    if ($cod_presupuesto > 0){
      $up = $cn->prepare("UPDATE presupuesto SET estado_presupuesto='UTILIZADO' WHERE cod_presupuesto=:id");
      $up->execute([':id'=>$cod_presupuesto]);
    }

    echo json_encode(['ok'=>true,'id'=>$id], JSON_UNESCAPED_UNICODE);
  }catch(Throwable $e){
    http_response_code(400);
    echo json_encode(['ok'=>false,'msg'=>'No se pudo guardar cabecera','extra'=>$e->getMessage()]);
  }
}

/* ===== LISTAR (resumen con total) ===== */
function leer(){
  $db = new DB(); $cn = $db->conectar();
  $q = $cn->prepare("
    SELECT 
      oc.cod_orden        AS cod_orden_compra,
      oc.oc_fecha_emision AS fecha_orden,
      oc.oc_estado        AS estado,
      pvc.pro_razonsocial AS nom_ape_prov,
      u.usuario_alias     AS nombre_apellido,
      COALESCE(SUM(do.cantidad * do.prec_uni),0) AS total
    FROM orden_compra oc
    JOIN proveedor pvc ON pvc.cod_proveedor = oc.cod_proveedor
    JOIN usuario   u   ON u.cod_usuario    = oc.cod_usuario
    LEFT JOIN det_orden do ON do.cod_orden = oc.cod_orden
    GROUP BY oc.cod_orden
    ORDER BY oc.cod_orden DESC
  ");
  $q->execute();
  if ($q->rowCount()){
    echo json_encode($q->fetchAll(PDO::FETCH_OBJ), JSON_UNESCAPED_UNICODE);
  } else {
    echo '0';
  }
}

/* ===== Búsqueda ===== */
function leer_buscar($busqueda){
  $db = new DB(); $cn = $db->conectar();
  $q = $cn->prepare("
    SELECT 
      oc.cod_orden        AS cod_orden_compra,
      oc.oc_fecha_emision AS fecha_orden,
      oc.oc_estado        AS estado,
      pvc.pro_razonsocial AS nom_ape_prov,
      u.usuario_alias     AS nombre_apellido,
      COALESCE(SUM(do.cantidad * do.prec_uni),0) AS total
    FROM orden_compra oc
    JOIN proveedor pvc ON pvc.cod_proveedor = oc.cod_proveedor
    JOIN usuario   u   ON u.cod_usuario    = oc.cod_usuario
    LEFT JOIN det_orden do ON do.cod_orden = oc.cod_orden
    WHERE CONCAT(
      oc.cod_orden,' ',oc.oc_fecha_emision,' ',oc.oc_estado,' ',
      pvc.pro_razonsocial,' ',u.usuario_alias
    ) LIKE :b
    GROUP BY oc.cod_orden
    ORDER BY oc.cod_orden DESC
    LIMIT 50
  ");
  $q->execute([':b'=>'%'.$busqueda.'%']);
  if ($q->rowCount()){
    echo json_encode($q->fetchAll(PDO::FETCH_OBJ), JSON_UNESCAPED_UNICODE);
  } else {
    echo '0';
  }
}

/* ===== Último registro (solo informativo) ===== */
function ultimo_registro(){
  $db = new DB(); $cn = $db->conectar();
  $q = $cn->prepare("SELECT cod_orden FROM orden_compra ORDER BY cod_orden DESC LIMIT 1");
  $q->execute();
  if ($q->rowCount()){
    echo json_encode($q->fetch(PDO::FETCH_OBJ), JSON_UNESCAPED_UNICODE);
  } else {
    echo '0';
  }
}

/* ===== Anular ===== */
function anular($id){
  $db = new DB(); $cn = $db->conectar();
  $q = $cn->prepare("UPDATE orden_compra SET oc_estado='ANULADO' WHERE cod_orden=?");
  $q->execute([$id]);
  echo json_encode(['ok'=>true]);
}

/* ===== Pendientes (para combos) ===== */
function Orden_pendientes(){
  $base_datos = new DB();
  $cn = $base_datos->conectar();

  $sql = "
    SELECT 
        oc.cod_orden               AS cod_orden_compra,
        oc.oc_fecha_emision        AS fecha_orden,
        oc.oc_estado               AS estado,
        pvc.pro_razonsocial        AS nom_ape_prov,
        COALESCE(SUM(do.cantidad * do.prec_uni), 0) AS total
    FROM orden_compra oc
    JOIN proveedor pvc
      ON pvc.cod_proveedor = oc.cod_proveedor
    LEFT JOIN det_orden do
      ON do.cod_orden = oc.cod_orden
    WHERE oc.oc_estado = 'PENDIENTE'
    GROUP BY oc.cod_orden, oc.oc_fecha_emision, oc.oc_estado, pvc.pro_razonsocial
    ORDER BY oc.cod_orden DESC
  ";

  $q = $cn->prepare($sql);
  $q->execute();

  if ($q->rowCount()) {
    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
  } else {
    echo '0';
  }
}

/* ===== NUEVO: traer cabecera por id (para autocompletar proveedor) ===== */
function traer_cabecera($id){
  if ($id <= 0){ echo '0'; return; }
  $db = new DB(); $cn = $db->conectar();
  $q = $cn->prepare("
    SELECT 
      oc.cod_orden        AS cod_orden_compra,
      oc.oc_fecha_emision AS fecha_orden,
      oc.oc_estado        AS estado,
      oc.cod_proveedor,
      pvc.pro_razonsocial AS razon_social_prov
    FROM orden_compra oc
    JOIN proveedor pvc ON pvc.cod_proveedor = oc.cod_proveedor
    WHERE oc.cod_orden = :id
    LIMIT 1
  ");
  $q->execute([':id'=>$id]);
  if ($q->rowCount()){
    echo json_encode($q->fetch(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
  } else {
    echo '0';
  }
}

function guardar_detalles($raw){
  try{
    $items = json_decode($raw, true);
    if (!is_array($items) || empty($items)) {
      throw new Exception('JSON inválido: se esperaba un array no vacío');
    }

    $db = new DB();
    $cn = $db->conectar();
    $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Helper: ¿existe columna?
    $columnaExiste = function(string $tabla, string $col) use ($cn): bool {
      $sql = "SELECT 1
              FROM INFORMATION_SCHEMA.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = :t AND COLUMN_NAME = :c
              LIMIT 1";
      $st = $cn->prepare($sql);
      $st->execute([':t'=>$tabla, ':c'=>$col]);
      return (bool)$st->fetchColumn();
    };

    // Detectar si det_orden usa cod_producto o cod_insumos
    $usaProducto = $columnaExiste('det_orden','cod_producto');
    $usaInsumos  = $columnaExiste('det_orden','cod_insumos');
    if (!$usaProducto && !$usaInsumos) {
      throw new Exception("La tabla det_orden no tiene cod_producto ni cod_insumos");
    }

    // Nombre de la columna real a usar en det_orden
    $colCodItem = $usaProducto ? 'cod_producto' : 'cod_insumos';

    // Validación básica + normalización (acepta que el cliente mande cualquiera de los 2 nombres)
    foreach ($items as $i => &$d) {
      $d = is_array($d) ? $d : [];
      $d['cod_orden'] = (int)($d['cod_orden'] ?? 0);
      $d['cantidad']  = (int)($d['cantidad']  ?? 0);
      $d['costo']     = (int)($d['costo']     ?? 0);

      // mapear a la columna real
      if (!isset($d[$colCodItem])) {
        if ($colCodItem === 'cod_producto' && isset($d['cod_insumos'])) $d['cod_producto'] = (int)$d['cod_insumos'];
        if ($colCodItem === 'cod_insumos'  && isset($d['cod_producto'])) $d['cod_insumos']  = (int)$d['cod_producto'];
      }
      $d[$colCodItem] = (int)($d[$colCodItem] ?? 0);

      if ($d['cod_orden'] <= 0)      throw new Exception("Ítem #$i: cod_orden inválido");
      if ($d[$colCodItem] <= 0)      throw new Exception("Ítem #$i: $colCodItem inválido");
      if ($d['cantidad']  <= 0)      throw new Exception("Ítem #$i: cantidad debe ser > 0");
      if ($d['costo']     <= 0)      throw new Exception("Ítem #$i: costo debe ser > 0");
    }
    unset($d); // rompe referencia

    $cn->beginTransaction();

    // INSERT (si querés evitar duplicados por (cod_orden, item), podés crear un índice único y usar ON DUPLICATE KEY)
    if ($usaProducto){
      $st = $cn->prepare("
        INSERT INTO det_orden (cod_orden, cod_producto, cantidad, prec_uni)
        VALUES (:cod_orden, :cod_item, :cantidad, :prec_uni)
      ");
    } else {
      $st = $cn->prepare("
        INSERT INTO det_orden (cod_orden, cod_insumos, cantidad, prec_uni)
        VALUES (:cod_orden, :cod_item, :cantidad, :prec_uni)
      ");
    }

    $inserted = 0;
    foreach($items as $d){
      $st->execute([
        ':cod_orden' => $d['cod_orden'],
        ':cod_item'  => $d[$colCodItem],
        ':cantidad'  => $d['cantidad'],
        ':prec_uni'  => $d['costo'],
      ]);
      $inserted += $st->rowCount();
    }

    $cn->commit();
    echo json_encode(['ok'=>true, 'inserted'=>$inserted], JSON_UNESCAPED_UNICODE);

  }catch(Throwable $e){
    if (isset($cn) && $cn->inTransaction()) $cn->rollBack();
    http_response_code(400);
    echo json_encode(['ok'=>false, 'msg'=>'No se pudo guardar detalles', 'extra'=>$e->getMessage()]);
  }
}