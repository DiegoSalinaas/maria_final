
<%@page import="java.sql.ResultSet"%>
<%@page import="clases.conexion"%>
<%
    String id = request.getParameter("id");
    conexion cn = new conexion();
    cn.conectar();
    String sql = "";
    ResultSet rst = cn.consultar("SELECT"
            + " os.cod_orden_ser,"
            + " c.nombre_cliente,"
            + "  os.fecha_emision_orden,"
            + "  os.estado_orden_ser,"
            + "  ec.imiei,"
            + "  m.descripcion as modelo,"
            + "  ma.descripcion as marca,"
            + "  string_agg(f.nombre_funcionario , ', ') as funcionarios"
            + " FROM orden_servicio_cabecera  os"
            + " JOIN cliente_1 c "
            + "  ON c.cod_cliente = os.cod_cliente"
            + " join orden_servicio_detalle  ds"
            + " ON ds.cod_orden_ser = os.cod_orden_ser "
            + " join funcionario f on f.cod_funcionario  =  ds.id_funcionario "
            + " join presupuesto_servicio ps ON ps.cod_presupuesto_servicio  = os.cod_presupuesto_servicio "
            + " join equipos_clientes ec on ps.cod_equipo = ec.id_equipo_cliente "
            + " JOIN equipo e  "
            + "  ON e.cod_equipo = ec.id_equipo "
            + " JOIN modelos m "
            + "   ON m.id_modelo = e.id_modelo "
            + " JOIN marcas_equipo ma "
            + "  ON ma.id_marca_equipo = m.id_marca_equipo"
            + "  where os.cod_orden_ser  = "+id+""
            + " GROUP BY"
            + " os.cod_orden_ser,"
            + " c.nombre_cliente,"
            + " os.fecha_emision_orden,"
            + " os.estado_orden_ser,"
            + " m.descripcion,"
            + " ec.imiei,"
            + " ma.descripcion");

    ResultSet rs_servicio = cn.consultar("select "
    +" osd.observacion,"
    +" f.nombre_funcionario,"
    +" s.tiposervicios "
    +" from orden_servicio_detalle osd"
    +" join funcionario f on f.cod_funcionario  = osd.id_funcionario "
    +" join servicios s on s.cod_tiposervicios = osd.cod_tiposervicios "
    +" where  osd.cod_orden_ser  =  " + id);

    

    

    rst.next();

%>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Orden de Servicio</title>
        <link href="../../../../css/bootstrap.min.css" rel="stylesheet">
        <script src="../../../../js/bootstrap.min.js"></script>
        <style>
            body {
                margin: 0 auto;
                box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
                background-color: #f8f9fa;
            }
            .card {
                margin: 20px auto;
                box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }
            .card-header {
                background-color: #007bff;
                color: #fff;
                font-size: 1.5rem;
                font-weight: bold;
                text-align: center;
            }
            .table th {
                background-color: #f1f1f1;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="text-center my-4">
                <img src="../../../../img/membrete.jpg" style="width: 100%; max-width: 800px;" alt="Membrete">
            </div>

            <div class="card">
                <div class="card-header">
                    Orden de Servicio
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Fecha de emision:</th>
                            <td><%= rst.getString("fecha_emision_orden")%></td>
                        </tr>
                        <tr>
                            <th>Cliente:</th>
                            <td><%= rst.getString("nombre_cliente")%></td>
                            <th>Estado:</th>
                            <td><%= rst.getString("estado_orden_ser")%></td>
                        </tr>
                        <tr>
                            <th>Equipo:</th>
                            <td colspan="3"><%= rst.getString("marca")%> <%= rst.getString("modelo")%> <%= rst.getString("imiei")%> </td>

                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Detalle de Orden de Servicios
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Funcionario</th>
                                <th>Observacion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <%
                              
                                if (rs_servicio.isBeforeFirst()) {
                                    while (rs_servicio.next()) {%>
                            <tr>
                                <td><%= rs_servicio.getString("tiposervicios")%></td>
                                <td><%= rs_servicio.getString("nombre_funcionario")%></td>
                                <td><%= rs_servicio.getString("observacion")%></td>
                            </tr>
                            <%
                                    
                                }
                            } else { %>
                            <tr>
                                <td colspan="4" class="text-center">No hay servicios registrados.</td>
                            </tr>
                            <% }%>
                        </tbody>
                        

                    </table>
                </div>
            </div>
           
        </div>
    </body>
</html>


<script>

    window.print();
</script>
