
<%@page import="java.sql.ResultSet"%>
<%@page import="clases.conexion"%>
<%
    String id = request.getParameter("id");
    conexion cn = new conexion();
    cn.conectar();
    String sql = "";
    ResultSet rst = cn.consultar("SELECT "
            + " rc.cod_recepcion, "
            + " trim(c.nombre_cliente) as nombre_cliente, "
            + " trim(s.nombre_sucur) as nombre_sucur, "
            + " rc.fecha_recepcion, "
            + " trim(rc.recepcion_estado) as recepcion_estado, "
            + " trim(rc.observacion) as observacion, "
            + " te.nombre_tipo, "
            + " m.descripcion as modelo,  "
            + " me.descripcion as marca "
            + " FROM recepcion_cabecera rc  "
            + " JOIN recepcion_detalle rd "
            + " ON rd.cod_recepcion = rc.cod_recepcion "
            + "  JOIN cliente_1 c  "
            + "  ON c.cod_cliente =  rc.cod_cliente "
            + " JOIN sucursal s  "
            + " ON s.cod_sucursal =  rc.cod_sucursal "
            + " JOIN equipo e  "
            + " ON e.cod_equipo = rd.cod_equipo "
            + " JOIN tipo_equipo te "
            + " ON e.id_tipo_equipo = te.cod_tipo "
            + " JOIN modelos m  "
            + " ON m.id_modelo =  e.id_modelo "
            + " JOIN marcas_equipo me  "
            + " ON me.id_marca_equipo = m.id_marca_equipo "
            + " WHERE rc.cod_recepcion =   " + id
            + " GROUP BY rc.cod_recepcion, c.nombre_cliente, s.nombre_sucur, "
            + " te.nombre_tipo,m.descripcion, me.descripcion");

    rst.next();

%>

<!doctype html>
<html lang="es">
    <style>
        label{
            font-weight: bolder;
        }
        .row{
            margin-top: 10px;
        }
        .col-md-6{
            margin-top: 20px;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 100px);
            grid-template-rows: repeat(3, 100px);
            gap: 10px;
        }

        .grid-item {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            width: 100px;
            height: 100px;
            background-color: #ffffff;
            border: 2px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .highlight {
            background-color: #4caf50;
            color: white;
        }
    </style>
    <head>
        <meta charset="UTF-8">
        <title>RECEPCION</title>
        <script src="../../../../js/bootstrap.min.js"></script>
        <link href="../../../../css/bootstrap.min.css" rel="stylesheet">    
    </head>
    <body style="margin: 0px auto;
          box-shadow: 1px 1px 10px 5px #999999;">
    <center><img src="../../../../img/membrete.jpg" style="width: 100%;" alt=""> </center>

    <hr> 
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <h1><center>Recepcion</center></h1>
            </div>
        </div>
        <div class="row">
            <table class="table border-0">
                <tr>
                    <th>Fecha</th>
                    <td><%= rst.getString("fecha_recepcion")%></td>
                    <th>Nro de orden</th>
                    <td><%= rst.getString("cod_recepcion")%></td>
                </tr>
                <tr>
                    <th>Cliente </th>
                    <td><%= rst.getString("nombre_cliente")%></td>
                </tr>
                <tr>
                    <th>Equipo </th>



                </tr>
                <tr>

                    <th>Estado</th>
                    <td><%= rst.getString("recepcion_estado")%></td>
                </tr>
            </table>
        </div>
        <hr>



        <div class="row">
            <%
                ResultSet rst2 = cn.consultar("select "
                        + " ec.id_equipo_cliente,"
                        + " m.descripcion  as modelo,"
                        + " me.descripcion  as marca,"
                        + " rd.cod_equipo,"
                        + " ec.imiei"
                        + " from recepcion_detalle rd"
                        + " join equipos_clientes ec on ec.id_equipo_cliente = rd.cod_equipo "
                        + " join equipo e on e.cod_equipo  =ec.id_equipo "
                        + " join modelos m on m.id_modelo  = e.id_modelo "
                        + " join marcas_equipo me on me.id_marca_equipo  = m.id_marca_equipo"
                        + " join servicios s on s.cod_tiposervicios = rd.cod_tipo_servicio "
                        + " where rd.cod_recepcion = 6 "
                        + " group by ec.id_equipo_cliente , m.descripcion, me.descripcion,rd.cod_equipo");

                while (rst2.next()) {
            %>
            <div class="col-md-12">
                <h2><%=rst2.getString("marca")%> <%=rst2.getString("modelo")%> <%=rst2.getString("imiei")%></h2>
            </div>


            <%
                ResultSet rstd = cn.consultar("select "
                        + " rd.cod_recepcion,"
                        + " rd.tipo_contrasena ,"
                        + " rd.contrasena ,"
                        + " s.tiposervicios "
                        + " from recepcion_detalle rd "
                        + " join servicios s on s.cod_tiposervicios = rd.cod_tipo_servicio "
                        + " where rd.cod_recepcion = " + id + " and rd.cod_equipo = " + rst2.getString("cod_equipo") + " ");


            %>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Tipo de contraseña</th>
                        <th>Contraseña</th>
                    </tr>
                </thead>
                <tbody>
                    <% 
                while (rstd.next()) {
                %>
                <tr>
                    <td><%=rstd.getString("tiposervicios")%></td>
                    <td><%=rstd.getString("tipo_contrasena")%></td>
                    <td><%=rstd.getString("contrasena")%></td>
                </tr>
                <%
                        }   
                    %>

                </tbody>
               
            </table>
            <hr>
            <%    }
            %>
        </div>
    </div>

</body>
</html>

<script>

    window.print();
</script>
