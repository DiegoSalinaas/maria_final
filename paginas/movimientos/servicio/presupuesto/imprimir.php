<?php
// Sube 4 niveles desde .../paginas/movimientos/servicio/presupuesto/
require_once dirname(__DIR__, 4) . '/conexion/db.php';
// Alternativa (si preferís usar DOCUMENT_ROOT):
// require_once $_SERVER['DOCUMENT_ROOT'] . '/examen_maria_anibal/maria_final/conexion/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id<=0){ echo "ID inválido"; exit; }

$db = new DB();
$cn = $db->conectar();
$cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Cabecera + cliente
$sqlCab = "
  SELECT p.*, c.nombre_cliente, c.ruc, c.telefono
  FROM presupuestos p
  JOIN cliente_1 c ON c.cod_cliente = p.id_cliente
  WHERE p.id_presupuesto = ?";
$st = $cn->prepare($sqlCab);
$st->execute([$id]);
$cab = $st->fetch(PDO::FETCH_OBJ);
if(!$cab){ echo "No encontrado"; exit; }

// Detalles
$serv = $cn->prepare("SELECT * FROM presupuesto_servicios WHERE id_presupuesto=?");
$serv->execute([$id]); $servicios = $serv->fetchAll(PDO::FETCH_OBJ);

$ins = $cn->prepare("SELECT * FROM presupuesto_insumos WHERE id_presupuesto=?");
$ins->execute([$id]); $insumos = $ins->fetchAll(PDO::FETCH_OBJ);

function fmt0($n){ return number_format((float)$n,0,',','.'); }
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Presupuesto #<?= htmlspecialchars($cab->id_presupuesto) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<style>
  body{ background:#f8f9fa }
  .doc{ max-width:960px; margin:24px auto; background:#fff; padding:24px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,.06) }
  h3{ border-bottom:2px solid #0d6efd; padding-bottom:8px; margin-bottom:16px }
  .table th, .table td { vertical-align: middle; }
</style>
</head>
<body onload="window.print();">
  <div class="doc">
    <div class="d-flex justify-content-between">
      <h3 class="mb-3">PRESUPUESTO #<?= htmlspecialchars($cab->id_presupuesto) ?></h3>
      <div class="text-end">
        <div><strong>Fecha emisión:</strong> <?= htmlspecialchars($cab->fecha_emision) ?></div>
        <div><strong>Vencimiento:</strong> <?= htmlspecialchars($cab->fecha_vencimiento) ?></div>
        <div><strong>Estado:</strong> <?= htmlspecialchars($cab->estado) ?></div>
      </div>
    </div>

    <div class="mb-3">
      <strong>Cliente:</strong> <?= htmlspecialchars($cab->nombre_cliente) ?> |
      <strong>RUC:</strong> <?= htmlspecialchars($cab->ruc) ?> |
      <strong>Tel:</strong> <?= htmlspecialchars($cab->telefono) ?>
    </div>

    <?php if ($servicios): ?>
    <h5 class="mt-4">Servicios</h5>
    <table class="table table-sm table-bordered">
      <thead class="table-light">
        <tr>
          <th style="width:26%">Tipo</th>
          <th>Descripción</th>
          <th class="text-end" style="width:8%">Cant.</th>
          <th class="text-end" style="width:12%">Precio</th>
          <th class="text-end" style="width:12%">Desc.</th>
          <th class="text-end" style="width:12%">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($servicios as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s->tipo_servicio) ?></td>
            <td><?= htmlspecialchars($s->descripcion) ?></td>
            <td class="text-end"><?= fmt0($s->cantidad) ?></td>
            <td class="text-end"><?= fmt0($s->precio_unitario) ?></td>
            <td class="text-end"><?= fmt0($s->descuento) ?></td>
            <td class="text-end"><?= fmt0($s->total_linea) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>

    <?php if ($insumos): ?>
    <h5 class="mt-4">Insumos</h5>
    <table class="table table-sm table-bordered">
      <thead class="table-light">
        <tr>
          <th>Descripción</th>
          <th style="width:14%">Marca</th>
          <th style="width:14%">Modelo</th>
          <th class="text-end" style="width:8%">Cant.</th>
          <th class="text-end" style="width:12%">Precio</th>
          <th class="text-end" style="width:12%">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($insumos as $i): ?>
          <tr>
            <td><?= htmlspecialchars($i->descripcion) ?></td>
            <td><?= htmlspecialchars($i->marca) ?></td>
            <td><?= htmlspecialchars($i->modelo) ?></td>
            <td class="text-end"><?= fmt0($i->cantidad) ?></td>
            <td class="text-end"><?= fmt0($i->precio_unitario) ?></td>
            <td class="text-end"><?= fmt0($i->total_linea) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>

    <div class="d-flex justify-content-end mt-4">
      <table class="table w-auto">
        <tr><th class="text-end">Subtotal Servicios</th><td class="text-end"><?= fmt0($cab->subtotal_servicios) ?></td></tr>
        <tr><th class="text-end">Subtotal Insumos</th><td class="text-end"><?= fmt0($cab->subtotal_insumos) ?></td></tr>
        <tr class="table-primary"><th class="text-end">TOTAL</th><td class="text-end fw-bold"><?= fmt0($cab->total) ?></td></tr>
      </table>
    </div>

    <?php if (!empty($cab->observaciones)): ?>
      <div class="mt-3"><strong>Observaciones:</strong> <?= nl2br(htmlspecialchars($cab->observaciones)) ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
