<?php session_start(); ?>
<div class="container-fluid card" style="padding: 30px;">
  <div class="row">
    <input type="text" id="editar" value="NO" hidden>

    <div class="col-md-12">
      <h3>Registro de Servicios</h3>
    </div>

    <div class="col-md-12"><hr></div>

    <div class="col-md-2">
      <label for="id_servicio">Código</label>
      <input type="text" id="id_servicio" class="form-control" readonly>
    </div>

    <div class="col-md-3">
      <label for="fecha_servicio">Fecha</label>
      <input type="date" id="fecha_servicio" class="form-control">
    </div>

    <div class="col-md-3">
      <label for="cliente_lst">Cliente</label>
      <select id="cliente_lst" class="form-control">
        <option value="">-- Seleccione --</option>
      </select>
    </div>

    <div class="col-md-2">
      <label for="ci_cliente">CI Cliente</label>
      <input type="text" id="ci_cliente" class="form-control" readonly>
    </div>

    <div class="col-md-2">
      <label for="telefono_cliente">Teléfono</label>
      <input type="text" id="telefono_cliente" class="form-control" readonly>
    </div>

    <div class="col-md-3 mt-3">
      <label for="tecnico">Técnico Asignado</label>
      <input type="text" id="tecnico" class="form-control">
    </div>

    <div class="col-md-3 mt-3">
      <label for="estado_servicio">Estado del Servicio</label>
      <select id="estado_servicio" class="form-control">
        <option value="Pendiente">Pendiente</option>
        <option value="En Proceso">En Proceso</option>
        <option value="Completado">Completado</option>
        <option value="Cancelado">Cancelado</option>
      </select>
    </div>

    <div class="col-md-12 mt-3">
      <label for="observaciones">Observaciones Generales</label>
      <textarea id="observaciones" class="form-control" rows="2"></textarea>
    </div>

    <div class="col-md-12"><hr></div>

    <div class="col-md-12">
      <h4>Detalle de Servicios</h4>
    </div>

    <div class="col-md-3 mt-2">
      <input type="text" id="tipo_servicio" class="form-control" placeholder="Tipo de Servicio">
    </div>

    <div class="col-md-3 mt-2">
      <input type="text" id="desc_servicio" class="form-control" placeholder="Descripción">
    </div>

    <div class="col-md-2 mt-2">
      <input type="text" id="producto_rel_txt" class="form-control" placeholder="Producto">
    </div>

    <div class="col-md-1 mt-2">
      <input type="number" id="cant_servicio" class="form-control" value="1" min="1" step="1" placeholder="Cant.">
    </div>

    <div class="col-md-2 mt-2">
      <input type="number" id="precio_servicio" class="form-control" placeholder="Precio" step="0.01" min="0">
    </div>

    <div class="col-md-1 mt-2">
      <input type="text" id="obs_detalle" class="form-control" placeholder="Obs.">
    </div>

    <div class="col-md-12" style="margin-top:10px;">
      <button type="button" class="btn btn-primary" onclick="agregarDetalle(); return false;">
        Agregar
      </button>
    </div>

    <div class="col-md-12" style="margin-top:10px;">
      <div class="table-responsive">
        <table class="table table-bordered table-sm">
          <thead class="table-light">
            <tr>
              <th>Tipo</th>
              <th>Descripción</th>
              <th>Producto</th>
              <th class="text-end">Cantidad</th>
              <th class="text-end">Precio Unitario</th>
              <th class="text-end">Subtotal</th>
              <th>Observaciones</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody id="detalle_servicio"></tbody>
        </table>
      </div>
    </div>

    <div class="col-md-12"><hr></div>

    <div class="col-md-3 mb-3">
      <button class="form-control btn btn-success" onclick="guardarServicio(); return false;">
        <i class="fa fa-save"></i> Guardar
      </button>
    </div>

    <div class="col-md-3 mb-3">
      <button class="form-control btn btn-danger" onclick="mostrarListarServicio(); return false;">
        <i class="fa fa-ban"></i> Cancelar
      </button>
    </div>
  </div>
</div>
