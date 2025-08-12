function mostrarListarNotaCreditoCompra() {
    let contenido = dameContenido("paginas/movimientos/compra/nota_credito/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaNotaCreditoCompra();
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function mostrarAgregarNotaCreditoCompra() {
    let contenido = dameContenido("paginas/movimientos/compra/nota_credito/agregar.php");
    $(".contenido-principal").html(contenido);
    
    dameFechaActual("fecha");
    dameFechaActual("fecha_venc");
    cargarListaFacturasComprasActivas("#facturas_compra_lst");
    
    $("#sucursal_lst").val("1");
    //obtener ultimo id
    let ultimo = ejecutarAjax("controladores/nota_compra.php", "ultimo_registro=1");
    if (ultimo === "0") {
        $("#cod").val("1");
    } else {
        let json_ultimo = JSON.parse(ultimo);
        $("#cod").val(quitarDecimalesConvertir(json_ultimo['cod_nota_compra']) + 1);
    }

}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function cancelarNotaCreditoCompra() {
    Swal.fire({
        title: "ATENCION",
        text: "Desea cancelar la operacion?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Si"
    }).then((result) => {
        if (result.isConfirmed) {
            mostrarListarNotaCreditoCompra();
        }
    });

}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function agregarTablaNotaCreditoCompra() {
    if ($("#material_lst").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar un material", "ATENCION");
        return;
    }

    if ($("#cantidad_txt").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes ingresar una cantidad", "ATENCION");
        return;
    }

    let cantidad = quitarDecimalesConvertir($("#cantidad_txt").val().trim());

    if (cantidad <= 0) {
        mensaje_dialogo_info_ERROR("La cantidad debe ser mayor a cero", "ATENCION");
        return;
    }
    if ($("#costo_txt").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes ingresar un costo", "ATENCION");
        return;
    }

    let costo = quitarDecimalesConvertir($("#costo_txt").val().trim());

    if (costo <= 0) {
        mensaje_dialogo_info_ERROR("El costo debe ser mayor a cero", "ATENCION");
        return;
    }

    let materialRepetido = false; // Variable para detectar si el material ya existe

    $("#nota_credito_compra tr").each(function () {


        if ($(this).find("td:eq(0)").text() === $("#material_lst").val()) {
            mensaje_dialogo_info_ERROR("El material ya ha sido agregado anteriormente", "ATENCION");
            materialRepetido = true; // Marca como repetido
            return false; // Rompe el ciclo
        }
    });

// Si no se encontró material repetido, agrega una nueva fila
    if (!materialRepetido) {
        $("#nota_credito_compra").append(`
        <tr>
            <td>${$("#material_lst").val()}</td>
            <td>${$("#material_lst option:selected").html().split(" | ")[0]}</td>
            <td>${$("#material_lst option:selected").html().split(" | ")[1].replace("Tipo: ", "")}</td>
            <td><input class="form-control costo-presu" value="${formatearNumero($("#costo_txt").val())}"></td>
            <td>${$("#cantidad_txt").val()}</td>
            <td>${formatearNumero(cantidad * costo)}</td>
            <td><input type="checkbox" class="seleccion-nota"></td>
        </tr>
    `);
        calcularTotalNotaCreditoCompra();
    }
}
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
function calcularTotalNotaCreditoCompra() {
    let total = 0;
    let totaliva5 = 0;
    let totaliva10 = 0;
    let totalexenta = 0;
    $("#nota_compra tr").each(function (evt) {


        if ($(this).find("input").filter(":eq(1)").prop("checked")) {

            totalexenta += quitarDecimalesConvertir($(this).find("td:eq(4)").text());
            totaliva5 += quitarDecimalesConvertir($(this).find("td:eq(5)").text());
            totaliva10 += quitarDecimalesConvertir($(this).find("td:eq(6)").text());

            total += quitarDecimalesConvertir($(this).find("td:eq(4)").text()) +
                    quitarDecimalesConvertir($(this).find("td:eq(5)").text()) +
                    quitarDecimalesConvertir($(this).find("td:eq(6)").text());
        }

    });

    $("#total_exenta").text(formatearNumero(totalexenta));
    $("#total_iva5").text(formatearNumero(totaliva5));
    $("#total_iva10").text(formatearNumero(totaliva10));
    $("#total").text(formatearNumero(total));


    let intervalo = quitarDecimalesConvertir($("#intervalo").val());
    $("#monto_cuota").val(formatearNumero(Math.round(total / intervalo)));
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".remover-item-nota_credito", function (evt) {
    let tr = $(this).closest("tr");
    Swal.fire({
        title: "ATENCION",
        text: "Desea remover el registro?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Si"
    }).then((result) => {
        if (result.isConfirmed) {
            $(tr).remove();
            calcularTotalNotaCreditoCompra();
        }
    });
});

//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
function guardarNotaCreditoCompra() {
    
    if($("#total").text().trim() === "0"){
        mensaje_dialogo_info_ERROR("No hay datos para guardar", "ATENCION");
        return;
    }
    
    if ($("#facturas_compra_lst").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar una factura de compra", "ATENCION");
        return;
    }

    if ($("#fecha").val() < dameFechaActualSQL()) {
        mensaje_dialogo_info_ERROR("La fecha no puede ser menor a la fecha actual", "ATENCION");
        return;
    }

    if ($("#nro_factura").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Ingresar un nro de factura", "ATENCION");
        return;
    }

    if ($("#timbrado").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Ingresar un nro de timbrado", "ATENCION");
        return;
    }

    if ($("#fecha_venc").val() < dameFechaActualSQL()) {
        mensaje_dialogo_info_ERROR("La fecha de vencimiento no puede ser menor a la actual", "ATENCION");
        return;
    }



    let cabecera = {
        'cod_nota_compra': $("#cod").val(),
        'cod_compra': $("#facturas_compra_lst").val(),
        'fecha_nota': $("#fecha").val(),
        'tipo_nota': $("#tipo_lst").val(),
        'timbrado': $("#timbrado").val(),
        'motivo': $("#motivo_txt").val(),
        'nro_factura': $("#nro_factura").val(),
        'fecha_venc_timbrado': $("#fecha_venc").val(),
        'estado': 'ACTIVO'
    };

    console.log(cabecera);

    let response = ejecutarAjax("controladores/nota_compra.php", "guardar=" + JSON.stringify(cabecera));

    console.log("CABECERA -> " + response);

    //detalle
    $("#nota_compra tr").each(function (evt) {
         if ($(this).find("input").filter(":eq(1)").prop("checked")) {
             
            let detalle = {
                'cod_nota_compra': $("#cod").val(),
                'cod_producto': $(this).find("td:eq(0)").text(),
                'costo': quitarDecimalesConvertir($(this).find("td:eq(2)").text()),
                'cantidad': $(this).find("input:eq(0)").val()
            };
            console.log(detalle);
            response = ejecutarAjax("controladores/nota_compra_det.php", "guardar=" + JSON.stringify(detalle));

            console.log("DETALLE -> " + response);
         }
    });
    mensaje_confirmacion("Se ha guardado correctamente, GUARDADO");

    mostrarListarNotaCreditoCompra();
}

//------------------------------------------------------------------------------
function cargarTablaNotaCreditoCompra() {
    let data = ejecutarAjax("controladores/nota_compra.php", "leer=1");

    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_nota_compra}</td>`;
            fila += `<td>${item.fecha_nota}</td>`;
            fila += `<td>${item.nro_factura}</td>`;
            fila += `<td>${item.razon_social_prov}</td>`;
            fila += `<td>${item.tipo_nota}</td>`;
            fila += `<td>${item.motivo}</td>`;
            fila += `<td>${formatearNumero(item.total)}</td>`;
            fila += `<td><span class="badge badge-${(item.estado === "PENDIENTE") ? 'info' : (item.estado === "ANULADO") ? 'danger' : 'success'}">${item.estado}</span></td>`;
            fila += `<td>
                        <button onclick="imprimirNotaCreditoCompra(${item.cod_nota_compra}); return false;" class='btn btn-warning btn-sm imprimir-nota_credito'><i class='typcn typcn-printer'></i></button>
                        <button ${(item.estado === "CONFIRMADO" || item.estado === "ANULADO") ? "disabled" : ""} class='btn btn-danger btn-sm anular-nota_credito'><i class='typcn typcn-delete'></i></button>
                    </td>`;
            
            
            fila += `</tr>`;
        });
    }

    $("#nota_compra").html(fila);
}
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
function imprimirNotaCreditoCompra(id) {
    window.open("paginas/movimientos/compra/nota_credito/print.php?id=" + id);
}
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
$(document).on("change", "#facturas_compra_lst", function (evt) {
    if ($("#facturas_compra_lst").val() === "0") {
        $("#nota_credito").html("");
        $("#proveedor_compra_lst").val("0");

    } else {
        let cabecera = ejecutarAjax("controladores/factura_compra.php", "id=" + $("#facturas_compra_lst").val());
        console.log(cabecera);

        let json_cabecera = JSON.parse(cabecera);
        $("#id_proveedor").val(json_cabecera['cod_proveedor']);
        $("#proveedor").val(json_cabecera['razon_social_prov']);

        let data = ejecutarAjax("controladores/factura_compra_detalle.php", "id=" + $("#facturas_compra_lst").val());
        console.log(data);
        let fila = "";
        if (data === "0") {
            $("#nota_compra").html("");
        } else {
            let json_data = JSON.parse(data);

            $("#nota_compra").html("");
            json_data.map(function (item) {
                $("#nota_compra").append(`
                    <tr>
                        <td>${item.cod_producto}</td>
                        <td>${item.nombre_producto}</td>
                        <td>${formatearNumero(item.costo)}</td>
                        <td><input type='number' max="${item.cantidad}" class="cantidad-nota form-control" value='${item.cantidad}' min="1"></td>
                        <td>${formatearNumero(item.exenta)}</td>
                        <td>${formatearNumero(item.iva5)}</td>
                        <td>${formatearNumero(item.iva10)}</td>
                        <td hidden>${(item.tipo_iva)}</td>
                         <td><input type="checkbox" class="seleccion-nota"></td>
                    </tr>
                `);
            });
        }
    }
    calcularTotalNotaCreditoCompra();
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("change", ".cantidad-nota", function (evt) {
    let cantidad = quitarDecimalesConvertir($(this).val());
    let costo = quitarDecimalesConvertir($(this).closest("tr").find("td:eq(2)").text());
    let impuesto = $(this).closest("tr").find("td:eq(7)").text();

    if (impuesto === "0") {
        $(this).closest("tr").find("td:eq(4)").text(formatearNumero(costo * cantidad));
    }
    if (impuesto === "5") {
        $(this).closest("tr").find("td:eq(5)").text(formatearNumero(costo * cantidad));
    }
    if (impuesto === "10") {
        $(this).closest("tr").find("td:eq(6)").text(formatearNumero(costo * cantidad));
    }
    calcularTotalNotaCreditoCompra();

});

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".anular-nota_credito", function (evt) {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    Swal.fire({
        title: "Atencion",
        text: "Desea anular el registro?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Si"
    }).then((result) => {
        if (result.isConfirmed) {
            let response = ejecutarAjax("controladores/nota_compra.php",
                    "anular=" + id);


            console.log(response);
            mensaje_confirmacion("Anulado Correctamente", "Eliminado");
            mostrarListarNotaCreditoCompra();
        }
    });
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function cargarListaFacturasComprasActivas(componente) {
    let datos = ejecutarAjax("controladores/factura_compra.php", "leer_activo=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona una factura de compra</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_compra}'>Nro Factura ${item.nro_factura} | ${item.razon_social_prov}  | Total: ${formatearNumero(item.total)}</option>`;
        });
    }
    $(componente).html(option);
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("change", "#condicion_lst", function (evt) {
    if ($("#condicion_lst").val() === "CONTADO") {
        $(".bloque-credito").attr("hidden", true);
    } else {

        $(".bloque-credito").removeAttr("hidden");
    }
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("change", "#intervalo", function (evt) {
    let intervalo = quitarDecimalesConvertir($("#intervalo").val());
    let total = quitarDecimalesConvertir($("#total").text());
    $("#monto_cuota").val(formatearNumero(Math.round(total / intervalo)));
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".seleccion-nota", function (evt) {
    calcularTotalNotaCreditoCompra();
});