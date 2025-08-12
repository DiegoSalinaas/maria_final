<div class="row">
    
    <input type="text" hidden id="id_modelo" value="0">
    <div class="col-md-6">
        <label>Modelo</label>
        <input type="text" class="form-control" id="modelo">
    </div>
    <div class="col-md-6">
        <label>Marca</label>
        <select  id="marca_lst" class="form-control"></select>
    </div>
    <div class="col-md-6" style="margin-top: 20px;">
        <button class="btn btn-primary" onclick="guardarModelo(); return false;"><i class="fa fa-save" > </i> Guardar</button>
        <button class="btn btn-success"><i class="fa fa-print"></i> Imprimir</button>
    </div>
    
</div>
<div class="row" style="margin-top: 40px;">
    <div class="col-md-12">
        <label>Busqueda</label>
        <input type="text" id="b_modelos" class="form-control" placeholder="Escribe unas palabras para buscar">
    </div>
    <div class="col-md-12" style="margin-top: 50px;">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>MODELO</th>
                    <th>MARCA</th>
                    <th>ESTADO</th>
                    <th>OPERACIONES</th>
                </tr>
            </thead>
            <tbody id="modelo_tb"> </tbody>
        </table>
    </div>
</div>