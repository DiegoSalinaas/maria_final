<div class="container-fluid card" style="padding: 30px;">
<?php session_start(); ?>
<div class="row">
    <input type="text" id="editar" value="NO" hidden>
    <div class="col-md-12">
        <h3>Servicio Técnico</h3>
    </div>
    <div class="col-md-12"><hr></div>
    <div class="col-md-2">
        <label>Código</label>
        <input type="text" id="id_servicio" class="form-control" readonly>
    </div>
    <div class="col-md-3">
        <label>Fecha</label>
        <input type="date" id="fecha_servicio" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Cliente</label>
        <select id="cliente_lst" class="form-control"></select>
    </div>
    <div class="col-md-4">
        <label>Equipo</label>
        <select id="equipo_lst" class="form-control"></select>
    </div>
    <div class="col-md-3">
        <label>Estado</label>
        <select id="estado_servicio" class="form-control">
            <option value="Pendiente">Pendiente</option>
            <option value="En proceso">En proceso</option>
            <option value="Terminado">Terminado</option>
        </select>
    </div>
    <div class="col-md-3">
        <label>Técnico</label>
        <input type="text" id="tecnico" class="form-control">
    </div>
    <div class="col-md-12">
        <label>Observaciones</label>
        <textarea id="observaciones" class="form-control"></textarea>
    </div>
    <div class="col-md-12"><hr></div>
    <div class="col-md-12"><h4>Detalles</h4></div>
    <div class="col-md-4"><input type="text" id="desc_detalle" class="form-control" placeholder="Descripción"></div>
    <div class="col-md-2"><input type="number" id="costo_detalle" class="form-control" placeholder="Costo"></div>
    <div class="col-md-2">
        <select id="estado_detalle" class="form-control">
            <option value="Pendiente">Pendiente</option>
            <option value="Realizado">Realizado</option>
        </select>
    </div>
    <div class="col-md-2"><input type="date" id="fecha_detalle" class="form-control"></div>
    <div class="col-md-2"><button class="btn btn-primary form-control" onclick="agregarDetalle(); return false;">Agregar</button></div>
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Costo</th>
                    <th>Estado</th>
                    <th>Fecha realizada</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody id="detalle_servicio"></tbody>
        </table>
    </div>
    <div class="col-md-12"><hr></div>
    <div class="col-md-3">
        <button class="form-control btn btn-success" onclick="guardarServicio(); return false;"><i class="fa fa-save"></i> Guardar</button>
    </div>
    <div class="col-md-3">
        <button class="form-control btn btn-danger" onclick="mostrarListarServicio(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
    </div>
</div>
</div>
