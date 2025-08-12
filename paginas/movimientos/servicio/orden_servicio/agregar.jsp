<%@page import="java.sql.ResultSet"%>
<%@page import="clases.conexion"%>
<input type="text" id="id_cliente" hidden value="0">
<input type="text" id="id_equipo"  hidden value="0" >
<input type="text" id="iva" value="0"  hidden>
<%@page import="clases.sesion"%>
<input type="text" class="form-control form-control-sm" value="<%=sesion.cod_usuario%>" id="id_usuario" hidden>
<input type="text" class="form-control form-control-sm" value="<%=sesion.cod_sucursal%>" id="id_sucursal" hidden>
<div class="row">
    <div class="col-12">
        <h3 id="titulo-principal">Nueva Orden de Servicio</h3>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>


    <div class="col-md-3">
        <label>Nro de Orden</label>
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
    
    


    <div class="col-md-12">
        <label>Presupuesto</label>
        <select  id="orden_presu_lst" class="form-control"></select>
    </div>

    <div class="col-md-12">
        <hr> 
    </div>

   
    <div class="col-md-12">
        <hr> 
    </div>

    <div class="col-12">
        <h4>Orden Servicio</h4>
    </div>
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>SERVICIO</th>
                    <th>FUNCIONARIO</th>
                    <th>OBSERVACION</th>
                </tr>
            </thead>
            <tbody id="servicio_orden_tb"></tbody>
            
        </table>
    </div>
    
  
   
    <div class="col-md-12">
        <hr> 
    </div>
    <div class="col-md-3">
        <button class="btn btn-success form-control" onclick="guardarOrdenServicio(); return false;">Confirmar</button>
    </div>

</div>

