<div class="container-fluid px-0">
  <div class="card shadow rounded-4 overflow-hidden">

    <!-- Header -->
    <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <i class="typcn typcn-shopping-cart mr-2" style="font-size:22px;"></i>
        <div>
          <h4 class="mb-0">Facturas de Venta</h4>
          <small class="opacity-75">Listado y acciones</small>
        </div>
      </div>
      <button class="btn btn-light text-primary" onclick="mostrarAgregarFactura(); return false;">
        <i class="typcn typcn-plus"></i> Nueva Factura
      </button>
    </div>

    <div class="card-body">

      <!-- Filtros -->
      <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
          <label for="b_factura" class="form-label mb-1">Búsqueda</label>
          <div class="input-group">
            <span class="input-group-text"><i class="typcn typcn-zoom"></i></span>
            <input type="text" id="b_factura" class="form-control" placeholder="Cliente, Nro, 001-001-0000001...">
            <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('b_factura').value='';">
              <i class="typcn typcn-delete-outline"></i>
            </button>
          </div>
          <small class="text-muted">Tip: escribí y presioná Enter para filtrar.</small>
        </div>

        <div class="col-md-4">
          <label for="estado_lst" class="form-label mb-1">Estado</label>
          <select id="estado_lst" class="form-control">
            <option value="">Todos</option>
            <option value="ACTIVO">Activo</option>
            <option value="ANULADO">Anulado</option>
          </select>
          <small class="text-muted">Filtro opcional por estado</small>
        </div>
      </div>

<!--      <div class="row g-2 align-items-end mb-3">
        <div class="col-md-6">
          <label for="fecha_desde_fv" class="form-label mb-1">Desde</label>
          <input type="date" id="fecha_desde_fv" class="form-control">
        </div>
        <div class="col-md-6">
          <label for="fecha_hasta_fv" class="form-label mb-1">Hasta</label>
          <input type="date" id="fecha_hasta_fv" class="form-control">
        </div>
      </div>-->

      <!-- Tabla -->
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0">
          <thead class="thead-light">
            <tr class="text-center">
              <th style="width:60px">#</th>
              <th style="width:120px">Fecha</th>
              <th style="width:150px">Número</th>
              <th>Cliente</th>
              <th style="width:150px">Total (Gs.)</th>
              <th style="width:130px">Estado</th>
              <th style="width:200px">Operaciones</th>
            </tr>
          </thead>
          <tbody id="tabla_facturas">
            <!-- filas dinámicas -->
          </tbody>
        </table>
      </div>

      <!-- Empty state opcional -->
      <div id="fv_empty_state" class="text-center text-muted py-4 d-none">
        <i class="typcn typcn-info-large-outline d-block mb-2" style="font-size:28px;"></i>
        No se encontraron facturas con los filtros actuales.
      </div>

    </div>
  </div>
</div>

<style>
  .rounded-4 { border-radius: 1rem !important; }
  .table thead th { background:#f8f9fa; }
</style>
