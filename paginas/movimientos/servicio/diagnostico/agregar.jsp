<%@page import="java.sql.ResultSet"%>
<%@page import="clases.conexion"%>
<input type="text" id="id_cliente" hidden value="0">
<input type="text" id="id_equipo" hidden value="0">
<input type="text" id="id_producto" value="0" hidden>
<input type="text" id="iva" value="0" hidden>
<%@page import="clases.sesion"%>
<input type="text" class="form-control form-control-sm" value="<%=sesion.cod_usuario%>" id="id_usuario" hidden>
<input type="text" class="form-control form-control-sm" value="<%=sesion.cod_sucursal%>" id="id_sucursal" hidden>
<div class="row">
    <div class="col-12">
        <h3 id="titulo-principal">Nuevo Diagnostico</h3>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>


    <div class="col-md-3">
        <label>Nro de Diagnostico</label>
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
        <label>Recepcion</label>
        <select  id="recepcion_diag_lst" class="form-control"></select>
    </div>
    <div class="col-md-12">
        <label>Dispositivos</label>
        <select  id="dispositivos_diag_lst" class="form-control"></select>
    </div>
    <div class="col-md-12">
        <label>Observaciones</label>
        <textarea id="observacion" class="form-control" name="name" rows="5" cols="10"></textarea>
    </div>
    

    <div class="col-md-12">
        <hr> 
    </div>

    <div class="col-md-12">
        <hr> 
    </div>
    <div class="col-md-4">
        <label>Servicio</label>
        <select  id="servicios_lst" class="form-control chosen-select">
            <%
                conexion cn = new conexion();
                cn.conectar();
                ResultSet rscli = cn.consultar("select * from servicios where UPPER(estado_servicios) = 'ACTIVO'");
                while (rscli.next()) {
            %>

            <option  value="<%= rscli.getString("cod_tiposervicios")%>"><%= rscli.getString("tiposervicios")%></option>

            <%
                }  
            %>

        </select>
    </div>
    <div class="col-md-4">
        <label>Insumo</label>
        <select  id="insumo_lst" class="form-control chosen-select">
                   <%                   
                cn.conectar();
                ResultSet rs = cn.consultar("select * from insumos where UPPER(estado_insumos) = 'ACTIVO' order by cod_insumos");
                while (rs.next()) {
            %>

            <option  value="<%= rs.getString("cod_insumos")%>"><%= rs.getString("descripcion")%></option>

            <%
                }

            %>

        </select>
    </div>


    <div class="col-md-2">
        <label>Cantidad</label>
        <input type="text" class="form-control" id="cantidad_diagnostico_servicio">
    </div>
    <div class="col-md-2">
        <label>Operaciones</label>
        <button class="btn btn-primary" onclick="agregarDiagnosticoTabla();">Agregar</button>
    </div>


    <div class="col-md-12">
        <hr> 
    </div>

    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th hidden>#</th>
                    <th>TIPO DE SERVICIO</th>
                    <th>INSUMO</th>
                    <th>CANTIDAD</th>
                    <th>OBSERVACION</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="diagnostico_servicio_tb"></tbody>
        </table>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>

   
   
    <div class="col-md-3">
        <button class="btn btn-success form-control" onclick="guardarDiagnostico(); return false;">Confirmar</button>
    </div>

</div>

