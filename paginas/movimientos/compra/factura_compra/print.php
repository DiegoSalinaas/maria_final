<?php
require_once '../../../../conexion/db.php';
header('Content-Type: text/html; charset=utf-8');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { echo "ID inválido"; exit; }

$db = new DB();
$cn = $db->conectar();
$cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function fmt($n){ return number_format((float)$n, 0, ',', '.'); }

$sqlCab = "
  SELECT 
    fc.cod_registro                     AS cod_compra,
    fc.fecha_compra,
    fc.condicion,
    fc.timbrado,
    fc.fecha_vencimiento_timbrado       AS fecha_venc_timbrado,
    fc.nro_factura,
    p.cod_proveedor,
    p.pro_ruc,
    p.pro_razonsocial                   AS razon_social_prov,
    fc.estado_registro                  AS estado,
    u.usuario_alias                     AS nombre_apellido,
    COALESCE(SUM(dc.cantidad * dc.costo), 0) AS total
  FROM compra fc
  JOIN proveedor p  ON p.cod_proveedor = fc.cod_proveedor
  JOIN usuario   u  ON u.cod_usuario   = fc.cod_usuario
  LEFT JOIN detalle_compra dc ON dc.cod_compra = fc.cod_registro
  WHERE fc.cod_registro = :id
  GROUP BY fc.cod_registro
  LIMIT 1
";
$qCab = $cn->prepare($sqlCab);
$qCab->bindValue(':id', $id, PDO::PARAM_INT);
$qCab->execute();
$cab = $qCab->fetch(PDO::FETCH_OBJ);
if(!$cab){ echo "No se encontró la compra #$id"; exit; }

$sqlDet = "
  SELECT
    COALESCE(pr.nombre, CONCAT('Producto #', dpc.cod_insumos)) AS nombre_producto,
    dpc.cantidad,
    dpc.costo,
    (dpc.cantidad * dpc.costo) AS subtotal
  FROM detalle_compra dpc
  LEFT JOIN producto pr ON pr.cod_producto = dpc.cod_insumos
  WHERE dpc.cod_compra = :id
  ORDER BY pr.nombre IS NULL, pr.nombre, dpc.cod_insumos
";
$qDet = $cn->prepare($sqlDet);
$qDet->bindValue(':id', $id, PDO::PARAM_INT);
$qDet->execute();
$detalles = $qDet->fetchAll(PDO::FETCH_ASSOC);

$impresoEn = date('d/m/Y H:i');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Factura de Compra #<?= htmlspecialchars($cab->cod_compra) ?></title>
  <link href="../../../../vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{
      --fg:#1f2937;        /* gris oscuro */
      --muted:#6b7280;     /* gris medio */
      --line:#e5e7eb;      /* gris claro */
      --accent:#111827;    /* casi negro */
    }
    @page { size: A4; margin: 12mm; }
    @media print { .no-print{display:none!important;} .screen-only{display:none!important;} body{margin:0;} .sheet{box-shadow:none; border:none;} }

    body{ background:#f5f7fb; color:var(--fg); font-size:13px; }
    .sheet{
      background:#fff; max-width:850px; margin:24px auto; padding:24px;
      border:1px solid var(--line); border-radius:14px;
      box-shadow: 0 12px 30px rgba(0,0,0,.06);
    }
    .brand-row { align-items:center; }
    .brand img{ max-height:64px; width:auto; }
    .fact-box{
      border:1px solid var(--line); border-radius:12px; padding:12px 14px;
      text-align:right; font-weight:600; line-height:1.25;
    }
    .fact-box .lbl{ color:var(--muted); font-weight:500; font-size:12px; }
    .title{ font-weight:800; letter-spacing:.3px; }
    .meta .card{
      border:1px solid var(--line); border-radius:12px; padding:12px; height:100%;
    }
    .meta .label{ color:var(--muted); font-size:12px; margin-bottom:2px; }
    .meta .value{ font-weight:600; }
    .table-items{
      border:1px solid var(--line); border-radius:12px; overflow:hidden;
    }
    table{ margin:0; }
    thead th{
      background:#f9fafb; font-weight:700; border-bottom:1px solid var(--line)!important;
      padding-top:10px!important; padding-bottom:10px!important;
    }
    tbody td{ vertical-align:middle; }
    .text-end-mono{ text-align:right; font-variant-numeric: tabular-nums; }
    tfoot th, tfoot td{ border-top:1px solid var(--line)!important; }
    .totals{
      display:flex; justify-content:flex-end; margin-top:12px;
    }
    .totals .box{
      min-width:280px; border:1px solid var(--line); border-radius:12px; padding:14px 16px;
      background:#fcfcfd;
    }
    .totals .rowx{ display:flex; justify-content:space-between; margin:4px 0; }
    .totals .grand{ font-size:18px; font-weight:800; color:var(--accent); }
    .signs{ margin-top:36px; display:flex; gap:24px; }
    .signs .sig{
      flex:1; text-align:center;
    }
    .signs .line{
      border-top:1px solid var(--line); margin-top:48px; padding-top:6px; color:var(--muted);
    }
    .footer{
      margin-top:18px; color:var(--muted); font-size:11px; display:flex; justify-content:space-between;
    }
  </style>
</head>
<body>
<div class="sheet">

  <!-- Header -->
  <div class="row brand-row mb-3">
    <div class="col-8 brand">
   
      <div class="mt-2 title h5 mb-0">Factura de Compra</div>
      <div class="text-muted">#<?= htmlspecialchars($cab->cod_compra) ?></div>
    </div>
    <div class="col-4">
      <div class="fact-box">
        <div class="lbl">Timbrado</div>
        <div><?= htmlspecialchars($cab->timbrado) ?></div>
        <div class="lbl mt-2">Venc. Timbrado</div>
        <div><?= htmlspecialchars($cab->fecha_venc_timbrado) ?></div>
        <div class="lbl mt-2">Nro. Factura</div>
        <div style="font-size:18px;"><?= htmlspecialchars($cab->nro_factura) ?></div>
      </div>
    </div>
  </div>

  <!-- Meta cards -->
  <div class="row meta g-3 mb-3">
    <div class="col-md-6">
      <div class="card">
        <div class="label">Proveedor</div>
        <div class="value mb-1"><?= htmlspecialchars($cab->razon_social_prov) ?></div>
        <div class="label">RUC</div>
        <div class="value"><?= htmlspecialchars($cab->pro_ruc) ?></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="row">
          <div class="col-6">
            <div class="label">Fecha</div>
            <div class="value"><?= htmlspecialchars($cab->fecha_compra) ?></div>
          </div>
          <div class="col-6">
            <div class="label">Condición</div>
            <div class="value"><?= htmlspecialchars($cab->condicion) ?></div>
          </div>
          <div class="col-6 mt-2">
            <div class="label">Estado</div>
            <div class="value"><?= htmlspecialchars($cab->estado) ?></div>
          </div>
          <div class="col-6 mt-2">
            <div class="label">Usuario</div>
            <div class="value"><?= htmlspecialchars($cab->nombre_apellido) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Items -->
  <div class="table-items mb-2">
    <table class="table table-sm">
      <thead>
        <tr>
          <th style="width:52px;" class="text-center">#</th>
          <th class="text-start">Descripción</th>
          <th style="width:120px;" class="text-end">Cantidad</th>
          <th style="width:140px;" class="text-end">Costo</th>
          <th style="width:160px;" class="text-end">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $total = 0; $i = 1;
        if (!empty($detalles)) {
          foreach ($detalles as $fila) {
            $total += (int)$fila['subtotal'];
            echo "<tr>";
            echo "<td class='text-center'>{$i}</td>";
            echo "<td class='text-start'>".htmlspecialchars($fila['nombre_producto'])."</td>";
            echo "<td class='text-end-mono'>".fmt($fila['cantidad'])."</td>";
            echo "<td class='text-end-mono'>".fmt($fila['costo'])."</td>";
            echo "<td class='text-end-mono'>".fmt($fila['subtotal'])."</td>";
            echo "</tr>";
            $i++;
          }
        } else {
          echo "<tr><td colspan='5' class='text-center text-muted py-4'>Sin ítems en esta compra.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Totals -->
  <div class="totals">
    <div class="box">
      <div class="rowx"><div>Total ítems</div><div class="text-end-mono"><?= fmt($i-1) ?></div></div>
      <div class="rowx grand"><div>Total General</div><div class="text-end-mono"><?= fmt($total) ?></div></div>
    </div>
  </div>

  <!-- Signatures -->
  <div class="signs">
    <div class="sig">
      <div class="line">Recibí Conforme</div>
    </div>
    <div class="sig">
      <div class="line">Autorizado</div>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer">
    <div>Impreso el <?= $impresoEn ?></div>
    <div class="screen-only">
      <button class="btn btn-dark btn-sm" onclick="window.print()">Imprimir</button>
    </div>
  </div>

</div>
</body>
</html>
