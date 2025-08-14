<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-primary fw-bold">
      <i class="bi bi-receipt-cutoff me-2"></i> Facturas de Venta
    </h3>
    <button class="btn btn-success d-flex align-items-center shadow-sm" onclick="mostrarAgregarFactura(); return false;">
      <i class="bi bi-plus-circle me-2"></i> Nueva Factura
    </button>
  </div>

  <div class="card shadow rounded-4 border-0">
    <div class="card-body">
      <div class="row g-3 align-items-end mb-3">
        <div class="col-md-4">
          <label for="b_factura" class="form-label fw-semibold">Buscar</label>
          <input type="text" id="b_factura" class="form-control form-control-lg" placeholder="Cliente, Nro, 001-001-0000001...">
        </div>
        <div class="col-md-2">
          <label for="desde_txt" class="form-label fw-semibold">Desde</label>
          <input type="date" id="desde_txt" class="form-control form-control-lg">
        </div>
        <div class="col-md-2">
          <label for="hasta_txt" class="form-label fw-semibold">Hasta</label>
          <input type="date" id="hasta_txt" class="form-control form-control-lg">
        </div>
        <div class="col-md-2">
          <label for="estado_lst" class="form-label fw-semibold">Estado</label>
          <select id="estado_lst" class="form-select form-select-lg">
            <option value="">Todos</option>
            <option value="ACTIVO">ACTIVO</option>
            <option value="ANULADO">ANULADO</option>
          </select>
        </div>
        <div class="col-md-2 d-grid">
          <button class="btn btn-primary btn-lg" id="btn_buscar">
            <i class="bi bi-search"></i> Buscar
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th>#</th>
              <th>Fecha</th>
              <th>Número</th>
              <th>Cliente</th>
              <th>Total</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tabla_facturas"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>