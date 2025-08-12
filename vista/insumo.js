function cargarListaInsumo(componente) {
    let datos = ejecutarAjax("controladores/insumo.php", "leer_activos=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona un Insumo</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_insumos}'>${item.descripcion}</option>`;
        });
    }
    $(componente).html(option);
}

// Alias de \"cargarListaInsumo\" que muestra la lista como Productos
// Se reutiliza el mismo controlador y datos, pero se cambia el texto
// mostrado al usuario para mantener compatibilidad con otras partes del
// sistema que aún utilizan la denominación \"insumo\".
function cargarListaProducto(componente) {
    let datos = ejecutarAjax("controladores/insumo.php", "leer_activos=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona un Producto</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_insumos}'>${item.descripcion}</option>`;
        });
    }
    $(componente).html(option);
}

function mostrarListarInsumo() {
    let contenido = dameContenido("paginas/referenciales/insumo/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaInsumo();
}
function mostrarAgregarInsumo() {
    let contenido = dameContenido("paginas/referenciales/insumo/agregar.php");
    $(".contenido-principal").html(contenido);
    cargarListaMarca("#marca_insumo");
    cargarListaTipoIva("#tipo_iva_insumo");

    //obtener ultimo id
    let ultimo = ejecutarAjax("controladores/insumo.php", "ultimo_registro=1");
    console.log(ultimo);
    if (ultimo === "0") {
        $("#cod").val("1");
    } else {
        let json_ultimo = JSON.parse(ultimo);
        $("#cod").val(quitarDecimalesConvertir(json_ultimo['cod_insumos']) + 1);


    }
}


function cargarListaMarca(componente) {
    let datos = ejecutarAjax("controladores/insumo.php", "leer_activos_marca=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona una marca</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_marca}'>${item.descripcion}</option>`;
        });
    }
    $(componente).html(option);
}

function cargarListaTipoIva(componente) {
    let option = "<option value='0'>Exento</option>";
    option += "<option value='5'>I.V.A. 5%</option>";
    option += "<option value='10'>I.V.A. 10%</option>";
    $(componente).html(option);
}

function guardarInsumo() {

    if ($("#descripcion_lst").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes seleccionar una descripcion", "ATENCION");
        return;
    }

    if ($("#marca_insumo").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar una marca", "ATENCION");
        return;
    }
    if ($("#tipo_iva_insumo").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar un tipo de IVA", "ATENCION");
        return;
    }

    if ($("#costo_txt").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes seleccionar un costo", "ATENCION");
        return;
    }
    if ($("#precio_txt").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes seleccionar un precio", "ATENCION");
        return;
    }


    if ($("#id_insumo").val() === "0") {
        let cabecera = {
            'cod_insumos': $("#cod").val(),
            'descripcion': $("#descripcion_lst").val(),
            'cod_marca_insumos': $("#marca_insumo").val(),
            'tipo_iva': $("#tipo_iva_insumo").val(),
            'cantidad': $("#cantidad_txt").val(),
            'estado_insumos': 'ACTIVO',
            'costo': quitarDecimalesConvertir($("#costo_txt").val()),
            'precio': quitarDecimalesConvertir($("#precio_txt").val())
        };
        console.log(cabecera);

        let response = ejecutarAjax("controladores/insumo.php", "guardar=" + JSON.stringify(cabecera));

        console.log("CABECERA -> " + response);

    } else {
        let cabecera2 = {
            'cod_insumos': $("#id_insumo").val(),
            'descripcion': $("#descripcion_lst").val(),
            'cod_marca_insumos': $("#marca_insumo").val(),
            'tipo_iva': $("#tipo_iva_insumo").val(),
            'cantidad': $("#cantidad_txt").val(),
            'estado_insumos': 'ACTIVO',
            'costo': quitarDecimalesConvertir($("#costo_txt").val()),
            'precio': quitarDecimalesConvertir($("#precio_txt").val())
        };
        console.log(cabecera2);
        let response2 = ejecutarAjax("controladores/insumo.php", "actualizar=" + JSON.stringify(cabecera2));

        console.log("CABECERA2 -> " + response2);

    }

    
    mensaje_confirmacion("Se ha guardado correctamente, GUARDADO");
    $("#id_articulo").val("0");
    mostrarListarInsumo();
}

function cargarTablaInsumo() {
    let data = ejecutarAjax("controladores/insumo.php", "leer=1");

    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_insumos}</td>`;
            fila += `<td>${item.descripcion}</td>`;
            fila += `<td>${item.descripcion_marca}</td>`;
            fila += `<td>${descripcionTipoIva(item.tipo_iva)}</td>`;
            fila += `<td>${formatearNumero(item.precio)}</td>`;
            fila += `<td><span class="badge badge-${(item.estado_insumos === "ACTIVO") ? 'success' : (item.estado_insumos === "DESACTIVADO") ? 'danger' : 'success'}">${item.estado_insumos}</span></td>`;
            fila += `<td>
                      
                        <button class='btn btn-warning editar-insumo btn-sm' ><i class='typcn typcn-edit'></i></button>
                        <button class='btn btn-danger eliminar-insumo btn-sm' ><i class='typcn typcn-delete'></i></button>
                    </td>`;
            fila += `</tr>`;
        });
    }

    $("#insumo_compra").html(fila);
}

function descripcionTipoIva(valor) {
    if (valor == 10 || valor === '10') {
        return 'I.V.A. 10%';
    }
    if (valor == 5 || valor === '5') {
        return 'I.V.A. 5%';
    }
    return 'EXENTO';
}

$(document).on("keyup", "#costo_txt, #precio_txt", function (evt) {
    $(this).val(formatearNumero($(this).val()));
});

$(document).on("click", ".eliminar-insumo", function (evt) {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    Swal.fire({
        title: 'Estas seguro?',
        text: "Desea eliminar esta registro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'No',
        confirmButtonText: 'Si'
    }).then((result) => {
        if (result.isConfirmed) {
            //borrado fisico
            let response = ejecutarAjax("controladores/insumo.php",
                    "eliminar=" + id);
            console.log(response);

            //borrado logico
            if (response.includes("Cannot delete or update a parent row: a foreign key constraint fails")) {
                var r = ejecutarAjax("controladores/insumo.php", "desactivar=" + id);

            }
            mensaje_confirmacion("Eliminado correstamente", "Eliminado");
            mostrarListarInsumo();
        }

    });
});


$(document).on("click", ".editar-insumo", function (evt) {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    Swal.fire({
        title: "Atencion",
        text: "Desea editar el registro?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Si"
    }).then((result) => {
        if (result.isConfirmed) {
            //obtenes aca lo que seria tu registro
            let response = ejecutarAjax("controladores/insumo.php", "id=" + id);
            console.log(response);
            if (response === "0") {

            } else {
                let json_data = JSON.parse(response);
                console.log(response);
                //abrir ventana
                let contenido = dameContenido("paginas/referenciales/insumo/agregar.php");
                $(".contenido-principal").html(contenido);
                cargarListaMarca("#marca_insumo");
                cargarListaTipoIva("#tipo_iva_insumo");


                //cargar los datos
                let json_registro = JSON.parse(response);
                //y guardas aca en tu input hidden creado
                $("#id_insumo").val(json_registro['cod_insumos']);
                $("#descripcion_lst").val(json_registro['descripcion']);
                $("#marca_insumo").val(json_registro['cod_marca_insumos']);
                $("#tipo_iva_insumo").val(json_registro['tipo_iva']);
                $("#cantidad_txt").val(json_registro['cantidad']);
                $("#costo_txt").val(formatearNumero(json_registro['costo']));
                $("#precio_txt").val(formatearNumero(json_registro['precio']));


            }
        }
    });
});