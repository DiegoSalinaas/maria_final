<div class="container-fluid card" style="padding: 30px;">
    <?php session_start();?>
<div class="row">
    <input type="text" id="id_cliente" hidden value="0">
    <input type="text" id="id_proveedor" hidden value="0">
    <div class="col-md-12">
        <h3>Agregar Nota de Credito / Debito Compra</h3>
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
        <input type="text" id="usuario_lst" class="form-control" readonly value="<?= $_SESSION['nombre_user']?>">
          
    </div>
    <div class="col-md-3">
        <label for="">Fecha</label>
        <input type="date" name="" id="fecha" class="form-control"></input>
    </div>
    <div class="col-md-12">
        <label>Facturas de compra</label>
        <select name="" id="facturas_compra_lst" class="form-control"></select>
    </div>
    <div class="col-md-3">
        <label for="">Nro de Factura</label>
        <input type="text" name="" id="nro_factura" class="form-control"></input>
    </div>
    <div class="col-md-3">
        <label for="">Timbrado</label>
        <input type="text" name="" id="timbrado" class="form-control"></input>
    </div>
    <div class="col-md-3">
        <label for="">Fecha Vencimiento timbrado</label>
        <input type="date" name="" id="fecha_venc" class="form-control"></input>
    </div>
    <div class="col-md-3">
        <label for="">Tipo</label>
        <select name="" id="tipo_lst" class="form-control">
            <option value="CREDITO">CREDITO</option>
            <option value="DEBITO">DEBITO</option>
        </select>
    </div>
   
    
    <div class="col-md-6">
        <label>Proveedor</label>
        <input type="text" readonly class="form-control" id="proveedor">
    </div>
    <div class="col-md-6">
        <label>Motivo</label>
        <input type="text" class="form-control" id="motivo_txt">
    </div>
     
    
       <div class="col-md-12">
        <hr> 
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
                     <th>Costo</th>
                     <th>Cantidad</th>
                     <th>Exento</th>
                     <th>I.V.A. 5%</th>
                     <th>I.V.A. 10%</th>
                    
                </tr>
            </thead>
            <tbody id="nota_compra"></tbody>
            <tfoot>
                <tr>
                    <th colspan="4">Sub Totales</th>
                    <th id="total_exenta">0</th>
                    <th id="total_iva5">0</th>
                    <th id="total_iva10">0</th>
                </tr>
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
        <button class="form-control btn btn-success" onclick="guardarNotaCreditoCompra(); return false;"><i class="fa fa-save"></i> Guardar</button>
    </div>
    <div class="col-md-3">
        <button class="form-control btn btn-danger" onclick="cancelarNotaCreditoCompra(); return false;"><i class="fa fa-ban"></i> Cancelar</button>
    </div>
</div>
</div>
