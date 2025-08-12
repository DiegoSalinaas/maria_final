<div class="container-fluid px-0">
  <?php session_start(); ?>
  <div class="card shadow rounded-4 overflow-hidden">

    <!-- Header -->
    <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-2">
        <i class="typcn typcn-shopping-cart mr-2" style="font-size: 22px;"></i>
        <div>
          <h4 class="mb-0">Orden de Compra</h4>
          <small class="opacity-75">Alta / Edición</small>
        </div>
      </div>
      <span class="badge badge-light text-primary px-3 py-2 rounded-pill">
        Código: <span id="cod_badge">—</span>
      </span>
    </div>

    <div class="card-body">
      <input type="hidden" id="id_cliente" value="0">

      <!-- Datos Principales -->
      <div class="row g-3">
        <div class="col-12">
          <div class="alert alert-light border d-flex align-items-center mb-2" role="alert" style="gap:.75rem">
            <i class="typcn typcn-info-large-outline"></i>
            <div>Completá los datos principales de la orden.</div>
          </div>
        </div>

        <div class="col-sm-2 col-md-2">
          <label class="form-label mb-1">Código</label>
          <input type="text" id="cod" class="form-control text-center font-weight-bold" readonly>
        </div>

        <div class="col-sm-5 col-md-5">
          <label class="form-label mb-1">Usuario</label>
          <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text"><i class="typcn typcn-user"></i></span></div>
            <input type="text" id="usuario_lst" class="form-control" value="<?= $_SESSION['usuario_alias']?>" readonly>
          </div>
        </div>

        <div class="col-sm-5 col-md-5">
          <label class="form-label mb-1">Fecha</label>
          <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text"><i class="typcn typcn-calendar-outline"></i></span></div>
            <input type="date" id="fecha" class="form-control">
          </div>
        </div>

        <div class="col-12">
          <label class="form-label mt-2 mb-1">Proveedor</label>
          <select id="proveedor_compra_lst" class="form-control">
            <!-- opciones dinámicas -->
          </select>
          <small class="text-muted">Seleccioná el proveedor para esta orden.</small>
        </div>
      </div>

      <hr class="my-4">

      <!-- Detalle: Alta de ítems -->
      <div class="d-flex align-items-center justify-content-between mb-2">
        <h5 class="mb-0">Detalle de la orden</h5>
        <span class="badge badge-secondary" id="contador_items">0 ítems</span>
      </div>

      <div class="row g-2 align-items-end">
        <div class="col-md-6">
          <label class="form-label mb-1">Producto</label>
          <select id="producto_lst" class="form-control">
            <!-- opciones dinámicas -->
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label mb-1">Cantidad</label>
          <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text"><i class="typcn typcn-tabs-outline"></i></span></div>
            <input type="text" id="cantidad_txt" value="1" class="form-control formatear-numero text-right">
          </div>
        </div>

        <div class="col-md-2">
          <label class="form-label mb-1">Precio</label>
          <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text">Gs.</span></div>
            <input type="text" id="precio_txt" value="1" class="form-control formatear-numero text-right">
          </div>
        </div>

        <div class="col-md-2">
          <label class="form-label mb-1 d-block">Operaciones</label>
          <div class="btn-group w-100">
            <button class="btn btn-primary" onclick="agregarTablaOrdenCompra(); return false;">
              <i class="typcn typcn-plus"></i> Agregar
            </button>
            <button class="btn btn-outline-secondary" onclick="limpiarLineaOC && limpiarLineaOC(); return false;">
              <i class="typcn typcn-delete-outline"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Tabla de ítems -->
      <div class="table-responsive mt-3">
        <table class="table table-sm table-hover table-bordered align-middle">
          <thead class="thead-light">
            <tr class="text-center">
              <th style="width:60px">#</th>
              <th>Producto</th>
              <th style="width:140px">Precio (Gs.)</th>
              <th style="width:120px">Cantidad</th>
              <th style="width:160px">Total (Gs.)</th>
              <th style="width:60px"></th>
            </tr>
          </thead>
          <tbody id="orden_compra_compra">
            <!-- filas dinámicas -->
          </tbody>
          <tfoot>
            <tr>
              <th colspan="4" class="text-right">TOTAL</th>
              <th id="total" class="text-right h5 mb-0">0</th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Resumen -->
<!--      <div class="row mt-3">
        <div class="col-md-4">
          <div class="border rounded p-3 bg-light">
            <div class="d-flex justify-content-between">
              <span class="text-muted">Ítems</span>
              <strong id="resumen_items">0</strong>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <span class="text-muted">Total Gs.</span>
              <strong id="resumen_total">0</strong>
            </div>
          </div>
        </div>
      </div>-->

      <hr class="my-4">

      <!-- Acciones -->
      <div class="d-flex flex-wrap gap-2 justify-content-end">
        <button class="btn btn-danger" onclick="cancelarOrdenCompra(); return false;">
          <i class="typcn typcn-times"></i> Cancelar
        </button>
        <button class="btn btn-success" onclick="guardarOrdenCompra(); return false;">
          <i class="typcn typcn-device-floppy"></i> Guardar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Estilitos opcionales para mejor spacing -->
<style>
  .card-header .badge { background: #fff; }
  .table tfoot th { background: #f8f9fa; }
  .gap-2 { gap: .5rem; }
  .rounded-4 { border-radius: 1rem !important; }
</style>

<script>
  // Si ya tenés un código en #cod, también lo reflejamos en el badge del header
  (function syncBadge() {
    const $cod = document.getElementById('cod');
    const $badge = document.getElementById('cod_badge');
    if ($cod && $badge) {
      const upd = () => { $badge.textContent = $cod.value || '—'; };
      upd();
      $cod.addEventListener('input', upd);
      $cod.addEventListener('change', upd);
    }
  })();

  // Callback opcional para limpiar los inputs de detalle
  function limpiarLineaOC(){
    const p = document.getElementById('producto_lst');
    const c = document.getElementById('cantidad_txt');
    const pr= document.getElementById('precio_txt');
    if (p) p.value = '';
    if (c) c.value = '1';
    if (pr) pr.value = '1';
  }
</script>
