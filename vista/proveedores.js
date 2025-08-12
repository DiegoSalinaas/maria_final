function mostrarListarProveedores() {
    let contenido = dameContenido("paginas/referenciales/proveedor/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaProveedores();
    console.log(contenido);
}
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
function mostrarAgregarProveedores() {
    let contenido = dameContenido("paginas/referenciales/proveedor/agregar.php");
    $(".contenido-principal").html(contenido);
    
    
     let ultimo = ejecutarAjax("controladores/proveedores.php", "ultimo_registro=1");
    console.log(ultimo);
    if (ultimo === "0") {
        $("#cod").val("1");
    } else {
        let json_ultimo = JSON.parse(ultimo);
        $("#cod").val(quitarDecimalesConvertir(json_ultimo['cod_proveedor']) + 1);


    }
    cargarListaCiudadCliente("#ciudad_lst");
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
//function cancelarProveedores() {
//    Swal.fire({
//        title: "Atencion",
//        text: "Desea cancelar la operacion?",
//        icon: "question",
//        showCancelButton: true,
//        confirmButtonColor: "#3085d6",
//        cancelButtonColor: "#d33",
//        cancelButtonText: "No",
//        confirmButtonText: "Si"
//    }).then((result) => {
//        if (result.isConfirmed) {
//            let contenido = dameContenido("paginas/referenciales/proveedores/listar.php");
//            $(".contenido-principal").html(contenido);
//            cargarTablaProveedores();
//        }
//    });
//
//}


//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
function guardarProveedores2() {




    // Validar 'Razón Social'
    if ($("#nombre_proveedor").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Atención", "Debes ingresar la Razón Social del Proveedor");
        return false;
    }
    // Validar 'Razón Social'
    if ($("#ruc_proveedor").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Atención", "Debes ingresar el RUC del Proveedor");
        return false;
    }

    // Validar 'Teléfono'
    if ($("#telefono_proveedor").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Atención", "Debes ingresar el Teléfono del Proveedor");
        return false;
    }

    // Validar que el teléfono sea numérico
    if (isNaN($("#telefono_proveedor").val().trim())) {
        mensaje_dialogo_info_ERROR("Atención", "El Teléfono debe ser un valor numérico");
        return false;
    }


    // Validar 'Ciudad'
    if ($("#ciudad_lst").val() === "0") {
        mensaje_dialogo_info_ERROR("Atención", "Debes seleccionar una Ciudad");
        return false;
    }


    let data = {
        'cod_proveedor': $("#cod").val(),
        'pro_razonsocial': $("#nombre_proveedor").val(),
        'pro_ruc': $("#ruc_proveedor").val(),
        'pro_telef': $("#telefono_proveedor").val(),
        'estado_proveedor': "ACTIVO",
        'cod_ciudad': $("#ciudad_lst").val()

    };
    console.log(data);



    if ($("#id_proveedor").val() === "0") {

        let response = ejecutarAjax("controladores/proveedores.php", "guardar=" + JSON.stringify(data));
        console.log(response);
        mensaje_confirmacion("Guardado correctamente", "Guardado");
        mostrarListarProveedores();
    } else {
        let data = {
            'cod_proveedor': $("#id_proveedor").val(),
            'pro_razonsocial': $("#nombre_proveedor").val(),
            'pro_ruc': $("#ruc_proveedor").val(),
            'pro_telef': $("#telefono_proveedor").val(),
            'estado_proveedor': "ACTIVO",
            'cod_ciudad': $("#ciudad_lst").val()

        };
        let response = ejecutarAjax("controladores/proveedores.php",
                "actualizar=" + JSON.stringify(data));
        console.log(response);
        mensaje_confirmacion("Actualizado Correctamente", "Actualizado");
        mostrarListarProveedores();
    }




}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function cargarTablaProveedores() {
    let data = ejecutarAjax("controladores/proveedores.php", "leer=1");


    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_proveedor}</td>`;
            fila += `<td>${item.pro_razonsocial}</td>`;
            fila += `<td>${item.pro_ruc}</td>`;
            fila += `<td>${item.pro_telef}</td>`;
            fila += `<td>${item.nombre_ciud}</td>`;
            fila += `<td>${item.estado_proveedor}</td>`;
            fila += `<td>
                        <button class='btn btn-warning editar-proveedores'>Editar</button>
                        <button class='btn btn-danger eliminar-proveedores'>Eliminar</i></button>
                    </td>`;
            fila += `</tr>`;
        });
    }

    $("#proveedor_tb").html(fila);
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".editar-proveedores", function (evt) {
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
            let response = ejecutarAjax("controladores/proveedores.php", "id=" + id);
            console.log(response);
            if (response === "0") {

            } else {
                let json_data = JSON.parse(response);
                //abrir ventana
                let contenido = dameContenido("paginas/referenciales/proveedor/agregar.php");
                $(".contenido-principal").html(contenido);



                //cargar los datos
                let json_registro = JSON.parse(response);
                $("#id_proveedor").val(id);
                $("#nombre_proveedor").val(json_registro['pro_razonsocial']);
                $("#ruc_proveedor").val(json_registro['pro_ruc']);
                $("#telefono_proveedor").val(json_registro['pro_telef']);
                cargarListaCiudadCliente("#ciudad_lst");
                $("#ciudad_lst").val(json_registro['cod_ciudad']);
            }
        }
    });
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".eliminar-proveedores", function (evt) {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    Swal.fire({
        title: "Atencion",
        text: "Desea eliminar el registro?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Si"
    }).then((result) => {
        if (result.isConfirmed) {
            let response = ejecutarAjax("controladores/proveedores.php",
                    "eliminar=" + id);

            if (response.includes("Cannot delete or update a parent row")) {
                let registro = ejecutarAjax("controladores/proveedores.php", "desactivar=" + id);
                console.log(registro);
            }

            console.log(response);
            mensaje_confirmacion("Eliminado Correctamente", "Eliminado");
            mostrarListarProveedores();
        }
    });
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("keyup", "#b_proveedor", function (evt) {
    let data = ejecutarAjax(
        "controladores/proveedores.php",
        "leer_descripcion_proveedores=" + $("#b_proveedor").val()
    );

    console.log(data);

    let fila = "";
    if (!data || data.length === 0) {
        fila = "<tr><td colspan='8'>NO HAY REGISTROS</td></tr>";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_proveedor}</td>`;
            fila += `<td>${item.pro_razonsocial}</td>`;
            fila += `<td>${item.pro_ruc}</td>`;
            fila += `<td>${item.pro_telef}</td>`;
            fila += `<td>${item.direccion_prov}</td>`;
            fila += `<td>${item.email_prov}</td>`;
            fila += `<td>${item.estado_proveedor}</td>`;
            fila += `<td>${item.cod_ciudad}</td>`;
            fila += `<td>
                        <button class='btn btn-warning editar-proveedores'><i class='fa fa-edit'></i> Editar</button>
                        <button class='btn btn-danger eliminar-proveedores'><i class='fa fa-trash'></i> Eliminar</button>
                    </td>`;
            fila += `</tr>`;
        });
    }

    $("#proveedor_tb").html(fila);
});


//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
function imprimirProveedor() {
    window.open("paginas/referenciales/proveedores/print.php");
}


function cargarListaServicioConPrecio(componente) {
    let datos = ejecutarAjax("controladores/proveedores.php", "leer_proveedores_activos=1");
    console.log(datos);
    let option = "";
    if (datos === "0") {
        option = "<option value='0'>Selecciona un Proveedor</option>";
    } else {
        option = "<option value='0'>Selecciona un Proveedor</option>";
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.id_servicio}-${item.costo}'>${item.descripcion}</option>`;


        });
    }
    $(componente).html(option);
}

function cargarListaProveedorActivos(componente) {
    let datos = ejecutarAjax("controladores/proveedores.php", "leer_activo=1");
    console.log(datos);
    let option = "";
    if (datos === "0") {
        option = "<option value='0'>Selecciona un Proveedor</option>";
    } else {
        option = "<option value='0'>Selecciona un Proveedor</option>";
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_proveedor}'>${item.pro_razonsocial} | RUC: ${item.pro_ruc}</option>`;


        });
    }
    $(componente).html(option);
}

