<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

session_start();
require_once '../conexion/db.php';

try { $cn = (new DB())->conectar(); }
catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'msg'=>'Error de conexión BD','extra'=>$e->getMessage()]);
  exit;
}

function ok($d){ echo json_encode(['ok'=>true,'data'=>$d], JSON_UNESCAPED_UNICODE); exit; }
function fail($m,$extra=null){ http_response_code(400); echo json_encode(['ok'=>false,'msg'=>$m,'extra'=>$extra]); exit; }

/** Ping de prueba */
if (isset($_REQUEST['ping'])) ok('pong');

/** === SERIE ACTIVA (timbrado/est/pto y correlativo) === */
if (isset($_REQUEST['serie_activa'])) {
  $hoy = date('Y-m-d');
  $q = $cn->prepare("
    SELECT id_serie, timbrado, vig_desde, vig_hasta, establecimiento, punto_expedicion,
           numero_actual, numero_fin
    FROM factura_serie
    WHERE activo=1 AND :hoy BETWEEN vig_desde AND vig_hasta
    ORDER BY id_serie DESC
    LIMIT 1
  ");
  $q->execute([':hoy'=>$hoy]);
  $row = $q->fetch(PDO::FETCH_ASSOC);
  if(!$row){ fail('No hay serie activa vigente'); }
  ok($row);
}

/** === CLIENTES para combos === */
if (isset($_REQUEST['clientes'])) {
  $q = $cn->prepare("SELECT cod_cliente, nombre_cliente, ruc, telefono
                     FROM cliente_1
                     ORDER BY nombre_cliente");
  $q->execute();
  ok($q->fetchAll(PDO::FETCH_ASSOC));
}

/** === PRODUCTOS para combos === */
if (isset($_REQUEST['productos'])) {
  $q = $cn->prepare("SELECT
                       cod_producto,
                       nombre,
                       descripcion,
                       precio,
                       COALESCE(stock,0) AS stock,
                       estado,
                       0 AS iva              -- compat con front: siempre 0
                     FROM producto
                     ORDER BY nombre");
  $q->execute();
  ok($q->fetchAll(PDO::FETCH_ASSOC));
}

/** === GUARDAR CABECERA con numeración automática === */
if (isset($_REQUEST['guardar_cabecera_auto'])) {
  $obj = json_decode($_REQUEST['guardar_cabecera_auto'], true);
  if(!$obj){ fail('JSON inválido'); }

  // Normalizar condicion_venta
  $cond = strtoupper(trim($obj['condicion_venta'] ?? 'CONTADO'));
  $cond = strtr($cond, ['É'=>'E','Á'=>'A','Í'=>'I','Ó'=>'O','Ú'=>'U']);
  if(!in_array($cond, ['CONTADO','CREDITO'], true)) $cond = 'CONTADO';

  try {
    $cn->beginTransaction();

    $hoy = date('Y-m-d');
    $sel = $cn->prepare("
      SELECT * FROM factura_serie
      WHERE activo=1 AND :hoy BETWEEN vig_desde AND vig_hasta
      ORDER BY id_serie DESC
      LIMIT 1
      FOR UPDATE
    ");
    $sel->execute([':hoy'=>$hoy]);
    $serie = $sel->fetch(PDO::FETCH_ASSOC);
    if(!$serie) throw new Exception('No hay serie activa vigente');

    $qmax = $cn->prepare("
      SELECT MAX(CAST(numero AS UNSIGNED)) AS maxnum
      FROM factura_venta
      WHERE timbrado=? AND establecimiento=? AND punto_expedicion=?
      FOR UPDATE
    ");
    $qmax->execute([$serie['timbrado'], $serie['establecimiento'], $serie['punto_expedicion']]);
    $max = (int)($qmax->fetchColumn() ?: 0);

    $siguiente = max((int)$serie['numero_actual'], $max) + 1;
    if($siguiente > (int)$serie['numero_fin']) throw new Exception('Se alcanzó el máximo de la serie');
    $numero_str = str_pad((string)$siguiente, 7, '0', STR_PAD_LEFT);

    $st = $cn->prepare("INSERT INTO factura_venta
      (fecha_emision,cod_cliente,condicion_venta,moneda,
       timbrado,timbrado_vigencia_desde,timbrado_vigencia_hasta,
       establecimiento,punto_expedicion,numero,observacion)
      VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $st->execute([
      $obj['fecha_emision'],
      $obj['cod_cliente'],
      $cond,
      $obj['moneda'] ?? 'PYG',
      $serie['timbrado'],
      $serie['vig_desde'],
      $serie['vig_hasta'],
      $serie['establecimiento'],
      $serie['punto_expedicion'],
      $numero_str,
      $obj['observacion'] ?? null
    ]);
    $id = (int)$cn->lastInsertId();

    $up = $cn->prepare("UPDATE factura_serie SET numero_actual=? WHERE id_serie=?");
    $up->execute([$siguiente, $serie['id_serie']]);

    $cn->commit();
    ok([
      'id_factura'      => $id,
      'numero'          => $numero_str,
      'numero_completo' => $serie['establecimiento'].'-'.$serie['punto_expedicion'].'-'.$numero_str,
      'timbrado'        => $serie['timbrado']
    ]);

  } catch(Throwable $e){
    $cn->rollBack();
    fail('No se pudo guardar cabecera (auto)', $e->getMessage());
  }
}

/** === GUARDAR DETALLES === */
/** === GUARDAR DETALLES === */
if (isset($_REQUEST['guardar_detalles'])) {
  $raw = $_REQUEST['guardar_detalles'] ?? '';
  $arr = json_decode($raw, true);
  if(!$arr || empty($arr['id_factura']) || !isset($arr['detalles']) || !is_array($arr['detalles'])) {
    fail('JSON inválido', 'payload=' . substr($raw,0,300));
  }

  try{
    $cn->beginTransaction();

    $selProd = $cn->prepare("SELECT precio, descripcion FROM producto WHERE cod_producto=?");
    $insDet  = $cn->prepare("INSERT INTO factura_venta_detalle
                (id_factura,cod_producto,descripcion,cantidad,precio_unitario,descuento)
                VALUES (?,?,?,?,?,?)");

    foreach($arr['detalles'] as $i => $d){
      $cod  = (int)($d['cod_producto'] ?? 0);
      $cant = (int)($d['cantidad'] ?? 0);
      $desc = (int)($d['descuento'] ?? 0);
      if($cod <= 0)  throw new Exception("Fila $i: cod_producto vacío");
      if($cant <= 0) throw new Exception("Fila $i: cantidad <= 0");
      if($desc < 0)  throw new Exception("Fila $i: descuento < 0");

      $selProd->execute([$cod]);
      $p = $selProd->fetch(PDO::FETCH_ASSOC);
      if(!$p) throw new Exception("Producto no encontrado: $cod");

      $precio = (int)($d['precio_unitario'] ?? $p['precio']);
      if($precio <= 0) throw new Exception("Fila $i: precio <= 0");

      $descTxt = mb_substr((string)$p['descripcion'], 0, 255, 'UTF-8');
      $insDet->execute([(int)$arr['id_factura'], $cod, $descTxt, $cant, $precio, $desc]);
    }

    $cn->commit();
    ok(true);
  }catch(Throwable $e){
    $cn->rollBack();
    fail('Error al guardar detalles', $e->getMessage());
  }
}

/** === LISTAR CABECERAS === */
/** === LISTAR CABECERAS === */
if (isset($_REQUEST['leer'])) {
  $where = "";
  $params = [];

  // Filtro por estado (opcional)
  if (isset($_REQUEST['estado']) && $_REQUEST['estado'] !== '') {
    $where .= " AND f.estado = :estado";
    $params[':estado'] = $_REQUEST['estado'];
  }

  // Filtro por rango de fechas (opcional)
  if (isset($_REQUEST['desde']) && $_REQUEST['desde'] !== '') {
    $dt = DateTime::createFromFormat('Y-m-d', $_REQUEST['desde']);
    if ($dt) {
      $where .= " AND f.fecha_emision >= :desde";
      $params[':desde'] = $dt->format('Y-m-d');
    }
  }
  if (isset($_REQUEST['hasta']) && $_REQUEST['hasta'] !== '') {
    $dt = DateTime::createFromFormat('Y-m-d', $_REQUEST['hasta']);
    if ($dt) {
      $where .= " AND f.fecha_emision <= :hasta";
      $params[':hasta'] = $dt->format('Y-m-d');
    }
  }

  // Búsqueda (nombre cliente, nro, nro completo)
  if (isset($_REQUEST['buscar']) && $_REQUEST['buscar'] !== '') {
    $b = '%'.$_REQUEST['buscar'].'%';
    $where .= " AND (
      c.nombre_cliente LIKE :b1
      OR f.numero LIKE :b2
      OR CONCAT(f.establecimiento,'-',f.punto_expedicion,'-',f.numero) LIKE :b3
    )";
    $params[':b1'] = $b;
    $params[':b2'] = $b;
    $params[':b3'] = $b;
  }

  $sql = "SELECT
            f.id_factura,
            f.fecha_emision,
            f.numero,
            f.establecimiento,
            f.punto_expedicion,
            f.numero_completo,
            f.estado,
            f.total_general,           -- (por si ya lo usás en otros lados)
            c.nombre_cliente,
            COALESCE(t.total_neto,0) AS total_neto  -- <- SOLO PRECIOS (sin IVA)
          FROM factura_venta f
          JOIN cliente_1 c ON c.cod_cliente = f.cod_cliente
          LEFT JOIN (
            SELECT id_factura,
                   SUM(GREATEST(cantidad*precio_unitario - descuento, 0)) AS total_neto
            FROM factura_venta_detalle
            GROUP BY id_factura
          ) t ON t.id_factura = f.id_factura
          WHERE 1=1 {$where}
          ORDER BY f.id_factura DESC";

  $q = $cn->prepare($sql);
  $q->execute($params);
  ok($q->fetchAll(PDO::FETCH_ASSOC));
}


/** === TRAER cabecera+detalle === */
if (isset($_REQUEST['traer'])) {
  $id=(int)$_REQUEST['traer'];
  $qc=$cn->prepare("SELECT * FROM factura_venta WHERE id_factura=?");
  $qc->execute([$id]); $cab=$qc->fetch(PDO::FETCH_ASSOC);
  if(!$cab) fail('Factura no encontrada');

  $qd=$cn->prepare("SELECT d.*, p.nombre AS producto
                    FROM factura_venta_detalle d
                    JOIN producto p ON p.cod_producto = d.cod_producto
                    WHERE d.id_factura=?");
  $qd->execute([$id]);
  $det=$qd->fetchAll(PDO::FETCH_ASSOC);

  ok(['cabecera'=>$cab,'detalles'=>$det]);
}

/** === ANULAR === */
if (isset($_REQUEST['anular'])) {
  $id=(int)$_REQUEST['anular'];
  $q=$cn->prepare("UPDATE factura_venta SET estado='ANULADO' WHERE id_factura=?");
  $q->execute([$id]);
  ok(true);
}

/** === ACTUALIZAR CABECERA (edición) === */
if (isset($_REQUEST['actualizar_cabecera'])) {
  $obj = json_decode($_REQUEST['actualizar_cabecera'], true);
  if(!$obj || empty($obj['id_factura'])) fail('JSON inválido');

  $id = (int)$obj['id_factura'];

  $cond = strtoupper(trim($obj['condicion_venta'] ?? 'CONTADO'));
  $cond = strtr($cond, ['É'=>'E','Á'=>'A','Í'=>'I','Ó'=>'O','Ú'=>'U']);
  if(!in_array($cond, ['CONTADO','CREDITO'], true)) $cond = 'CONTADO';

  $fecha = trim($obj['fecha_emision'] ?? '');
  if (preg_match('#^\d{2}/\d{2}/\d{4}$#', $fecha)) {
    $dt = DateTime::createFromFormat('d/m/Y', $fecha);
    if ($dt) $fecha = $dt->format('Y-m-d');
  }

  try{
    $q = $cn->prepare("UPDATE factura_venta SET
        fecha_emision=?, cod_cliente=?, condicion_venta=?, moneda=?, observacion=?
      WHERE id_factura=? AND estado<>'ANULADO'");
    $q->execute([
      $fecha,
      (int)$obj['cod_cliente'],
      $cond,
      $obj['moneda'] ?? 'PYG',
      $obj['observacion'] ?? null,
      $id
    ]);

    if ($q->rowCount() === 0) {
      $chk = $cn->prepare("SELECT estado FROM factura_venta WHERE id_factura=?");
      $chk->execute([$id]);
      $row = $chk->fetch(PDO::FETCH_ASSOC);
      if (!$row) fail('Factura no existe', 'id='.$id);
      if ($row['estado'] === 'ANULADO') fail('Factura ANULADA, no se puede editar');
      ok(true); // sin cambios = OK
    } else {
      ok(true);
    }
  }catch(Throwable $e){
    fail('Error al actualizar cabecera', $e->getMessage());
  }
}

/** === REEMPLAZAR DETALLES (edición) === */
/** === REEMPLAZAR DETALLES (edición) === */
if (isset($_REQUEST['reemplazar_detalles'])) {
  $raw = $_REQUEST['reemplazar_detalles'] ?? '';
  $arr = json_decode($raw, true);
  if(!$arr || empty($arr['id_factura']) || !isset($arr['detalles']) || !is_array($arr['detalles'])) {
    fail('JSON inválido', 'payload=' . substr($raw,0,300));
  }

  try{
    $cn->beginTransaction();

    $del = $cn->prepare("DELETE FROM factura_venta_detalle WHERE id_factura=?");
    $del->execute([(int)$arr['id_factura']]);

    $selProd = $cn->prepare("SELECT precio, descripcion FROM producto WHERE cod_producto=?");
    $insDet  = $cn->prepare("INSERT INTO factura_venta_detalle
                (id_factura,cod_producto,descripcion,cantidad,precio_unitario,descuento)
                VALUES (?,?,?,?,?,?)");

    foreach($arr['detalles'] as $i => $d){
      $cod  = (int)($d['cod_producto'] ?? 0);
      $cant = (int)($d['cantidad'] ?? 0);
      $desc = (int)($d['descuento'] ?? 0);
      if($cod <= 0)  throw new Exception("Fila $i: cod_producto vacío");
      if($cant <= 0) throw new Exception("Fila $i: cantidad <= 0");
      if($desc < 0)  throw new Exception("Fila $i: descuento < 0");

      $selProd->execute([$cod]);
      $p = $selProd->fetch(PDO::FETCH_ASSOC);
      if(!$p) throw new Exception("Producto no encontrado: $cod");

      $precio = (int)($d['precio_unitario'] ?? $p['precio']);
      if($precio <= 0) throw new Exception("Fila $i: precio <= 0");

      $descTxt = mb_substr((string)$p['descripcion'], 0, 255, 'UTF-8');
      $insDet->execute([(int)$arr['id_factura'], $cod, $descTxt, $cant, $precio, $desc]);
    }

    $cn->commit();
    ok(true);
  }catch(Throwable $e){
    $cn->rollBack();
    fail('No se pudieron reemplazar los detalles', $e->getMessage());
  }
}


/** Default (debe ir al FINAL) */
fail('Acción no reconocida');
