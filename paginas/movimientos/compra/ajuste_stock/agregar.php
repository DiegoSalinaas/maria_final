<div class="container-fluid card" style="padding: 30px; height: auto;" >
    <?php session_start(); ?>
    <div class="row">
        <input type="text" id="id_cliente" hidden value="0">
        <div class="col-md-12">
            <h3>Agregar Ajuste de Stock</h3>
        </div>
        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-1">
            <label for="">Codigo</label>
            <input type="text" id="cod" class="form-control" readonly>
        </div>
        
        <div class="col-md-4">
            <label for="">Usuario</label>
            <input type="text" id="usuario_lst" class="form-control" readonly value="<?= $_SESSION['nombre_user'] ?>">

        </div>
        <div class="col-md-4">
            <label for="descripcion">Fecha</label>
            <input type="date" id="fecha" class="form-control" placeholder=" ">
        </div>
        
       
       

        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-4">
            <label>Insumo</label>
            <select name="" id="material_lst" class="form-control"></select>
        </div>
        <div class="col-md-3">
            <label for="">Tipo</label>
            <select id="tipo" class="form-control">
                <option value="ENTRADA">ENTRADA</option>
                <option value="SALIDA">SALIDA</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Cantidad</label>
            <input type="text" value="1" class="form-control formatear-numero" id="cantidad_txt">
        </div>
        <div class="col-md-2">
            <label>Operaciones</label>
            <button class="form-control btn btn-primary" onclick="agregarTablaAjusteStock(); return false;">Agregar</button>
        </div>
        <div class="col-md-12">
            <hr> 
        </div>


        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Insumo</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>

                    </tr>
                </thead>
                <tbody id="ajuste_stock_compra"></tbody>
            </table>
        </div>



        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-success" onclick="guardarAjusteStock(); return false;"><i class="fa fa-save"></i> Guardar</button>
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-danger" onclick="cancelarAjusteStock(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
        </div>
    </div>
</div>
