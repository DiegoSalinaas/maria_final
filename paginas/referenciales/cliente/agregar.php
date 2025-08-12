<div class="container-fluid card" style="padding: 30px; height: auto;" >

    <div class="row">
        <input type="text" value="0" id="id_cliente" hidden>
        <div class="col-md-12">
            <h3>Agregar cliente</h3>
        </div>
        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-1">
            <label for="">Codigo</label>
            <input type="text" id="cod" class="form-control" readonly>
        </div>

        <div class="col-md-5">
            <label for="">Nombre y Apellido</label>
            <input type="text" id="nombre_cliente" class="form-control">

        </div>
        <div class="col-md-5">
            <label for="">C.I. Nº</label>
            <input type="text" id="ci_cliente" class="form-control">

        </div>
   <div class="form-group">
    <label for="ruc_cliente">RUC</label>
    <input type="text" class="form-control" id="ruc_cliente" name="ruc_cliente" placeholder="Ingrese RUC">
    <div id="mensaje_ruc" class="invalid-feedback"></div>
</div>
        <div class="col-md-5">
            <label for="">Telefono</label>
            <input type="text" id="telefono_cliente" class="form-control">

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
            <button class="form-control btn btn-success" onclick="guardarClientes(); return false;"><i class="fa fa-save"></i> Guardar</button>
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-danger" onclick="mostrarListarCliente(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
        </div>
    </div>
</div>
