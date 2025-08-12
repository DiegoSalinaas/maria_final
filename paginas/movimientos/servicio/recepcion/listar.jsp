

<hr> 
<div class="row" id="tabla-busqueda">
    <div class="col-md-12">
        <label >Cliente</label>
        <select  id="cliente_b_lst" class="form-control"></select>
    </div>
    <div class="col-md-3">
        <label >Desde</label>
        <input type="date" id="desde" class="form-control">
    </div>
    <div class="col-md-3">
        <label >Hasta</label>
        <input type="date" id="hasta" class="form-control">
    </div>
    <div class="col-md-3">
        <label >Operacion</label>
        <button class="btn btn-primary form-control " onclick="buscarRecepcionPeriodo(); return false;">Buscar</button>
    </div>
    <div class="col-md-3">
        <label >Nro factura</label>
        <input type="text" id="nro_venta_buscar" class="form-control">
    </div>
    <div class="col-md-12">
        <hr> 
    </div>
    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha Vencimiento</th>
                    <th>Cliente</th>
                    <th>Descripcion</th>
                    <th>Estado</th>
                    <th>Operaciones</th>
                </tr>
            </thead>
            <tbody id="recepcion_listado_tb"></tbody>
        </table> 
    </div>
</div>

