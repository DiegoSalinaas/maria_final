function mostrarListarRemision() {
    let contenido = dameContenido("paginas/movimientos/compra/remision/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaRemision();
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function mostrarAgregarRemision() {
    let contenido = dameContenido("paginas/movimientos/compra/remision/agregar.php");
    $(".contenido-principal").html(contenido);
    
    dameFechaActual("fecha");
    dameFechaActual("vencimiento");
    cargarListaFacturasComprasActivasRemision("#factura_compra_remision_lst");
    //obtener ultimo id
    let ultimo = ejecutarAjax("controladores/remision.php", "ultimo_registro=1");
    if (ultimo === "0" || ultimo.length === 0) {
        $("#cod").val("1");
    } else {
        let json_ultimo = JSON.parse(ultimo);
        $("#cod").val(quitarDecimalesConvertir(json_ultimo['cod_remision_comp']) + 1);


    }

}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function cancelarRemision() {
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
            mostrarListarRemision();
        }
    });
   
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function agregarTablaRemision() {
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

    let materialRepetido = false; // Variable para detectar si el material ya existe

    $("#remision_compra tr").each(function () {


        if ($(this).find("td:eq(0)").text() === $("#material_lst").val()) {
            mensaje_dialogo_info_ERROR("El material ya ha sido agregado anteriormente", "ATENCION");
            materialRepetido = true; // Marca como repetido
            return false; // Rompe el ciclo
        }
    });

// Si no se encontró material repetido, agrega una nueva fila
    if (!materialRepetido) {
        $("#remision_compra").append(`
        <tr>
            <td>${$("#material_lst").val()}</td>
            <td>${$("#material_lst option:selected").html().split(" | ")[0]}</td>
            <td>${$("#material_lst option:selected").html().split(" | ")[1].replace("Tipo: ", "")}</td>
            <td><input type="number" min="1" class='form-control' value="${$("#cantidad_txt").val()}"></td>

            <td>
                <button class="btn btn-danger remover-item-remision">Remover</button>
            </td>
        </tr>
    `);
    }
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".remover-item-remision", function (evt) {
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
        }
    });
});

//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
function guardarRemision(){
   
    
    if($("#fecha_remision").val() < dameFechaActualSQL()){
        mensaje_dialogo_info_ERROR("La fecha no puede ser menor a la fecha actual", "ATENCION");
        return;
    }
    
    if($("#nro_nota").val().trim().length === 0){
        mensaje_dialogo_info_ERROR("Debes ingresar un nro de nota", "ATENCION");
        return;
    }
    
    if($("#timbrado").val().trim().length === 0){
        mensaje_dialogo_info_ERROR("Debes ingresar un nro de timbrado", "ATENCION");
        return;
    }
    
    if($("#vencimiento").val() < dameFechaActualSQL()){
        mensaje_dialogo_info_ERROR("La fecha de vencimiento no puede ser menor a la fecha actual", "ATENCION");
        return;
    }
    
    if($("#motivo").val().trim().length === 0){
        mensaje_dialogo_info_ERROR("Debes ingresar un motivo", "ATENCION");
        return;
    }
    
    if($("#punto_salida").val().trim().length === 0){
        mensaje_dialogo_info_ERROR("Debes ingresar un punto de salida", "ATENCION");
        return;
    }
    
    if($("#punto_llegada").val().trim().length === 0){
        mensaje_dialogo_info_ERROR("Debes ingresar un punto de llegada", "ATENCION");
        return;
    }
    
    if($("#vehiculo_lst").val().trim().length === "0"){
        mensaje_dialogo_info_ERROR("Debes seleccionar un vehiculo", "ATENCION");
        return;
    }
    
    if($("#chofer").val().trim().length === "0"){
        mensaje_dialogo_info_ERROR("Debes seleccionar un chofer", "ATENCION");
        return;
    }
    
    
    
    
    let cabecera = {
        'cod_nota_remision' : $("#cod").val(),
        'fecha_registro' : $("#fecha").val(),
        'nro_nota' : $("#nro_nota").val(),
        'timbrado' : $("#timbrado").val(),
        'vencimiento' : $("#vencimiento").val(),
        'motivo' : $("#motivo").val(),
        'punto_salida' : $("#punto_salida").val(),
        'punto_llegada' : $("#punto_llegada").val(),
        'chofer' : $("#chofer").val(),
        'cod_compra' : $("#factura_compra_remision_lst").val(),
        'cod_vehiculo' : $("#vehiculo_lst").val(),
        'estado' : 'ACTIVO'
    };
    console.log(cabecera);
    let response = ejecutarAjax("controladores/remision.php", "guardar="+JSON.stringify(cabecera));
    
    console.log("CABECERA -> "+response);

    //detalle
    $("#ajuste_stock_compra tr").each(function(evt) {
        let detalle = {
            'cod_nota_remision' : $("#cod").val(),
            'cod_producto' : $(this).find("td:eq(0)").text(),
            'cantidad_factura' : $(this).find("td:eq(2)").text(),
            'cantidad' : $(this).find("input").val()
        };
        response = ejecutarAjax("controladores/remision_detalle.php", "guardar="+JSON.stringify(detalle));
    
    console.log("DETALLE -> "+response);
    });
    mensaje_confirmacion ("Se ha guardado correctamente, GUARDADO")
    //imprimirPedido($("#cod").val());
    mostrarListarRemision();
}

//------------------------------------------------------------------------------
function cargarTablaRemision() {
    let data = ejecutarAjax("controladores/remision.php", "leer=1");
    console.log(data);

    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_nota_remision}</td>`;
            fila += `<td>${item.fecha_registro}</td>`;
            fila += `<td>${item.nro_nota}</td>`;
            fila += `<td>${item.razon_social_prov}</td>`;
            fila += `<td>${item.nom_ape}</td>`;
            fila += `<td>${item.punto_salida}</td>`;
            fila += `<td>${item.punto_llegada}</td>`;
            fila += `<td><span class="badge badge-${(item.estado === "PENDIENTE") ? 'info' : (item.estado === "ANULADO") ? 'danger' : 'success'}">${item.estado}</span></td>`;
            fila += `<td>
                        <button onclick="imprimirRemision(${item.cod_nota_remision}); return false;" class='btn btn-warning btn-sm imprimir-remision'><i class='typcn typcn-printer'></i></button>
                        <button ${(item.estado === "CONFIRMADO" || item.estado === "ANULADO") ? "disabled" : ""} class='btn btn-danger btn-sm anular-remision'><i class='typcn typcn-delete'></i></button>
                    </td>`;
            
            
            fila += `</tr>`;
        });
    }
   

    $("#remision_compra").html(fila);
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".anular-remision", function (evt) {
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
            let response = ejecutarAjax("controladores/remision.php",
                    "anular=" + id);


            console.log(response);
            mensaje_confirmacion("Anulado Correctamente", "Eliminado");
            mostrarListarRemision();
        }
    });
});

//$("#material_lst").change(function () {
//    let codMaterial = $(this).val();
//    if (codMaterial) {
//        let data = ejecutarAjax("controladores/remision.php", "cod_producto=" + codMaterial);
//        let materialInfo = JSON.parse(data);
//        $("#tipo_material").val(materialInfo.tipo_material); // Cargar tipo de material
//    }
//});

function cargarListaPedidoPendientes2(componente) {
    let datos = ejecutarAjax("controladores/remision.php", "remisions_pendientes=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona un remision</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_remision_compra}'>Nro Pedido ${item.cod_remision_compra} | ${item.nombre_apellido} | ${item.suc_descripcion} </option>`;
        });
    }
    $(componente).html(option);
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function imprimirRemision(id){
    window.open("paginas/movimientos/compra/remision/print.php?id="+id);
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function cargarListaChofer(componente){
    let datos = ejecutarAjax("controladores/chofer.php", "leer_activo=1");
    console.log(datos);
    let option = "";
    if (datos === "0") {
        option = "<option value='0'>Selecciona un chofer</option>";
    } else {
        option = "<option value='0'>Selecciona un chofer</option>";
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_chofer}'>${item.nom_ape}</option>`;


        });
    }
    $(componente).html(option);
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function cargarListaFacturasComprasActivasRemision(componente) {
    let datos = ejecutarAjax("controladores/factura_compra.php", "leer_activo_remision=1");
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
$(document).on("change", "#factura_compra_remision_lst", function () {
    let facturaId = $(this).val();

    if (facturaId === "0") {
        // Limpiar campos de cabecera
        $("#proveedor").val("");
        $("#id_proveedor").val("");
        $("#timbrado").val("");
        $("#motivo").val("");
        $("#punto_salida").val("");
        $("#punto_llegada").val("");
        $("#ajuste_stock_compra").html(""); // limpiar tabla
        return;
    }

    // 1️⃣ Cargar cabecera de factura
    let cabecera = ejecutarAjax("controladores/factura_compra.php", "id=" + facturaId);
    if (cabecera === "0") {
        mensaje_dialogo_info_ERROR("No se encontraron datos de factura", "ATENCIÓN");
        return;
    }

    let json_cabecera = JSON.parse(cabecera);
    $("#id_proveedor").val(json_cabecera['cod_proveedor']);
    $("#proveedor").val(json_cabecera['razon_social_prov']);
    if (json_cabecera['timbrado']) $("#timbrado").val(json_cabecera['timbrado']);
    if (json_cabecera['motivo']) $("#motivo").val(json_cabecera['motivo']);
    if (json_cabecera['punto_salida']) $("#punto_salida").val(json_cabecera['punto_salida']);
    if (json_cabecera['punto_llegada']) $("#punto_llegada").val(json_cabecera['punto_llegada']);

    // 2️⃣ Cargar detalle y estirarlo en la tabla de ajuste
    let data = ejecutarAjax("controladores/factura_compra_detalle.php", "id=" + facturaId);
    $("#ajuste_stock_compra").html(""); // limpiar antes de insertar

    if (data !== "0") {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            $("#ajuste_stock_compra").append(`
                <tr>
                    <td>${item.cod_producto}</td>
                    <td>${item.nombre_producto}</td>
                    <td>${item.cantidad}</td>
                    <td><input type="number" min="1" class='form-control' value="${item.cantidad}"></td>
                </tr>
            `);
        });
    } else {
        $("#ajuste_stock_compra").html("<tr><td colspan='4'>No hay productos en esta factura</td></tr>");
    }
});
