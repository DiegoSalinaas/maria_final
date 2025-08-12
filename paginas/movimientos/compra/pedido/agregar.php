

<div class="container-fluid card" style="padding: 30px; height: auto;" >
    <input type="text" id="editar" value="NO" hidden>
    <?php session_start(); ?>
    <div class="row">
        <input type="text" id="id_cliente" hidden value="0">
        <div class="col-md-12">
            <h3>Agregar un nuevo Pedido</h3>
        </div>
        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-1">
            <label for="">Codigo</label>
            <input type="text" id="cod" class="form-control" readonly>
        </div>
<!--        <div class="col-md-3">
            <label for="">Sucursal</label>
            <select name="" id="sucursal_lst" class="form-control"></select>
        </div>-->
        <div class="col-md-3">
            <label for="">Usuario</label>
            <input type="text" id="usuario_lst" class="form-control" readonly value="<?= $_SESSION['usuario_alias'] ?>">

        </div>
        <div class="col-md-4">
            <label for="descripcion">Fecha de Pedido</label>
            <input type="date" id="fecha_pedido" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="descripcion">Proveedor</label>
            <select name="proveedor" id="proveedor_lst" class="form-control">
                
            </select>
            <!--<input type="text" id="proveedor_lst" class="form-control">-->
        </div>

        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-6">
            <label>Insumo</label>
            <select name="" id="material_lst" class="form-control"></select>
        </div>
        <div class="col-md-4">
            <label>Cantidad</label>
            <input type="text" value="1" class="form-control formatear-numero" id="cantidad_txt">
        </div>
        <div class="col-md-2">
            <label>Operaciones</label>
            <button class="form-control btn btn-primary" onclick="agregarTablaPedidoCompra(); return false;">Agregar</button>
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
                        <th>Cantidad</th>

                    </tr>
                </thead>
                <tbody id="pedido_compra"></tbody>
            </table>
        </div>



        <div class="col-md-12">
            <hr> 
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-success" id="guardar_btn" onclick="guardarPedidoCompra(); return false;"><i class="fa fa-save"></i> Guardar</button>
        </div>
        <div class="col-md-3">
            <button class="form-control btn btn-danger" onclick="cancelarPedidoCompra(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
        </div>
    </div>
</div>
