<?php
require_once dirname(__DIR__, 4) . '/conexion/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { echo 'ID inválido'; exit; }

$db  = new DB();
$pdo = $db->conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sqlCab = "SELECT s.*, c.nombre_cliente, c.ruc, c.telefono
            FROM servicios s
            JOIN cliente_1 c ON c.cod_cliente = s.id_cliente
            WHERE s.id_servicio = ?";
$st = $pdo->prepare($sqlCab);
$st->execute([$id]);
$cab = $st->fetch(PDO::FETCH_OBJ);
if(!$cab){ echo 'No encontrado'; exit; }

$det = $pdo->prepare("SELECT * FROM servicio_detalles WHERE id_servicio=?");
$det->execute([$id]);
$detalles = $det->fetchAll(PDO::FETCH_OBJ);

function fmt0($n){ return number_format((float)$n,0,',','.'); }
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Servicio #<?= htmlspecialchars($cab->id_servicio) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<style>
  body{ background:#f8f9fa }
  .doc{ max-width:960px; margin:24px auto; background:#fff; padding:24px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,.06) }
  .table th, .table td { vertical-align: middle; }
</style>
</head>
<body onload="window.print();">
  <div class="doc">
    <div class="d-flex justify-content-between">
      <h3 class="mb-3">SERVICIO #<?= htmlspecialchars($cab->id_servicio) ?></h3>
      <div class="text-end">
        <div><strong>Fecha:</strong> <?= htmlspecialchars($cab->fecha_servicio) ?></div>
        <div><strong>Estado:</strong> <?= htmlspecialchars($cab->estado) ?></div>
      </div>
    </div>

    <div class="mb-3">
      <strong>Cliente:</strong> <?= htmlspecialchars($cab->nombre_cliente) ?> |
      <strong>RUC:</strong> <?= htmlspecialchars($cab->ruc) ?> |
      <strong>Tel:</strong> <?= htmlspecialchars($cab->telefono) ?>
    </div>

    <?php if ($detalles): ?>
    <table class="table table-sm table-bordered">
      <thead class="table-light">
        <tr>
          <th>Tipo</th>
          <th>Descripción</th>
          <th>Prod. Relacionado</th>
          <th class="text-end" style="width:8%">Cant.</th>
          <th class="text-end" style="width:12%">Precio</th>
          <th class="text-end" style="width:12%">Subtotal</th>
          <th>Obs.</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($detalles as $d): ?>
          <tr>
            <td><?= htmlspecialchars($d->tipo_servicio) ?></td>
            <td><?= htmlspecialchars($d->descripcion) ?></td>
            <td><?= htmlspecialchars($d->producto_relacionado) ?></td>
            <td class="text-end"><?= fmt0($d->cantidad) ?></td>
            <td class="text-end"><?= fmt0($d->precio_unitario) ?></td>
            <td class="text-end"><?= fmt0($d->subtotal) ?></td>
            <td><?= htmlspecialchars($d->observaciones) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>

    <div class="d-flex justify-content-end mt-4">
      <table class="table w-auto">
        <tr class="table-primary"><th class="text-end">TOTAL</th><td class="text-end fw-bold"><?= fmt0($cab->total) ?></td></tr>
      </table>
    </div>

    <?php if (!empty($cab->observaciones)): ?>
      <div class="mt-3"><strong>Observaciones:</strong> <?= nl2br(htmlspecialchars($cab->observaciones)) ?></div>
    <?php endif; ?>
  </div>
</body>
</html>

