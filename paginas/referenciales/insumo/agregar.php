<div class="container-fluid card" style="padding: 30px; height: auto;" >

    <div class="row">
        <input type="text" value="0" id="id_insumo" hidden>
        <div class="col-md-12">
            <h3>Agregar insumos</h3>
        </div>
        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-1">
            <label for="">Codigo</label>
            <input type="text" id="cod" class="form-control" readonly>
        </div>

        <div class="col-md-5">
            <label for="">Descripcion</label>
            <input type="text" id="descripcion_lst" class="form-control">

        </div>

        <div class="col-md-6">
            <label for="">Marca insumo</label>
            <select id="marca_insumo" class="form-control">
            </select>
        </div>
        <div class="col-md-6">
            <label for="">Tipo IVA</label>
            <select id="tipo_iva_insumo" class="form-control">
            </select>
        </div>
        <div class="col-md-6">
            <label for="">Cantidad</label>
            <input type="text" id="cantidad_txt" class="form-control">

        </div>

        <div class="col-md-6">
            <label for="">Costo</label>
            <input type="text" id="costo_txt" class="form-control">


        </div>
        
        <div class="col-md-6">
            <label for="">Precio</label>
            <input type="text" id="precio_txt" class="form-control">

        </div>

        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-success" onclick="guardarInsumo(); return false;"><i class="fa fa-save"></i> Guardar</button>
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-danger" onclick="mostrarListarInsumo(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
        </div>
    </div>
</div>
