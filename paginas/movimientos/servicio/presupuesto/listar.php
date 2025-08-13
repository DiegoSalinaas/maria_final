<div class="container-fluid card" style="padding: 30px;">
<div class="row">
    <div class="col-md-10">
        <h3>Lista de Presupuestos de Servicio</h3>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary" onclick="mostrarAgregarPresupuestoServicio(); return false;"><i class="fa fa-plus"></i> Agregar</button>
    </div>
    <div class="col-md-12"><hr></div>
    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Operaciones</th>
                </tr>
            </thead>
            <tbody id="presupuesto_servicio"></tbody>
        </table>
    </div>
</div>
</div>
