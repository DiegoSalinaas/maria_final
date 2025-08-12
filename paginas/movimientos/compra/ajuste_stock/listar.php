<div class="container-fluid card" style="padding: 30px;">
<div class="row">
    <div class="col-md-10">
        <h3>Lista Ajustes</h3>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary " onclick="mostrarAgregarAjusteStock(); return false;"><i class="fa fa-plus"></i> Agregar</button>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>
    <div class="col-md-12">
        <label for="b_cliente">Busqueda</label>
        <input type="text" class="form-control" id="b_cliente2">
    </div>
    <div class="col-md-3" style="margin-top: 30px;">
        <button class="btn btn-primary" onclick="imprimirCliente(); return false;"><i class="fa fa-print"></i> Imprimir</button>
    </div>
    <div class="col-md-12" style="margin-top: 30px;">
        <table class="table table-bordered table-striped  table-head-bg-primary mt-4">
            <thead>
                <tr>
                   <th>#</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Operaciones</th>
                </tr>
            </thead>
            <tbody id="ajuste_compra"></tbody>
        </table>
    </div>
    
</div>
</div>