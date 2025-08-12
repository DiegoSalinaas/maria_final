function mostrarListarMarca() {
    let contenido = dameContenido("paginas/referenciales/marca/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaMarca();
}

function mostrarAgregarMarca() {
    let contenido = dameContenido("paginas/referenciales/marca/agregar.php");
    $(".contenido-principal").html(contenido);
    let ultimo = ejecutarAjax("controladores/marca.php", "ultimo_registro=1");
    if (ultimo === "0") {
        $("#cod").val("1");
    } else {
        let json_ultimo = JSON.parse(ultimo);
        $("#cod").val(parseInt(json_ultimo['cod_marca']) + 1);
    }
}

function guardarMarca() {
    if ($("#descripcion").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Atención", "Debes ingresar la descripción de la marca");
        return false;
    }
    let data = {
        'cod_marca': $("#cod").val(),
        'descripcion': $("#descripcion").val(),
        'estado': $("#estado").val()
    };
    if ($("#id_marca").val() === "0") {
        ejecutarAjax("controladores/marca.php", "guardar=" + JSON.stringify(data));
        mensaje_confirmacion("Guardado correctamente", "Guardado");
    } else {
        data = {
            'cod_marca': $("#id_marca").val(),
            'descripcion': $("#descripcion").val(),
            'estado': $("#estado").val()
        };
        ejecutarAjax("controladores/marca.php", "actualizar=" + JSON.stringify(data));
        mensaje_confirmacion("Actualizado Correctamente", "Actualizado");
    }
    mostrarListarMarca();
}

function cargarTablaMarca() {
    let data = ejecutarAjax("controladores/marca.php", "leer=1");
    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_marca}</td>`;
            fila += `<td>${item.descripcion}</td>`;
            fila += `<td>${item.estado}</td>`;
            fila += `<td>`;
            fila += `<button class='btn btn-warning editar-marca'><i class='fa fa-edit'></i> Editar</button>`;
            fila += ` <button class='btn btn-danger eliminar-marca'><i class='fa fa-trash'></i> Eliminar</button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#marca_tb").html(fila);
}

$(document).on("click", ".editar-marca", function (evt) {
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
            let response = ejecutarAjax("controladores/marca.php", "id=" + id);
            if (response !== "0") {
                let contenido = dameContenido("paginas/referenciales/marca/agregar.php");
                $(".contenido-principal").html(contenido);
                let json_registro = JSON.parse(response);
                $("#id_marca").val(json_registro['cod_marca']);
                $("#cod").val(json_registro['cod_marca']);
                $("#descripcion").val(json_registro['descripcion']);
                $("#estado").val(json_registro['estado']);
            }
        }
    });
});

$(document).on("click", ".eliminar-marca", function (evt) {
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
            ejecutarAjax("controladores/marca.php", "eliminar=" + id);
            mensaje_confirmacion("Eliminado Correctamente", "Eliminado");
            cargarTablaMarca();
        }
    });
});
