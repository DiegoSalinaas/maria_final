<div class="container-fluid card" style="padding: 30px; height: auto;" >

    <div class="row">
        <input type="text" value="0" id="id_proveedor" hidden>
        <div class="col-md-12">
            <h3>Agregar proveedor</h3>
        </div>
        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-1">
            <label for="">Codigo</label>
            <input type="text" id="cod" class="form-control" readonly>
        </div>

        <div class="col-md-5">
            <label for="">Razon Social</label>
            <input type="text" id="nombre_proveedor" class="form-control">

        </div>
        
        <div class="col-md-5">
            <label for="">RUC</label>
            <input type="text" id="ruc_proveedor" class="form-control">

        </div>
        <div class="col-md-5">
            <label for="">Telefono</label>
            <input type="text" id="telefono_proveedor" class="form-control">

        </div>

        <div class="col-md-6">
            <label for="">Ciudad</label>
            <select id="ciudad_lst" class="form-control">
            </select>
        </div>
       
        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-success" onclick="guardarProveedores2(); return false;"><i class="fa fa-save"></i> Guardar</button>
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-danger" onclick="mostrarListarProveedores(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
        </div>
    </div>
</div>
