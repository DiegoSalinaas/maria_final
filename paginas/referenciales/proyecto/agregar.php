<div class="container-fluid card" style="padding: 30px; height: auto;" >

    <div class="row">
        <input type="text" value="0" id="id_proyecto" hidden>
        <div class="col-md-12">
            <h3>Agregar proyecto</h3>
        </div>
        <div class="col-md-12">
            <hr>
        </div>
        <div class="col-md-1">
            <label for="">Codigo</label>
            <input type="text" id="cod" class="form-control" readonly>
        </div>

        <div class="col-md-5">
            <label for="">Nombre</label>
            <input type="text" id="nombre_proyecto" class="form-control">

        </div>
        <div class="col-md-5">
            <label for="">Descripción</label>
            <input type="text" id="descripcion_proyecto" class="form-control">

        </div>
        <div class="col-md-5">
            <label for="">Precio</label>
            <input type="text" id="precio_proyecto" class="form-control">

        </div>

        <div class="col-md-6">
            <label for="">Marca</label>
            <select id="marca_lst" class="form-control">
            </select>
        </div>

        <div class="col-md-12">
            <hr>
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-success" onclick="guardarProyecto(); return false;"><i class="fa fa-save"></i> Guardar</button>
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-danger" onclick="mostrarListarProyecto(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
        </div>
    </div>
</div>
