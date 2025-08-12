<input type="text" id="id_cliente" value="0" hidden>
<input type="text" id="id_proveedor" value="0" hidden>
<div class="container-fluid card" style="padding: 30px;">
    <div class="row">
        <div class="col-md-12">
            <h1>Informe de movimientos</h1>
        </div>
        <div class="col-md-6">
            <label>Movimiento</label>
            <select id="movimiento_lst" class="form-control">
                <option value="0">Selecciona un movimiento</option>
                <option value="Pedido">Pedido</option>
                <option value="Presupuesto">Presupuesto</option>
                <option value="Orden Compra">Orden de Compra</option>
                <option value="Factura de compra">Factura de compra</option>
                <option value="Libro Compra">Libro Compra</option>
                <option value="Pedido Compra">Cuenta a pagar</option>
                <option value="Gastos">Nota de credito</option>
                <option value="Nota de Compra">Ajustes</option>
                <option value="Nota de Compra">Remision</option>

            </select>
        </div>
        <div class="col-md-6">
            <label>Especificacion</label>
            <select id="especificacion_lst" class="form-control">


            </select>
        </div>

        <div class="col-md-6">
            <label>Desde</label>
            <input type="date" class="form-control" id="desde">
        </div>
        <div class="col-md-6">
            <label>Hasta</label>
            <input type="date" class="form-control" id="hasta">
        </div>
        <div class="col-md-12" hidden id="panel_cliente">
            <div class="col-md-3">
                <label>RUC/Cedula</label>
                <input type="text" id="cedula_ruc_fac" class="form-control">
            </div>
            <div class="col-md-9">
                <label>Nombre Cliente</label>
                <input type="text" id="nombre_cliente_fqac" class="form-control">
            </div>
        </div>
        <div class="col-md-12" hidden id="panel_proveedor">
            <div class="col-md-4">
                <label for=" ">RUC Proveedor</label>
                <input type="text" class="form-control" id="rucProveedor">
            </div>

            <div class="col-md-8">
                <label for=" ">Razón Social</label>
                <input type="text" class="form-control" id="nombreProveedor">
            </div>
        </div>
        <div class="col-md-12" style="margin-top: 20px;">
            <button class="btn btn-success form-control" onclick="imprimirInforme(); return false;">Generar e imprimir</button>
        </div>

    </div>
</div>

