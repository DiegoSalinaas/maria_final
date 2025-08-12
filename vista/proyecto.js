function mostrarListarProyecto() {
    let contenido = dameContenido("paginas/referenciales/proyecto/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaProyecto();
}
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
function mostrarAgregarProyecto() {
    let contenido = dameContenido("paginas/referenciales/proyecto/agregar.php");
    $(".contenido-principal").html(contenido);

    let ultimo = ejecutarAjax("controladores/proyecto.php", "ultimo_registro=1");
    if (ultimo === "0") {
        $("#cod").val("1");
    } else {
        let json_ultimo = JSON.parse(ultimo);
        $("#cod").val(quitarDecimalesConvertir(json_ultimo['cod_producto']) + 1);
    }
    cargarListaMarca("#marca_lst");
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
function guardarProyecto() {
    if ($("#nombre_proyecto").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Atención", "Debes ingresar el nombre");
        return false;
    }
    if ($("#descripcion_proyecto").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Atención", "Debes ingresar la descripción");
        return false;
    }
    if ($("#precio_proyecto").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Atención", "Debes ingresar el precio");
        return false;
    }
    if ($("#marca_lst").val() === "0") {
        mensaje_dialogo_info_ERROR("Atención", "Debes seleccionar una marca");
        return false;
    }

    let data = {
        'cod_producto': $("#cod").val(),
        'nombre': $("#nombre_proyecto").val(),
        'descripcion': $("#descripcion_proyecto").val(),
        'precio': $("#precio_proyecto").val(),
        'cod_marca': $("#marca_lst").val(),
        'estado': "ACTIVO"
    };

    if ($("#id_proyecto").val() === "0") {
        let response = ejecutarAjax("controladores/proyecto.php", "guardar=" + JSON.stringify(data));
        mensaje_confirmacion("Guardado correctamente", "Guardado");
        mostrarListarProyecto();
    } else {
        data['cod_producto'] = $("#id_proyecto").val();
        let response = ejecutarAjax("controladores/proyecto.php", "actualizar=" + JSON.stringify(data));
        mensaje_confirmacion("Actualizado Correctamente", "Actualizado");
        mostrarListarProyecto();
    }
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function cargarTablaProyecto() {
    let data = ejecutarAjax("controladores/proyecto.php", "leer=1");
    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_producto}</td>`;
            fila += `<td>${item.nombre}</td>`;
            fila += `<td>${item.descripcion}</td>`;
            fila += `<td>${formatearNumero(item.precio)}</td>`;
            fila += `<td>${item.marca}</td>`;
            fila += `<td>${item.estado}</td>`;
            fila += `<td><button class='btn btn-warning editar-proyecto'><i class='fa fa-edit'></i> Editar</button>
                        <button class='btn btn-danger eliminar-proyecto'><i class='fa fa-trash'></i> Eliminar</button></td>`;
            fila += `</tr>`;
        });
    }
    $("#proyecto_tb").html(fila);
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".editar-proyecto", function () {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    Swal.fire({
        title: "Atencion",
        text: "Desea editar el registro?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Si",
    }).then((result) => {
        if (result.isConfirmed) {
            let response = ejecutarAjax("controladores/proyecto.php", "id=" + id);
            if (response !== "0") {
                let contenido = dameContenido("paginas/referenciales/proyecto/agregar.php");
                $(".contenido-principal").html(contenido);
                cargarListaMarca("#marca_lst");
                let json_registro = JSON.parse(response);
                $("#id_proyecto").val(json_registro['cod_producto']);
                $("#cod").val(json_registro['cod_producto']);
                $("#nombre_proyecto").val(json_registro['nombre']);
                $("#descripcion_proyecto").val(json_registro['descripcion']);
                $("#precio_proyecto").val(json_registro['precio']);
                $("#marca_lst").val(json_registro['cod_marca']);
            }
        }
    });
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".eliminar-proyecto", function () {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    Swal.fire({
        title: "Atencion",
        text: "Desea eliminar el registro?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Si",
    }).then((result) => {
        if (result.isConfirmed) {
            let response = ejecutarAjax("controladores/proyecto.php", "eliminar=" + id);
            mensaje_confirmacion("Eliminado Correctamente", "Eliminado");
            cargarTablaProyecto();
        }
    });
});

$(document).on("keyup", "#b_proyecto", function (evt) {
    let data = ejecutarAjax(
        "controladores/proyecto.php",
        "leer_descripcion_proyecto=" + encodeURIComponent($("#b_proyecto").val())
    );

    if (!data || data === "0") {
        $("#proyecto_tb").html("<tr><td colspan='7'>NO HAY REGISTROS</td></tr>");
        return;
    }

    let json_data = JSON.parse(data);
    let fila = "";
    json_data.forEach(function(item) {
        fila += `<tr>`;
        fila += `<td>${item.cod_producto}</td>`;
        fila += `<td>${item.nombre}</td>`;
        fila += `<td>${item.descripcion}</td>`;
        fila += `<td>${item.precio}</td>`;
        fila += `<td>${item.cod_marca}</td>`;
        fila += `<td>${item.estado}</td>`;
        fila += `<td>
                    <button class='btn btn-warning editar-producto'><i class='fa fa-edit'></i> Editar</button>
                    <button class='btn btn-danger eliminar-producto'><i class='fa fa-trash'></i> Eliminar</button>
                </td>`;
        fila += `</tr>`;
    });

    $("#proyecto_tb").html(fila);
});
