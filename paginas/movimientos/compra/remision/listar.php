<div class="container-fluid px-0">
  <div class="card shadow rounded-4 overflow-hidden">

    <!-- Header -->
    <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <i class="typcn typcn-truck mr-2" style="font-size:22px;"></i>
        <div>
          <h4 class="mb-0">Remisiones</h4>
          <small class="opacity-75">Listado y acciones</small>
        </div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-light text-primary" onclick="mostrarAgregarRemision(); return false;">
          <i class="typcn typcn-plus"></i> Agregar
        </button>
        <button class="btn btn-light text-primary" onclick="imprimirCliente(); return false;">
          <i class="typcn typcn-printer"></i> Imprimir
        </button>
      </div>
    </div>

    <div class="card-body">

      <!-- Filtros -->
      <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
          <label for="b_cliente2" class="form-label mb-1">Búsqueda</label>
          <div class="input-group">
            <span class="input-group-text"><i class="typcn typcn-zoom"></i></span>
            <input type="text" class="form-control" id="b_cliente2" placeholder="Buscar por proveedor, número o fecha…">
            <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('b_cliente2').value='';">
              <i class="typcn typcn-delete-outline"></i>
            </button>
          </div>
          <small class="text-muted">Tip: escribí y presioná Enter para filtrar.</small>
        </div>

        <div class="col-md-4">
          <label class="form-label mb-1" for="estado_lst_rem">Estado</label>
          <select id="estado_lst_rem" class="form-control">
            <option value="">Todos</option>
            <option value="PENDIENTE">Pendiente</option>
            <option value="ENTREGADO">Entregado</option>
            <option value="ANULADO">Anulado</option>
          </select>
          <small class="text-muted">Filtro opcional por estado</small>
        </div>
      </div>

      <div class="row g-2 align-items-end mb-3">
        <div class="col-md-6">
          <label class="form-label mb-1" for="fecha_desde_rem">Desde</label>
          <input type="date" id="fecha_desde_rem" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label mb-1" for="fecha_hasta_rem">Hasta</label>
          <input type="date" id="fecha_hasta_rem" class="form-control">
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
              <th>Chofer</th>
              <th style="width:120px">Salida</th>
              <th style="width:120px">Llegada</th>
              <th style="width:130px">Estado</th>
              <th style="width:200px">Operaciones</th>
            </tr>
          </thead>
          <tbody id="remision_compra">
            <!-- filas dinámicas -->
          </tbody>
        </table>
      </div>

      <!-- Empty state opcional -->
      <div id="rem_empty_state" class="text-center text-muted py-4 d-none">
        <i class="typcn typcn-info-large-outline d-block mb-2" style="font-size:28px;"></i>
        No se encontraron remisiones con los filtros actuales.
      </div>

    </div>
  </div>
</div>

<style>
  .rounded-4 { border-radius: 1rem !important; }
  .table thead th { background:#f8f9fa; }
</style>
