<div class="container-fluid card" style="padding: 30px; height: auto;" >

    <div class="row">
        <input type="text" value="0" id="id_equipo" hidden>
        <div class="col-md-12">
            <h3>Agregar Equipo</h3>
        </div>
        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-3">
            <label for="">Codigo</label>
            <input type="text" id="cod" class="form-control" readonly>
        </div>


        <div class="col-md-3">
            <label for="">Tipo equipo</label>
            <select id="tipo_equipo" class="form-control">
            </select>
        </div>
        <div class="col-md-3">
            <label for="">Color</label>
            <select id="color_lst" class="form-control">
            </select>
        </div>
        <div class="col-md-3">
            <label for="">Modelo</label>
            <select id="modelo_lst" class="form-control">
            </select>
        </div>
        
        <div class="col-md-12">
            <br> 
        </div>
      
        <div class="col-md-3">
            <button class="form-control btn btn-success" onclick="guardarEquipo(); return false;"><i class="fa fa-save"></i> Guardar</button>
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-danger" onclick="mostrarListarEquipo(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
        </div>
    </div>
</div>
