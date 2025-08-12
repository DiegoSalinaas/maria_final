<?php
require_once '../conexion/db.php';
header('Content-Type: application/json; charset=utf-8');

// Acepta varias claves comunes para el id de la OC
$id = 0;
if     (isset($_POST['id']))               $id = (int)$_POST['id'];
elseif (isset($_POST['cod_orden_compra'])) $id = (int)$_POST['cod_orden_compra'];
elseif (isset($_POST['orden']))            $id = (int)$_POST['orden'];
elseif (isset($_POST['oc']))               $id = (int)$_POST['oc'];

if ($id <= 0) { echo '0'; exit; }

try{
  $db = new DB();
  $cn = $db->conectar();
  $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Helper: verificar columna en la tabla
  $columnaExiste = function(string $tabla, string $col) use ($cn): bool {
    $sql = "SELECT 1
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :t
              AND COLUMN_NAME = :c
            LIMIT 1";
    $st = $cn->prepare($sql);
    $st->execute([':t'=>$tabla, ':c'=>$col]);
    return (bool)$st->fetchColumn();
  };

  // Detectar si det_orden usa cod_producto o cod_insumos
  $usaProducto = $columnaExiste('det_orden','cod_producto');
  $usaInsumos  = $columnaExiste('det_orden','cod_insumos');

  if (!$usaProducto && !$usaInsumos) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'msg'=>'det_orden no tiene cod_producto ni cod_insumos']);
    exit;
  }

  if ($usaProducto) {
    // det_orden.cod_producto -> producto.cod_producto
    $sql = "
      SELECT
        d.cod_producto                             AS cod_producto,
        COALESCE(p.nombre, CONCAT('Prod #', d.cod_producto)) AS nombre_producto,
        d.prec_uni                                  AS costo,
        d.cantidad
      FROM det_orden d
      LEFT JOIN producto p
        ON p.cod_producto = d.cod_producto
      WHERE d.cod_orden = :id
      ORDER BY p.nombre IS NULL, p.nombre, d.cod_producto
    ";
  } else {
    // det_orden.cod_insumos -> insumos.cod_insumos (normalizamos como cod_producto en la salida)
    $sql = "
      SELECT
        d.cod_insumos                               AS cod_producto,
        COALESCE(i.descripcion, CONCAT('Insumo #', d.cod_insumos)) AS nombre_producto,
        d.prec_uni                                  AS costo,
        d.cantidad
      FROM det_orden d
      LEFT JOIN insumos i
        ON i.cod_insumos = d.cod_insumos
      WHERE d.cod_orden = :id
      ORDER BY i.descripcion IS NULL, i.descripcion, d.cod_insumos
    ";
  }

  $st = $cn->prepare($sql);
  $st->execute([':id' => $id]);

  if ($st->rowCount()){
    echo json_encode($st->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
  } else {
    echo '0';
  }

}catch(Throwable $e){
  http_response_code(400);
  echo json_encode(['ok'=>false,'msg'=>'No se pudo leer detalles','extra'=>$e->getMessage()]);
}
