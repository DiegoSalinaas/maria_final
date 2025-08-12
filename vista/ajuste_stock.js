function mostrarListarAjusteStock() {
    let contenido = dameContenido("paginas/movimientos/compra/ajuste_stock/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaAjusteStock("#ajuste_stock_compra");
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function mostrarAgregarAjusteStock() {
    let contenido = dameContenido("paginas/movimientos/compra/ajuste_stock/agregar.php");
    $(".contenido-principal").html(contenido);
    cargarListaInsumo("#material_lst");
    dameFechaActual("fecha");
   
    $("#sucursal_lst").val("1");
    //obtener ultimo id
    let ultimo = ejecutarAjax("controladores/ajuste_stock.php", "ultimo_registro=1");
    if (ultimo === "0") {
        $("#cod").val("1");
    } else {
        let json_ultimo = JSON.parse(ultimo);
        $("#cod").val(quitarDecimalesConvertir(json_ultimo['cod_ajuste']) + 1);
    }

}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function cancelarAjusteStock() {
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
            mostrarListarAjusteStock();
        }
    });
   
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function agregarTablaAjusteStock() {
    if ($("#material_lst").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar un material", "ATENCION");
        return;
    }

    if ($("#tipo").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes ingresar una cantidad", "ATENCION");
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

    $("#ajuste_stock_compra tr").each(function () {


        if ($(this).find("td:eq(0)").text() === $("#material_lst").val()) {
            mensaje_dialogo_info_ERROR("El material ya ha sido agregado anteriormente", "ATENCION");
            materialRepetido = true; // Marca como repetido
            return false; // Rompe el ciclo
        }
    });

// Si no se encontró material repetido, agrega una nueva fila
    if (!materialRepetido) {
        $("#ajuste_stock_compra").append(`
        <tr>
            <td>${$("#material_lst").val()}</td>
            <td>${$("#material_lst option:selected").html()}</td>
            <td>${$("#tipo option:selected").html()}</td>
            <td><input type="number" min="1" class='form-control' value="${$("#cantidad_txt").val()}"></td>

            <td>
                <button class="btn btn-danger remover-item-ajuste_stock">Remover</button>
            </td>
        </tr>
    `);
    }
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".remover-item-ajuste_stock", function (evt) {
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
function guardarAjusteStock(){
    if($("#sucursal_lst").val() === "0"){
        mensaje_dialogo_info_ERROR("Debes seleccionar una sucursal", "ATENCION");
        return;
    }
    
    if($("#deposito_lst").val() < "0"){
        mensaje_dialogo_info_ERROR("Debes seleccionar un deposito para la operación", "ATENCION");
        return;
    }
    
    if($("#fecha").val() < dameFechaActualSQL()){
        mensaje_dialogo_info_ERROR("La fecha no puede ser menor a la fecha actual", "ATENCION");
        return;
    }
    
    let cabecera = {
        'cod_ajuste_stock' : $("#cod").val(),
        'fecha_ajuste' : $("#fecha").val()
    };
    
    let response = ejecutarAjax("controladores/ajuste_stock.php", "guardar="+JSON.stringify(cabecera));
    
    console.log("CABECERA -> "+response);

    //detalle
    $("#ajuste_stock_compra tr").each(function(evt) {
        let detalle = {
            'cod_ajuste_stock' : $("#cod").val(),
            'cod_material' : $(this).find("td:eq(0)").text(),
            'tipo' : $(this).find("td:eq(2)").text(),
            'cantidad_nueva' : $(this).find("input").val()
        };
        response = ejecutarAjax("controladores/ajuste_stock_detalle.php", "guardar="+JSON.stringify(detalle));
    
    console.log("DETALLE -> "+response);
    });
    mensaje_confirmacion ("Se ha guardado correctamente, GUARDADO")
    imprimirAjuste($("#cod").val());
    mostrarListarAjusteStock();
}

//------------------------------------------------------------------------------
function cargarTablaAjusteStock() {
    let data = ejecutarAjax("controladores/ajuste_stock.php", "leer=1");

    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_ajuste_stock}</td>`;
            fila += `<td>${item.fecha_ajuste}</td>`;
            fila += `<td>${item.nombre_apellido}</td>`;
            fila += `<td><span class="badge badge-${(item.estado === "PENDIENTE") ? 'info' : (item.estado === "ANULADO") ? 'danger' : 'success'}">${item.estado}</span></td>`;
            fila += `<td>
                        <button onclick="imprimirAjuste(${item.cod_ajuste_stock}); return false;" class='btn btn-warning btn-sm imprimir-ajuste_stock'><i class='typcn typcn-printer'></i></button>
                        <button ${(item.estado === "CONFIRMADO" || item.estado === "ANULADO") ? "disabled" : ""} class='btn btn-danger btn-sm anular-ajuste_stock'><i class='typcn typcn-delete'></i></button>
                    </td>`;
            
            
            fila += `</tr>`;
        });
    }

    $("#ajuste_compra").html(fila);
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".anular-ajuste_stock", function (evt) {
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
            let response = ejecutarAjax("controladores/ajuste_stock.php",
                    "anular=" + id);


            console.log(response);
            mensaje_confirmacion("Anulado Correctamente", "Eliminado");
            mostrarListarAjusteStock();
        }
    });
});

//$("#material_lst").change(function () {
//    let codMaterial = $(this).val();
//    if (codMaterial) {
//        let data = ejecutarAjax("controladores/ajuste_stock.php", "cod_material=" + codMaterial);
//        let materialInfo = JSON.parse(data);
//        $("#tipo_material").val(materialInfo.tipo_material); // Cargar tipo de material
//    }
//});

function cargarListaPedidoPendientes2(componente) {
    let datos = ejecutarAjax("controladores/ajuste_stock.php", "ajuste_stocks_pendientes=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona un ajuste_stock</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_ajuste_stock_compra}'>Nro Pedido ${item.cod_ajuste_stock_compra} | ${item.nombre_apellido} | ${item.suc_descripcion} </option>`;
        });
    }
    $(componente).html(option);
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function imprimirAjuste(id){
    window.open("paginas/movimientos/compra/ajuste_stock/print.php?id="+id);
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
