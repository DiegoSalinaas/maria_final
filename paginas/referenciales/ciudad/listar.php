<div class="container-fluid card" style="padding: 30px;">
<div class="row">
    <div class="col-md-10">
        <h3>Lista de ciudades</h3>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary " onclick="mostrarAgregarCiudad(); return false;"><i class="fa fa-plus"></i> Agregar</button>
    </div>
    <div class="col-md-12">
        <hr>
    </div>
    <div class="col-md-12" style="margin-top: 30px;">
        <table class="table table-bordered table-striped  table-head-bg-primary mt-4">
            <thead>
                <tr>
                   <th>#</th>
                    <th>Ciudad</th>
                    <th>Estado</th>
                    <th>Operaciones</th>
                </tr>
            </thead>
            <tbody id="ciudad_tb"></tbody>
        </table>
    </div>
</div>
</div>
