<%@page import="java.sql.ResultSet"%>
<%@page import="clases.conexion"%>
<input type="text" id="id_cliente" hidden >
<input type="text" id="id_equipo"  hidden value="0" >
<input type="text" id="iva" value="0"  hidden>
<%@page import="clases.sesion"%>
<input type="text" class="form-control form-control-sm" value="<%=sesion.cod_usuario%>" id="id_usuario" hidden>
<input type="text" class="form-control form-control-sm" value="<%=sesion.cod_sucursal%>" id="id_sucursal" hidden>
<div class="row">
    <div class="col-12">
        <h3 id="titulo-principal">Nuevo Presupuesto de Servicio</h3>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>


    <div class="col-md-3">
        <label>Nro de Presupuesto</label>
        <input type="text" class="form-control" id="cod" readonly>
    </div>
    <div class="col-md-3">
        <label>Sucursal</label>
        <input type="text" class="form-control" id="sucursal" value="<%=sesion.nombre_sucur%>" readonly>
    </div>

    <div class="col-md-3">
        <label>Usuario</label>
        <input type="text" class="form-control" id="sucursal" value="<%=sesion.usuario_alias%>" readonly>
    </div>

    <div class="col-md-3">
        <label>Fecha</label>
        <input type="date" class="form-control" id="fecha" readonly>
    </div>
    
    <div class="col-md-3">
        <label>Fecha Vencimiento</label>
        <input type="date" class="form-control" id="fecha_vencimiento" >
    </div>


    <div class="col-md-12">
        <label>Diagnostico</label>
        <select  id="diagnostico_presu_lst" class="form-control"></select>
    </div>

    <div class="col-md-12">
        <hr> 
    </div>



    <div class="col-md-4">
        <label>Cliente</label>
        <input type="text" readonly class="form-control" id="cliente_diag">
    </div>
    <div class="col-md-4">
        <label>Marca</label>
        <input type="text" readonly class="form-control" id="marca_diag">
    </div>
    <div class="col-md-4">
        <label>Modelo</label>
        <input type="text" readonly class="form-control" id="modelo_diag">
    </div>

    <div class="col-md-12">
        <label>Observación</label>
        <textarea  id="observacion" cols="30" rows="5" class="form-control"></textarea>
    </div>


    

    <div class="col-md-12">
        <hr> 
    </div>

    <div class="col-12">
        <h4>Presupuesto de Insumos</h4>
    </div>
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>INSUMO</th>
                    <th>CANTIDAD</th>
                    <th>PRECIO</th>
                    <th>TOTAL</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="presupuesto_insumo_tb"></tbody>
            <tfoot>
                <tr>
                    <th colspan="4">SUB TOTAL</th>
                    <th id="total_presu_insumo">0</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>
    <div class="col-12">
        <h4>Presupuesto de Servicios</h4>
    </div>
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>TIPO SERVICIO</th>
                    <th>INVERSION</th>
                    <th>PROMOCIONES</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="presupuesto_servicio_tb"></tbody>
            <tfoot>
                <tr>
                    <th colspan="2" >SUB TOTAL</th>
                    <th id="presu_servicio_total">0</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>

    <table class="table table-bordered">
        <tr style="font-size: 20px;">
            <th colspan="4" >TOTAL</th>
            <th id="total_presu_servicio">0</th>
        </tr>
    </table>
    <div class="col-md-12">
        <hr> 
    </div>
    <div class="col-md-3">
        <button class="btn btn-success form-control" onclick="guardarPresupuestoServicio(); return false;">Confirmar</button>
    </div>

</div>

