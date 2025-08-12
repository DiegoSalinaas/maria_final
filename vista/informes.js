function mostrarListarInforme() {
    let contenido = dameContenido("paginas/movimientos/compra/informes/principal.php");
    $(".contenido-principal").html(contenido);
    dameFechaActual("desde");
    dameFechaActual("hasta");
}
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
$(document).on("change", "#movimiento_lst", function (evt) {
    let movimiento = $("#movimiento_lst").val();
    let especificaciones = "<option value='0'>Selecciona una especificacion</option>";
    
    switch (movimiento) {
        case "Pedido":
            especificaciones += `<option value='pedido_pendiente'>Pedidos pendiente</option>`;
            especificaciones += `<option value='pedido_presupuestado'>Pedidos Presupuestado</option>`;
            especificaciones += `<option value='pedido_anulado'>Pedidos Anulado</option>`;
            break;
        case "Presupuesto":
            especificaciones += `<option value='presupuesto_pendiente'>Presupuesto pendiente</option>`;
            especificaciones += `<option value='presupuesto_anulado'>Presupuesto Anulado</option>`;
            break;
        case "Orden Compra":
            especificaciones += `<option value='orden_pendiente'>Orden pendiente</option>`;
            especificaciones += `<option value='orden_anulado'>Orden Anulado</option>`;
            especificaciones += `<option value='orden_utilizado'>Orden Utilizado</option>`;
            break;
        case "Factura de compra":
            especificaciones += `<option value='factura_anulado'>Factura Anulado</option>`;
            especificaciones += `<option value='factura_activo'>Factura Activa</option>`;
            break;
        case "Libro Compra":
            especificaciones += `<option value='factura_anulado'>Factura Anulado</option>`;
            especificaciones += `<option value='factura_activo'>Factura Activa</option>`;
            break;
            
        default:
            
            break;
    }
    
    $("#especificacion_lst").html(especificaciones);
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function imprimirInforme(){
    let reporte = $("#especificacion_lst").val();
    
    let desde = $("#desde").val();
    let hasta = $("#hasta").val();
    
    if(desde > hasta){
        mensaje_dialogo_info_ERROR("La fecha desde no puede ser mayor al hasta", "ATENCION");
        return;
    }
    
    if(reporte === "0"){
        mensaje_dialogo_info_ERROR("Debes seleccionar una especificacion", "ATENCION");
        return;
    }
    
    window.open(`paginas/movimientos/compra/informes/print/${reporte}.php?desde=${desde}&hasta=${hasta}`);
}
