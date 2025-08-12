<div class="container-fluid card" style="padding: 30px; height: auto;" >
    <?php session_start(); ?>
    <div class="row">
        <input type="text" id="id_cliente" hidden value="0">
        <div class="col-md-12">
            <h3>Agregar Remisión</h3>
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
            <label for="">Compras</label>
            <select  id="factura_compra_remision_lst" class="form-control"></select>
        </div>
        <div class="col-md-3">
            <label for="">Nro de Nota</label>
            <input type="text" class="form-control" id="nro_nota">
        </div>
        <div class="col-md-3">
            <label for="">Timbrado</label>
            <input type="text" class="form-control" id="timbrado">
        </div>
        <div class="col-md-3">
            <label for="">Vencimiento</label>
            <input type="date" class="form-control" id="vencimiento">
        </div>
        <div class="col-md-3" >
            <label for="">Motivo</label>
            <input type="text" class="form-control" id="motivo">
        </div>
        <div class="col-md-3">
            <label for="">Punto de Salida</label>
            <input type="text" class="form-control" id="punto_salida">
        </div>
        <div class="col-md-3">
            <label for="">Punto de Llegada</label>
            <input type="text" class="form-control" id="punto_llegada">
        </div>
        <div class="col-md-3">
            <label for="">Chofer</label>
            <input type="text" class="form-control" id="chofer">
        </div>
        <div class="col-md-3">
            <label for="">Vehiculo</label>
             <input type="text" class="form-control" id="vehiculo_lst">
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
                        <th>Cantidad Factura</th>
                        <th>Cantidad Recibida</th>

                    </tr>
                </thead>
                <tbody id="ajuste_stock_compra"></tbody>
            </table>
        </div>



        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-success" onclick="guardarRemision(); return false;"><i class="fa fa-save"></i> Guardar</button>
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-danger" onclick="cancelarRemision(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
        </div>
    </div>
</div>
