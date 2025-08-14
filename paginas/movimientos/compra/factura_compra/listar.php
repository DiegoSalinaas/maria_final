<div class="container-fluid px-0">
  <div class="card shadow rounded-4 overflow-hidden">

    <!-- Header -->
    <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <i class="typcn typcn-document-text mr-2" style="font-size:22px;"></i>
        <div>
          <h4 class="mb-0">Facturas de Compras</h4>
          <small class="opacity-75">Listado y acciones</small>
        </div>
      </div>
      <button class="btn btn-light text-primary" onclick="mostrarAgregarFacturaCompra(); return false;">
        <i class="typcn typcn-plus"></i> Agregar
      </button>
    </div>

    <div class="card-body">

      <!-- Filtros -->
      <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
          <label for="b_cliente2" class="form-label mb-1">Búsqueda</label>
          <div class="input-group">
            <span class="input-group-text"><i class="typcn typcn-zoom"></i></span>
            <input type="text" class="form-control" id="b_cliente2" placeholder="Buscar por proveedor, número o fecha…">
            <button class="btn btn-outline-secondary" id="btn_limpiar_fc" type="button">
              <i class="typcn typcn-delete-outline"></i>
            </button>
          </div>
          <small class="text-muted">Tip: escribí y presioná Enter para filtrar.</small>
        </div>

        <div class="col-md-4">
          <label class="form-label mb-1" for="estado_lst_fc">Estado</label>
          <select id="estado_lst_fc" class="form-control">
            <option value="">Todos</option>
            <option value="ACTIVO">Activo</option>
            <option value="ANULADO">Anulado</option>
          </select>
          <small class="text-muted">Filtro opcional por estado</small>
        </div>
      </div>

      <div class="row g-2 align-items-end mb-3">
        <div class="col-md-6">
          <label class="form-label mb-1" for="fecha_desde_fc">Desde</label>
          <input type="date" id="fecha_desde_fc" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label mb-1" for="fecha_hasta_fc">Hasta</label>
          <input type="date" id="fecha_hasta_fc" class="form-control">
        </div>
      </div>

      <!-- Tabla -->
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0">
          <thead class="thead-light">
            <tr class="text-center">
              <th style="width:60px">#</th>
              <th style="width:120px">Fecha</th>
              <th style="width:150px">Nro Factura</th>
              <th>Proveedor</th>
              <th style="width:120px">Condición</th>
              <th style="width:150px">Total (Gs.)</th>
              <th style="width:130px">Estado</th>
              <th style="width:200px">Operaciones</th>
            </tr>
          </thead>
          <tbody id="factura_compra">
            <!-- filas dinámicas -->
          </tbody>
        </table>
      </div>

      <!-- Empty state opcional -->
      <div id="fc_empty_state" class="text-center text-muted py-4 d-none">
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
