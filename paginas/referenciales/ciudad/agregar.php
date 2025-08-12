<div class="container-fluid card" style="padding: 30px; height: auto;" >
    <div class="row">
        <input type="text" value="0" id="id_ciudad" hidden>
        <div class="col-md-12">
            <h3>Agregar ciudad</h3>
        </div>
        <div class="col-md-12">
            <hr>
        </div>
        <div class="col-md-1">
            <label for="">Codigo</label>
            <input type="text" id="cod" class="form-control" readonly>
        </div>
        <div class="col-md-5">
            <label for="">Ciudad</label>
            <input type="text" id="nombre_ciud" class="form-control">
        </div>
        <div class="col-md-5">
            <label for="">Estado</label>
            <select id="estado_ciud" class="form-control">
                <option value="ACTIVO">ACTIVO</option>
                <option value="INACTIVO">INACTIVO</option>
            </select>
        </div>
        <div class="col-md-12">
            <hr>
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-success" onclick="guardarCiudad(); return false;"><i class="fa fa-save"></i> Guardar</button>
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-danger" onclick="mostrarListarCiudad(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
        </div>
    </div>
</div>
