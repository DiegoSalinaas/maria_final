
<%@page import="java.sql.ResultSet"%>
<%@page import="clases.conexion"%>
<%
    String id = request.getParameter("id");
    conexion cn = new conexion();
    cn.conectar();
    String sql = "";
    ResultSet rst = cn.consultar("SELECT"
            + " p.cod_presupuesto_servicio,"
            + "  p.fecha_emision_pre,"
            + "  p.fecha_ven_servicio,"
            + " c.nombre_cliente,"
            + " p.estado_pre_servicio,"
            + " SUM(pd.cantidad_insumo * pd.precio_insumo) + "
            + "  SUM(pd.precio_unitario_ser) AS total,"
            + " m.descripcion  as modelo,"
            + " me.descripcion  as marca,"
            + " ec.imiei "
            + " FROM"
            + "  presupuesto_servicio p"
            + " JOIN"
            + " cliente_1 c"
            + " ON"
            + "  c.cod_cliente = p.cod_cliente"
            + " JOIN"
            + "  det_presupuesto_servicio pd"
            + " ON"
            + " pd.cod_presupuesto_servicio = p.cod_presupuesto_servicio"
            + " join equipos_clientes ec on ec.id_equipo_cliente = p.cod_equipo "
            + "  join equipo e on e.cod_equipo  = ec.id_equipo "
            + " join modelos m on m.id_modelo  = e.id_modelo "
            + " join marcas_equipo me on me.id_marca_equipo  = m.id_marca_equipo  "
            + " where p.cod_presupuesto_servicio  = 1"
            + " GROUP BY"
            + "  p.cod_presupuesto_servicio,"
            + "  p.fecha_emision_pre,"
            + "  p.fecha_ven_servicio,"
            + "  c.nombre_cliente,"
            + "  p.estado_pre_servicio,"
            + "  m.descripcion,"
            + "  me.descripcion,"
            + "  ec.imiei "
            + " ORDER BY"
            + " p.cod_presupuesto_servicio DESC ");

    ResultSet rs_servicio = cn.consultar("select "
            + "  s.tiposervicios ,"
            + " dps.precio_unitario_ser "
            + " from det_presupuesto_servicio dps"
            + " join servicios s on s.cod_tiposervicios = dps.cod_tiposervicios "
            + " where dps.cod_presupuesto_servicio = " + id);

    ResultSet rs_insumo = cn.consultar("select "
            + " i.descripcion ,"
            + " dps.cantidad_insumo,"
            + "  dps.precio_insumo ,"
            + "  dps.cantidad_insumo * dps.precio_insumo as total"
            + " from det_presupuesto_servicio dps"
            + "  join insumos i on i.cod_insumos = dps.cod_insumos "
            + " where dps.cod_presupuesto_servicio =" + id);

    ResultSet rs_promocion = cn.consultar("select "
            + " i.descripcion,"
            + " pd.cantidad "
            + " from det_presupuesto_servicio dps "
            + " join promociones_detalle pd on pd.id_promocion  =  dps.cod_promocion "
            + " join insumos i on i.cod_insumos  = pd.id_insumo "
            + " where dps.cod_presupuesto_servicio  = " + id
    );

    rst.next();

%>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Presupuesto - Diagnóstico</title>
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
                    Presupuesto
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Fecha de emision:</th>
                            <td><%= rst.getString("fecha_emision_pre")%></td>
                            <th>Fecha de Vencimiento:</th>
                            <td><%= rst.getString("fecha_ven_servicio")%></td>
                        </tr>
                        <tr>
                            <th>Cliente:</th>
                            <td><%= rst.getString("nombre_cliente")%></td>
                            <th>Estado:</th>
                            <td><%= rst.getString("estado_pre_servicio")%></td>
                        </tr>
                        <tr>
                            <th>Equipo:</th>
                            <td colspan="3"><%= rst.getString("marca")%> <%= rst.getString("modelo")%> <%= rst.getString("imiei")%> </td>

                        </tr>
                        <tr>
                            <th>Total Presupuesto</th>
                            <td colspan="3"><%= rst.getString("total")%> </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Detalle de Servicios
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Inversión</th>
                            </tr>
                        </thead>
                        <tbody>
                            <%
                                int total = 0;
                                if (rs_servicio.isBeforeFirst()) {
                                    while (rs_servicio.next()) {%>
                            <tr>
                                <td><%= rs_servicio.getString("tiposervicios")%></td>
                                <td><%= rs_servicio.getString("precio_unitario_ser")%></td>
                            </tr>
                            <%
                                    total += rs_servicio.getInt("precio_unitario_ser");
                                }
                            } else { %>
                            <tr>
                                <td colspan="4" class="text-center">No hay servicios registrados.</td>
                            </tr>
                            <% }%>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>SUB TOTAL</th>
                                <th><%=total%></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
            <%

                if (rs_promocion.isBeforeFirst()) {
            %>
            <div class="card">
                <div class="card-header">
                    Detalle de Promocion
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Insumo Promocion</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <%
                                
                                    while (rs_promocion.next()) {%>
                            <tr>
                                <td><%= rs_promocion.getString("descripcion")%></td>
                                <td><%= rs_promocion.getString("cantidad")%></td>
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

            <div class="card">
                <div class="card-header">
                    Detalle de Insumo
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Insumo</th>
                                <th>Cantidad</th>
                                <th>Costo Unitario</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <%
                                total = 0;
                                if (rs_insumo.isBeforeFirst()) {
                                    while (rs_insumo.next()) {
                            %>
                            <tr>
                                <td><%= rs_insumo.getString("descripcion")%></td>
                                <td><%= rs_insumo.getString("cantidad_insumo")%></td>
                                <td><%= rs_insumo.getString("precio_insumo")%></td>
                                <td><%= rs_insumo.getString("total")%></td>
                            </tr>
                            <%
                                    total += rs_insumo.getInt("total");
                                }
                            } else { %>
                            <tr>
                                <td colspan="4" class="text-center">No hay servicios registrados.</td>
                            </tr>
                            <% }%>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">SUB TOTAL</th>
                                <th><%=total%></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>


<script>

    window.print();
</script>
