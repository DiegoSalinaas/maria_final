
<%@page import="java.sql.ResultSet"%>
<%@page import="clases.conexion"%>
<input type="text" id="id_cliente" hidden>
<input type="text" id="id_producto" value="0" hidden>
<input type="text" id="iva" value="0" hidden>
<input type="text" id="id_recepcion" value="0" hidden>
<%@page import="clases.sesion"%>
<input type="text" class="form-control form-control-sm" value="<%=sesion.cod_usuario%>" id="id_usuario" hidden>
<input type="text" class="form-control form-control-sm" value="<%=sesion.cod_sucursal%>" id="id_sucursal" hidden>
<div class="row">
    <div class="col-12">
        <h3 id="titulo-principal">Nueva Recepcion de Servicio</h3>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>


    <div class="col-md-3">
        <label>Nro de Recepcion</label>
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
        <label>Fecha Emisión</label>
        <input type="date" class="form-control" id="fecha" readonly="" min="">
    </div>

    <div class="col-md-8">
        <div class="row">
            <div class="col-md-10">
                <label>Cliente</label>
                <select  id="cliente" class="form-control chosen-select">
                    <option value="0">Selecciona un cliente</option>
                    <%
                        conexion cn = new conexion();
                        cn.conectar();
                        ResultSet rscli = cn.consultar("select * from cliente_1 order by cod_cliente");
                        while (rscli.next()) {
                    %>

                    <option  value="<%= rscli.getString("cod_cliente")%>"><%= rscli.getString("nombre_cliente")%> | Cedula : <%= rscli.getString("ci_cliente")%></option>

                    <%
                        }

                    %>
                </select>
            </div>
            <div class="col-md-2" style="margin-top: 23px;">
                <button class="form-control btn btn-primary" >Agregar Cliente</button>
            </div>
            <div class="col-md-10">
                <label>Equipo</label>
                <select  id="equipo" class="form-control chosen-select">
                    <option value="0">Selecciona un equipo</option>

                </select>
            </div>
            <div class="col-md-2" style="margin-top: 23px;">
                <button class="form-control btn btn-primary">Agregar Equipo</button>
            </div>
            <div class="col-md-12">
                <label>Tipo Contraseña</label>
                <select  id="tipo_contra" class="form-control chosen-select">
                    <option value="0">Selecciona un tipo de contraseña</option>
                    <option value="SIN CONTRASEÑA">SIN CONTRASEÑA</option>
                    <option value="PATRON">PATRON</option>
                    <option value="PIN">PIN</option>
                    <option value="ALFANUMERICO">ALFANUMERICO</option>

                </select>
            </div>
        </div>
    </div>


    <div class="col-md-4" id="contenido-pass">

    </div>

    <div class="col-md-12">
        <label>Descripción</label>
        <textarea  id="descripcion" class="form-control" cols="30" rows="5"></textarea>
    </div>


    <div class="col-md-12">
        <button class="btn btn-primary form-control" onclick="agregarEquipoRecepcion(); return false;">Agregar Equipo</button>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>
    <div class="col-md-12">
        <table class="table table-bordered"
               <thead>
                <tr>
                    <th>#</th>
                    <th>Equipo</th>
                    <th>Tipo contraseña</th>
                    <th>Contraseña</th>
                    <th>Descripcion</th>
                    <th>Operaciones</th>
                </tr>
            </thead>
            <tbody id="equipos_recepcion_tb"></tbody>
        </table>
    </div>
    <div class="col-md-12">
        <div id="tabla-detalles" class="row">

        </div>
    </div>
    <div class="col-md-12">
        <hr> 
    </div>



    <div class="col-md-3">
        <button class="btn btn-success form-control" onclick="guardarRecepcionServicio(); return false;">Confirmar</button>
    </div>

</div>



