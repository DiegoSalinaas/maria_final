
<%@page import="java.sql.ResultSet"%>
<%@page import="clases.conexion"%>
<%
    String id = request.getParameter("id");
    conexion cn = new conexion();
    cn.conectar();
    String sql = "";
    ResultSet rst = cn.consultar("SELECT"
            + " d.id_diagnostico,"
            + " c.nombre_cliente,"
            + " TO_CHAR(d.fecha, 'DD/MM/YYYY') AS fecha,"
            + " d.observacion,"
            + " d.estado,"
            + " te.nombre_tipo,"
            + " m.descripcion as modelo,"
            + " me.descripcion as marca"
            + " FROM diagnosticos d "
            + " JOIN cliente_1 c "
            + " ON c.cod_cliente =  d.id_cliente"
            + " JOIN equipo e "
            + " ON e.cod_equipo = d.id_equipo"
            + " JOIN tipo_equipo te"
            + " ON te.cod_tipo = e.id_tipo_equipo"
            + " JOIN modelos m "
            + " ON m.id_modelo = e.id_modelo"
            + " JOIN marcas_equipo me "
            + " ON me.id_marca_equipo = m.id_marca_equipo"
            + " WHERE d.id_diagnostico = " + id);
    ResultSet rstd = cn.consultar("SELECT"
            + " s.tiposervicios,"
            + "i.descripcion as insumo,"
            + " dd.cantidad,"
            + "dd.observacion "
            + " FROM diagnosticos_detalle dd "
            + " JOIN servicios s "
            + " ON s.cod_tiposervicios = dd.id_tipo_servicio"
            + " JOIN insumos i "
            + " ON i.cod_insumos = dd.id_insumo"
            + " WHERE id_diagnostico = " + id);

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

                <h1><center>Diagnostico</center></h1>
            </div>
        </div>
        <div class="row">
            <table class="table border-0">
                <tr>
                    <th>Fecha</th>
                    <td><%= rst.getString("fecha")%></td>
                    <th>Nro de Diagnostico</th>
                    <td><%= rst.getString("id_diagnostico")%></td>
                </tr>
                <tr>
                    <th>Cliente </th>
                    <td><%= rst.getString("nombre_cliente")%></td>

                </tr>

                <tr>
                    <th>Equipo</th>
                    <td><%= rst.getString("marca")%> <%= rst.getString("modelo")%></td>
                    <th>Estado</th>
                    <td><%= rst.getString("estado")%></td>
                </tr>
            </table>
        </div>
        <hr>
        <div class="row">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Insumo</th>
                        <th>Cantidad</th>
                        <th>Observacion</th>
                    </tr>
                </thead>
                <tbody>
                    <%
                        if (rstd.isBeforeFirst()) {
                         
                            while (rstd.next()) {
                            %>
                            <tr>
                                <td><%= rstd.getString("tiposervicios") %></td>
                                <td><%= rstd.getString("insumo") %></td>
                                <td><%= rstd.getString("cantidad") %></td>
                                <td><%= rstd.getString("observacion") %></td>
                            </tr>
                            <%
                            }
                        
                        }
                    %>

                </tbody>
                <tfooter>
                    <tr>

                    </tr>
                </tfooter>
            </table>
            <hr>

        </div>
    </div>

</body>
</html>

<script>

    window.print();
</script>
