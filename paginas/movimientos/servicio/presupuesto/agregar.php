<div class="container-fluid card" style="padding: 30px;">
<?php session_start(); ?>
<div class="row">
    <input type="text" id="editar" value="NO" hidden>
    <div class="col-md-12">
        <h3>Presupuesto de Servicio</h3>
    </div>
    <div class="col-md-12"><hr></div>
    <div class="col-md-2">
        <label>Código</label>
        <input type="text" id="id_presupuesto" class="form-control" readonly>
    </div>
    <div class="col-md-3">
        <label>Fecha Emisión</label>
        <input type="date" id="fecha_emision" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Fecha Vencimiento</label>
        <input type="date" id="fecha_vencimiento" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Cliente</label>
        <select id="cliente_lst" class="form-control"></select>
    </div>
    <div class="col-md-12">
        <label>Observaciones</label>
        <textarea id="observaciones" class="form-control"></textarea>
    </div>
    <div class="col-md-12"><hr></div>
    <div class="col-md-12"><h4>Servicios</h4></div>
    <div class="col-md-3"><input type="text" id="tipo_servicio" class="form-control" placeholder="Tipo servicio"></div>
    <div class="col-md-3"><input type="text" id="desc_servicio" class="form-control" placeholder="Descripción"></div>
    <div class="col-md-1"><input type="number" id="cant_servicio" class="form-control" value="1" min="1"></div>
    <div class="col-md-2"><input type="number" id="precio_servicio" class="form-control" placeholder="Precio"></div>
    <div class="col-md-1"><input type="number" id="descuento_servicio" class="form-control" placeholder="Desc"></div>
    <div class="col-md-2"><button class="btn btn-primary form-control" onclick="agregarServicio(); return false;">Agregar</button></div>
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Descuento</th>
                    <th>Total</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody id="detalle_servicios"></tbody>
        </table>
    </div>
    <div class="col-md-12"><hr></div>
    <div class="col-md-12"><h4>Insumos</h4></div>
    <div class="col-md-3"><input type="text" id="desc_insumo" class="form-control" placeholder="Descripción"></div>
    <div class="col-md-2"><input type="text" id="marca_insumo" class="form-control" placeholder="Marca"></div>
    <div class="col-md-2"><input type="text" id="modelo_insumo" class="form-control" placeholder="Modelo"></div>
    <div class="col-md-1"><input type="number" id="cant_insumo" class="form-control" value="1" min="1"></div>
    <div class="col-md-2"><input type="number" id="precio_insumo" class="form-control" placeholder="Precio"></div>
    <div class="col-md-2"><button class="btn btn-primary form-control" onclick="agregarInsumo(); return false;">Agregar</button></div>
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Total</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody id="detalle_insumos"></tbody>
        </table>
    </div>
    <div class="col-md-12"><hr></div>
    <div class="col-md-3">
        <button class="form-control btn btn-success" onclick="guardarPresupuestoServicio(); return false;"><i class="fa fa-save"></i> Guardar</button>
    </div>
    <div class="col-md-3">
        <button class="form-control btn btn-danger" onclick="mostrarListarPresupuestoServicio(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
    </div>
</div>
</div>
