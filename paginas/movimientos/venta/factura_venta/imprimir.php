<?php
require_once '../../../../conexion/db.php';

$base = new DB();
$cn = $base->conectar();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Content-Type: text/plain; charset=utf-8'); echo "ID inválido"; exit; }

/* --- Cabecera + cliente --- */
$sqlCab = $cn->prepare("
  SELECT f.*,
         c.nombre_cliente, c.ruc, c.telefono
  FROM factura_venta f
  JOIN cliente_1 c ON c.cod_cliente = f.cod_cliente
  WHERE f.id_factura = :id
  LIMIT 1
");
$sqlCab->execute([':id'=>$id]);
$cab = $sqlCab->fetch(PDO::FETCH_ASSOC);
if (!$cab) { header('Content-Type: text/plain; charset=utf-8'); echo "Factura no encontrada"; exit; }

/* --- Detalles --- */
$sqlDet = $cn->prepare("
  SELECT d.*, p.nombre AS producto
  FROM factura_venta_detalle d
  JOIN producto p ON p.cod_producto = d.cod_producto
  WHERE d.id_factura = :id
  ORDER BY d.id_factura, d.cod_producto
");
$sqlDet->execute([':id'=>$id]);
$detalles = $sqlDet->fetchAll(PDO::FETCH_ASSOC);

/* --- Total (solo neto: cantidad * precio - descuento) --- */
$totalGeneral = 0;
foreach ($detalles as $d) {
  $cant   = (int)$d['cantidad'];
  $precio = (int)$d['precio_unitario'];
  $desc   = (int)$d['descuento'];
  $neto   = max($cant * $precio - $desc, 0);
  $totalGeneral += $neto;
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function py($n){ return number_format((int)$n, 0, ',', '.'); }
function dmy($s){ $t=strtotime($s); return $t?date('d/m/Y',$t):h($s); }

$numeroCompleto = !empty($cab['numero_completo'])
  ? $cab['numero_completo']
  : ($cab['establecimiento'].'-'.$cab['punto_expedicion'].'-'.$cab['numero']);

$isAnulada = (isset($cab['estado']) && strtoupper($cab['estado'])==='ANULADO');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Factura de Venta</title>
  <link href="../../../../assets/plugins/bootstrap/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{
      --ink:#1f2937;
      --muted:#6b7280;
      --line:#e5e7eb;
      --accent:#0d6efd;
    }
    @page { margin: 12mm; }
    @media print { .no-print{display:none} .print-only{display:block} }

    body{ font-family: Arial, sans-serif; color:var(--ink); background:#fff; }
    .doc{ max-width: 1000px; margin: 0 auto; }

    /* Header */
    .doc-header{ display:flex; align-items:flex-start; justify-content:space-between; gap:20px; }
    .doc-brand{ display:flex; align-items:center; gap:16px; }
    .doc-brand img{ max-height:64px; }
    .brand-text{ line-height:1.2 }
    .brand-name{ font-size:18px; font-weight:800; letter-spacing:.02em }
    .brand-sub{ font-size:12px; color:var(--muted) }

    .doc-meta{ text-align:right }
    .doc-title{ font-weight:800; letter-spacing:.06em; text-transform:uppercase; }
    .doc-title small{ display:block; color:var(--muted); font-weight:600 }
    .doc-number{ display:inline-block; margin-top:6px; padding:.4rem .7rem; border:1px solid var(--line);
      border-radius:10px; font-weight:700; }

    .estado-badge{ margin-top:8px; display:inline-block; padding:.25rem .6rem; border-radius:999px;
      font-size:.85rem; font-weight:700; color:#fff; }
    .estado-vigente{ background:#198754; }
    .estado-anulado{ background:#dc3545; }

    hr.sep{ border:0; border-top:1px solid var(--line); margin:14px 0; }

    /* Cards */
    .box{ border:1px solid var(--line); border-radius:12px; padding:12px 14px; height:100%; }
    .box h6{ font-size:12px; text-transform:uppercase; color:var(--muted); letter-spacing:.04em; margin-bottom:6px; }
    .kv{ display:flex; gap:8px; margin:2px 0; }
    .kv .k{ min-width:110px; color:var(--muted); }
    .kv .v{ font-weight:600; }

    /* Table */
    table.table{ width:100%; border:1px solid var(--line); }
    .table thead th{
      background:#f8f9fa;
      border-bottom:1px solid var(--line);
      text-transform:uppercase;
      font-size:12px; letter-spacing:.04em;
      vertical-align:middle; text-align:center;
      padding:.6rem .5rem;
    }
    .table tbody td{
      vertical-align:middle; text-align:center; padding:.55rem .5rem;
    }
    .text-left{ text-align:left !important; }
    .text-right{ text-align:right !important; }
    .nowrap{ white-space:nowrap; }
    .w-40{ width:40% }
    .w-10{ width:10% }
    .w-12{ width:12% }

    /* Total (simple) */
    .grand-total{
      margin-left:auto; margin-top:8px; border:1px solid var(--line); border-radius:12px;
      width:320px; overflow:hidden;
    }
    .grand-total .rowx{
      display:flex; justify-content:space-between; align-items:center;
      padding:.7rem .9rem;
    }
    .grand-total .label{ color:var(--muted); font-weight:600; }
    .grand-total .amount{ font-weight:800; font-size:1.25rem; }

    /* Notas y firmas */
    .foot-note{ color:var(--muted); font-size:12px; }
    .signs{ margin-top:28px; display:flex; gap:30px; }
    .sign{ flex:1; text-align:center }
    .sign .line{ border-top:1px solid var(--ink); margin-top:46px; padding-top:4px; font-weight:600 }

    /* Watermark ANULADO */
    .watermark{
      position:fixed; inset:0; display:flex; align-items:center; justify-content:center;
      pointer-events:none; opacity:.09; transform:rotate(-28deg);
      font-size:130px; color:#dc3545; font-weight:900; letter-spacing:.1em;
    }

    .avoid-break{ page-break-inside: avoid; }
  </style>
</head>
<body>
  <?php if ($isAnulada): ?>
    <div class="watermark">ANULADO</div>
  <?php endif; ?>

  <div class="doc">
    <!-- Header -->
    <div class="doc-header avoid-break">
      <div class="doc-brand">
        <!-- Si tenés logo, descomenta -->
        <!-- <img src='../../../../img/membrete.png' alt='Logo'> -->
        <div class="brand-text">
          <div class="brand-name">Factura de Venta</div>
          <div class="brand-sub">Timbrado <?= h($cab['timbrado']) ?></div>
        </div>
      </div>
      <div class="doc-meta">
        <div class="doc-title">COMPROBANTE <small>Original</small></div>
        <div class="doc-number"><?= h($numeroCompleto) ?></div>
        <div class="estado-badge <?= $isAnulada?'estado-anulado':'estado-vigente' ?>">
          <?= h($cab['estado']) ?>
        </div>
      </div>
    </div>

    <hr class="sep">

    <!-- Info -->
    <div class="row avoid-break">
      <div class="col-md-4 mb-3">
        <div class="box">
          <h6>Cliente</h6>
          <div class="kv"><div class="k">Nombre</div><div class="v"><?= h($cab['nombre_cliente']) ?></div></div>
          <div class="kv"><div class="k">RUC/CI</div><div class="v"><?= h($cab['ruc']) ?></div></div>
          <div class="kv"><div class="k">Teléfono</div><div class="v"><?= h($cab['telefono']) ?></div></div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="box">
          <h6>Documento</h6>
          <div class="kv"><div class="k">Fecha</div><div class="v"><?= dmy($cab['fecha_emision']) ?></div></div>
          <div class="kv"><div class="k">Condición</div><div class="v"><?= h($cab['condicion_venta']) ?></div></div>
          <div class="kv"><div class="k">Moneda</div><div class="v"><?= h($cab['moneda']) ?></div></div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="box">
          <h6>Serie</h6>
          <div class="kv"><div class="k">Est./Pto.</div><div class="v"><?= h($cab['establecimiento']) ?> / <?= h($cab['punto_expedicion']) ?></div></div>
          <div class="kv"><div class="k">Vigencia</div><div class="v"><?= dmy($cab['timbrado_vigencia_desde']) ?> a <?= dmy($cab['timbrado_vigencia_hasta']) ?></div></div>
        </div>
      </div>
    </div>

    <!-- Detalle (sin columna IVA) -->
    <div class="avoid-break">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th class="w-10">#</th>
            <th class="w-40 text-left">Producto</th>
            <th class="w-10">Cant.</th>
            <th class="w-12">Precio</th>
            <th class="w-12">Desc.</th>
            <th class="w-12">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($detalles): ?>
            <?php foreach ($detalles as $i=>$d):
              $cant   = (int)$d['cantidad'];
              $precio = (int)$d['precio_unitario'];
              $desc   = (int)$d['descuento'];
              $sub    = max($cant*$precio - $desc, 0);
            ?>
              <tr>
                <td class="nowrap"><?= $i+1 ?></td>
                <td class="text-left"><?= h($d['producto']) ?></td>
                <td class="nowrap"><?= py($cant) ?></td>
                <td class="nowrap text-right"><?= py($precio) ?></td>
                <td class="nowrap text-right"><?= py($desc) ?></td>
                <td class="nowrap text-right"><?= py($sub) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center text-muted">Sin ítems.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Total simple -->
    <div class="grand-total avoid-break">
      <div class="rowx">
        <span class="label">Total General</span>
        <span class="amount"><?= py($totalGeneral) ?></span>
      </div>
    </div>

    <!-- Observación -->
    <?php if (!empty($cab['observacion'])): ?>
      <div class="box mt-3 avoid-break">
        <h6>Observación</h6>
        <div><?= h($cab['observacion']) ?></div>
      </div>
    <?php endif; ?>

    <!-- Nota + firmas -->
    <div class="row mt-4 avoid-break">
      <div class="col-12 foot-note">
        Este comprobante se emite bajo el timbrado <strong><?= h($cab['timbrado']) ?></strong>, vigente del
        <strong><?= dmy($cab['timbrado_vigencia_desde']) ?></strong> al
        <strong><?= dmy($cab['timbrado_vigencia_hasta']) ?></strong>.
        <?= $isAnulada ? '<span class="text-danger font-weight-bold"> Documento ANULADO.</span>' : '' ?>
      </div>
      <div class="col-12 signs">
        <div class="sign"><div class="line">Entregué Conforme</div></div>
        <div class="sign"><div class="line">Recibí Conforme</div></div>
      </div>
    </div>
  </div>

  <script> window.print(); </script>
</body>
</html>
