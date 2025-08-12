<div class="container-fluid card" style="padding: 30px;">
    <?php session_start();?>
<div class="row">
    <input type="text" id="id_cliente" hidden value="0">
    <div class="col-md-12">
        <h3>Agregar Factura de  Compra</h3>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>
    <div class="col-md-2">
        <label for="">Codigo</label>
        <input type="text" id="cod" class="form-control" readonly>
    </div>
   
    <div class="col-md-4">
        <label for="">Usuario</label>
        <input type="text" id="usuario_lst" class="form-control" readonly value="<?= $_SESSION['nombre_user']?>">
          
    </div>
    <div class="col-md-3">
        <label for="">Fecha</label>
       <input type="date" id="fecha" class="form-control">
    </div>
    <div class="col-md-3">
        <label for="">Condición</label>
        <select name="" id="condicion_lst" class="form-control">
            <option value="CONTADO">CONTADO</option>
            <option value="CREDITO">CREDITO</option>
        </select>
    </div>
    <div class="col-md-3">
        <label for="">Nro de Factura</label>
        <input type="text" id="nro_factura" class="form-control" readonly>
    </div>
    <div class="col-md-3">
        <label for="">Timbrado</label>
       <input type="text" id="timbrado" class="form-control" readonly>
    </div>
    <div class="col-md-3">
        <label for="">Fecha Vencimiento timbrado</label>
        <input type="date" id="fecha_venc" class="form-control" readonly>

    </div>
    
    <div class="col-md-12 bloque-credito" hidden>
        <div class="row" style="border: 1px solid #cecece; padding: 20px; background: #cccccc; 
             box-shadow: inset 0px 4px 8px rgba(0, 0, 0, 0.2);">
            <div class="col-md-12">
                <h5>Configuración de cuenta a pagar</h5>
            </div>
            <div class="col-md-6">
                <label>Intervalo</label>
                <input type="number" min="1" id="intervalo" value="1" class="form-control">
            </div>
            <div class="col-md-6">
                <label>Monto</label>
                <input type="text" readonly value="0" id="monto_cuota" class="form-control">
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <label>Orden de Compra</label>
       <select id="orden_compra_lst" class="form-control"></select>
    </div>
    <div class="col-md-5">
        <label>Proveedor</label>
        <select name="" id="proveedor_compra_lst" class="form-control"></select>
    </div>
    
    
       <div class="col-md-12">
        <hr> 
    </div>
    <div class="col-md-4">
        <label>Producto</label>
        <select name="" id="producto_lst" class="form-control"></select>
    </div>
    <div class="col-md-3">
        <label>Cantidad</label>
        <input type="text" value="1" class="form-control formatear-numero" id="cantidad_txt">
    </div>
    <div class="col-md-3">
        <label>Costo</label>
        <input type="text" value="1" class="form-control formatear-numero" id="costo_txt">
    </div>
    <div class="col-md-2">
        <label>Operaciones</label>
        <button class="form-control btn btn-primary" onclick="agregarTablaFacturaCompra(); return false;">Agregar</button>
    </div>
       <div class="col-md-12">
        <hr> 
    </div>
    
 
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                     <th>#</th>
                    <th>Producto</th>
                     <th>Costo</th>
                     <th>Cantidad</th>
                     <th>Total</th>

                    
                </tr>
            </thead>
            <tbody id="factura_compra"></tbody>
            <tfoot>
  
                <tr>
                    <th colspan="6">Total General</th>
                    <th id="total">0</th>
                </tr>
            </tfoot>
        </table>
    </div>
    
   
   
    <div class="col-md-12">
        <hr> 
    </div>
    <div class="col-md-3">
        <button class="form-control btn btn-success" onclick="guardarFacturaCompra(); return false;"><i class="fa fa-save"></i> Guardar</button>
    </div>
    <div class="col-md-3">
        <button class="form-control btn btn-danger" onclick="cancelarFacturaCompra(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
    </div>
</div>
</div>
