 <div class="container mt-4">
  <input type="hidden" id="id_factura" value="0">

  <div class="card shadow rounded-4 overflow-hidden">
    <!-- Header -->
    <div class="card-header bg-primary text-white rounded-top-4 py-3">
      <div class="d-flex align-items-center justify-content-between">
        <h4 class="mb-0 d-flex align-items-center">
          <i class="typcn typcn-ticket mr-2"></i> Agregar / Editar Factura
        </h4>
        <div class="text-right small">
          <div><span class="opacity-75">Serie:</span> <strong id="serie_info">—</strong></div>
          <div><span class="opacity-75">Próximo Nº:</span> <strong id="numero_info">—</strong></div>
        </div>
      </div>
    </div>

    <!-- Body -->
    <div class="card-body">
      <!-- CABECERA -->
      <div class="mb-3">
        <div class="d-flex align-items-center mb-2">
          <i class="typcn typcn-document-text mr-2 text-primary"></i>
          <h5 class="mb-0">Datos de la factura</h5>
        </div>

        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Fecha</label>
            <div class="input-group">
              <div class="input-group-prepend"><span class="input-group-text"><i class="typcn typcn-calendar"></i></span></div>
              <input type="date" id="fecha_txt" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
          </div>

          <div class="col-md-5">
            <label class="form-label">Cliente</label>
            <div class="input-group">
              <div class="input-group-prepend"><span class="input-group-text"><i class="typcn typcn-user"></i></span></div>
              <select id="id_cliente_lst" class="form-select"></select>
            </div>
          </div>

          <div class="col-md-2">
            <label class="form-label">Condición</label>
            <select id="condicion_lst" class="form-select">
              <option value="CONTADO">CONTADO</option>
              <option value="CREDITO">CREDITO</option>
            </select>
          </div>

<!--          <div class="col-md-2">
            <label class="form-label">Moneda</label>
            <input type="text" id="moneda_txt" class="form-control" value="PYG">
          </div>
        </div>
      </div>-->

      <hr class="my-4"/>

      <!-- SERIE / TIMBRADO -->
      <div class="mb-3">
        <div class="d-flex align-items-center mb-2">
          <i class="typcn typcn-tags mr-2 text-primary"></i>
          <h5 class="mb-0">Serie y timbrado</h5>
        </div>

        <div class="row g-3">
          <div class="col-md-2">
            <label class="form-label">Timbrado</label>
            <input type="text" id="timbrado_txt" class="form-control" maxlength="8" placeholder="00000000" disabled>
          </div>
          <div class="col-md-2">
            <label class="form-label">Vig. desde</label>
            <input type="date" id="vig_desde_txt" class="form-control" disabled>
          </div>
          <div class="col-md-2">
            <label class="form-label">Vig. hasta</label>
            <input type="date" id="vig_hasta_txt" class="form-control" disabled>
          </div>
          <div class="col-md-2">
            <label class="form-label">Est.</label>
            <input type="text" id="est_txt" class="form-control text-center" maxlength="3" placeholder="001" disabled>
          </div>
          <div class="col-md-2">
            <label class="form-label">Pto. Exp.</label>
            <input type="text" id="pto_txt" class="form-control text-center" maxlength="3" placeholder="001" disabled>
          </div>
          <div class="col-md-2">
            <label class="form-label">Número</label>
            <input type="text" id="num_txt" class="form-control text-center" maxlength="7" placeholder="0000001" disabled>
          </div>

          <div class="col-12">
            <label class="form-label">Observación</label>
            <input type="text" id="obs_txt" class="form-control" placeholder="Notas adicionales (opcional)">
          </div>
        </div>
      </div>

      <hr class="my-4"/>

      <!-- DETALLE -->
      <div>
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="d-flex align-items-center">
            <i class="typcn typcn-clipboard mr-2 text-primary"></i>
            <h5 class="mb-0">Detalle</h5>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="limpiarDetalleUI(); return false;">
              <i class="typcn typcn-refresh"></i> Limpiar línea
            </button>
          </div>
        </div>

        <div class="row g-2 align-items-end">
          <div class="col-md-6">
            <label class="form-label">Producto</label>
            <select id="id_producto_lst" class="form-select"></select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Cantidad</label>
            <input type="number" id="cant_txt" class="form-control text-right" min="1" value="1">
          </div>
          <div class="col-md-2">
            <label class="form-label">Precio</label>
            <input type="number" id="precio_txt" class="form-control text-right" min="1" value="0">
          </div>
          <div class="col-md-2">
            <label class="form-label">Descuento</label>
            <input type="number" id="desc_txt" class="form-control text-right" min="0" value="0">
          </div>
          <div class="col-12 col-md-12 mt-2">
            <button class="btn btn-primary btn-block" onclick="agregarDetalle(); return false;">
              <i class="typcn typcn-plus"></i> Agregar ítem
            </button>
          </div>
        </div>

        <div class="table-responsive mt-3">
          <table class="table table-hover align-middle text-center mb-0">
            <thead class="thead-light">
              <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Desc.</th>
<!--                <th>IVA</th>-->
                <th>Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="tabla_detalle"></tbody>
          </table>
        </div>

        <!-- Totales -->
        <div class="row g-3 mt-3">
<!--          <div class="col-md-3">
            <div class="border rounded p-2 d-flex justify-content-between">
              <span class="text-muted">Exentas</span>
              <strong id="tot_exenta">0</strong>
            </div>
          </div>
          <div class="col-md-3">
            <div class="border rounded p-2 d-flex justify-content-between">
              <span class="text-muted">Grav. 5%</span>
              <strong id="tot_5">0</strong>
            </div>
          </div>
          <div class="col-md-3">
            <div class="border rounded p-2 d-flex justify-content-between">
              <span class="text-muted">Grav. 10%</span>
              <strong id="tot_10">0</strong>
            </div>
          </div>-->
          <div class="col-md-3">
            <div class="bg-light rounded p-2 d-flex justify-content-between">
              <span class="font-weight-bold">TOTAL</span>
              <strong id="tot_general" class="h5 mb-0">0</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="d-flex justify-content-end gap-2 mt-4">
        <button class="btn btn-light border" onclick="mostrarListarFacturas(); return false;">
          <i class="typcn typcn-arrow-left"></i> Cancelar
        </button>
        <button class="btn btn-success" onclick="guardarFacturaCompleta(); return false;">
          <i class="typcn typcn-tick"></i> Guardar
        </button>
      </div>
    </div>
  </div>
</div>
