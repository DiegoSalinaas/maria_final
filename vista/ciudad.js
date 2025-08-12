function mostrarListarCiudad() {
    let contenido = dameContenido("paginas/referenciales/ciudad/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaCiudad();
}
//----------------------------------------------------------------------------
function mostrarAgregarCiudad() {
    let contenido = dameContenido("paginas/referenciales/ciudad/agregar.php");
    $(".contenido-principal").html(contenido);
    let ultimo = ejecutarAjax("controladores/ciudad.php", "ultimo_registro=1");
    if (ultimo === "0") {
        $("#cod").val("1");
    } else {
        let json_ultimo = JSON.parse(ultimo);
        $("#cod").val(parseInt(json_ultimo['cod_ciudad']) + 1);
    }
}
//---------------------------------------------------------------------------
function guardarCiudad() {
    if ($("#nombre_ciud").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Atención", "Debes ingresar la descripción de la ciudad");
        return false;
    }
    let data = {
        'cod_ciudad': $("#cod").val(),
        'nombre_ciud': $("#nombre_ciud").val(),
        'estado_ciud': $("#estado_ciud").val()
    };
    if ($("#id_ciudad").val() === "0") {
        ejecutarAjax("controladores/ciudad.php", "guardar=" + JSON.stringify(data));
        mensaje_confirmacion("Guardado correctamente", "Guardado");
    } else {
        data = {
            'cod_ciudad': $("#id_ciudad").val(),
            'nombre_ciud': $("#nombre_ciud").val(),
            'estado_ciud': $("#estado_ciud").val()
        };
        ejecutarAjax("controladores/ciudad.php", "actualizar=" + JSON.stringify(data));
        mensaje_confirmacion("Actualizado Correctamente", "Actualizado");
    }
    mostrarListarCiudad();
}
//------------------------------------------------------------------------------
function cargarTablaCiudad() {
    let data = ejecutarAjax("controladores/ciudad.php", "leer=1");
    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_ciudad}</td>`;
            fila += `<td>${item.nombre_ciud}</td>`;
            fila += `<td>${item.estado_ciud}</td>`;
            fila += `<td>`;
            fila += `<button class='btn btn-warning editar-ciudad'><i class='fa fa-edit'></i> Editar</button>`;
            fila += ` <button class='btn btn-danger eliminar-ciudad'><i class='fa fa-trash'></i> Eliminar</button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#ciudad_tb").html(fila);
}
//------------------------------------------------------------------------------
$(document).on("click", ".editar-ciudad", function (evt) {
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
            let response = ejecutarAjax("controladores/ciudad.php", "id=" + id);
            if (response !== "0") {
                let contenido = dameContenido("paginas/referenciales/ciudad/agregar.php");
                $(".contenido-principal").html(contenido);
                let json_registro = JSON.parse(response);
                $("#id_ciudad").val(json_registro['cod_ciudad']);
                $("#cod").val(json_registro['cod_ciudad']);
                $("#nombre_ciud").val(json_registro['nombre_ciud']);
                $("#estado_ciud").val(json_registro['estado_ciud']);
            }
        }
    });
});
//------------------------------------------------------------------------------
$(document).on("click", ".eliminar-ciudad", function (evt) {
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
            ejecutarAjax("controladores/ciudad.php", "eliminar=" + id);
            mensaje_confirmacion("Eliminado Correctamente", "Eliminado");
            cargarTablaCiudad();
        }
    });
});
