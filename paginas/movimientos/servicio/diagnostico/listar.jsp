

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
        <button class="btn btn-primary form-control " onclick="buscarDiagnosticoPeriodo(); return false;">Buscar</button>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>

    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>FECHA DE EMISION</th>
                    <th>CLIENTE</th>
                    <th>EQUIPO</th>
                    <th>OBSERVACION</th>
                    <th>ESTADO</th>
                    <th>OPERACIONES</th>
                </tr>
            </thead>
            <tbody id="diagnostico_listado_tb"></tbody>
        </table> 
    </div>
</div>