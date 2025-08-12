function cargarListaTipoEquipo(componente) {
    let datos = ejecutarAjax("controladores/equipo.php", "leer_activos_tipo=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona un tipo de equipo</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_tipo}'>${item.nombre_tipo}</option>`;
        });
    }
    $(componente).html(option);
}

function mostrarListarEquipo() {
    let contenido = dameContenido("paginas/referenciales/equipo/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaEquipo();
}
function mostrarAgregarEquipo() {
    let contenido = dameContenido("paginas/referenciales/equipo/agregar.php");
    $(".contenido-principal").html(contenido);

    //obtener ultimo id
    let ultimo = ejecutarAjax("controladores/equipo.php", "ultimo_registro=1");
    console.log(ultimo);
    if (ultimo === "0") {
        $("#cod").val("1");
    } else {
        let json_ultimo = JSON.parse(ultimo);
        $("#cod").val(quitarDecimalesConvertir(json_ultimo['cod_equipo']) + 1);


    }
    cargarListaTipoEquipo("#tipo_equipo");
    cargarListaColor("#color_lst");
    cargarListaModelo("#modelo_lst");
}


function cargarListaColor(componente) {
    let datos = ejecutarAjax("controladores/equipo.php", "leer_activos_equipo=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona un color</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_color}'>${item.descripcion}</option>`;
        });
    }
    $(componente).html(option);
}

function cargarListaModelo(componente) {
    let datos = ejecutarAjax("controladores/equipo.php", "leer_activos_modelo=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona un modelo</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.id_modelo}'>${item.descripcion}</option>`;
        });
    }
    $(componente).html(option);
}

function guardarEquipo() {



    if ($("#tipo_equipo").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar una tipo equipo", "ATENCION");
        return;
    }
    if ($("#color_lst").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar un color", "ATENCION");
        return;
    }
    if ($("#modelo_lst").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar un modelo", "ATENCION");
        return;
    }



    if ($("#id_equipo").val() === "0") {
        let cabecera = {
            'cod_equipo': $("#cod").val(),
            'estado': "ACTIVO",
            'id_color': $("#color_lst").val(),
            'id_tipo_equipo': $("#tipo_equipo").val(),
            'id_modelo': $("#modelo_lst").val()
        };
        console.log(cabecera);

        let response = ejecutarAjax("controladores/equipo.php", "guardar=" + JSON.stringify(cabecera));

        console.log("CABECERA -> " + response);
        mensaje_confirmacion("Se ha guardado correctamente, GUARDADO");
    } else {
        let cabecera2 = {
            'cod_equipo': $("#id_equipo").val(),
            'estado': "ACTIVO",
            'id_color': $("#color_lst").val(),
            'id_tipo_equipo': $("#tipo_equipo").val(),
            'id_modelo': $("#modelo_lst").val()
        };
        console.log(cabecera2);
        let response2 = ejecutarAjax("controladores/equipo.php", "actualizar=" + JSON.stringify(cabecera2));

        console.log("CABECERA2 -> " + response2);
        mensaje_confirmacion("Se ha actualizado correctamente, ACTUALIZADO");

    }



    $("#id_equipo").val("0");
    mostrarListarEquipo();
}

function cargarTablaEquipo() {
    let data = ejecutarAjax("controladores/equipo.php", "leer=1");

    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_equipo}</td>`;
            fila += `<td>${item.nombre_tipo}</td>`;
            fila += `<td>${item.descripcion}</td>`;
            fila += `<td>${item.nom_modelo}</td>`;
            fila += `<td><span class="badge badge-${(item.estado === "ACTIVO") ? 'success' : (item.estado === "DESACTIVADO") ? 'danger' : 'success'}">${item.estado}</span></td>`;
            fila += `<td>
                      
                        <button class='btn btn-warning editar-equipo btn-sm' ><i class='typcn typcn-edit'></i></button>
                        <button class='btn btn-danger eliminar-equipo btn-sm' ><i class='typcn typcn-delete'></i></button>
                    </td>`;
            fila += `</tr>`;
        });
    }

    $("#equipo").html(fila);
}


$(document).on("click", ".eliminar-equipo", function (evt) {
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
            let response = ejecutarAjax("controladores/equipo.php",
                    "eliminar=" + id);
            console.log(response);

            //borrado logico
            if (response.includes("Cannot delete or update a parent row: a foreign key constraint fails")) {
                var r = ejecutarAjax("controladores/equipo.php", "desactivar=" + id);

            }
            mensaje_confirmacion("Eliminado correstamente", "Eliminado");
            mostrarListarEquipo();
        }

    });
});


$(document).on("click", ".editar-equipo", function (evt) {
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
            let response = ejecutarAjax("controladores/equipo.php", "id=" + id);
            console.log(response);
            if (response === "0") {

            } else {
                let json_data = JSON.parse(response);
                console.log(response);
                //abrir ventana
                let contenido = dameContenido("paginas/referenciales/equipo/agregar.php");
                $(".contenido-principal").html(contenido);
                
                cargarListaTipoEquipo("#tipo_equipo");
                cargarListaColor("#color_lst");
                cargarListaModelo("#modelo_lst");


                //cargar los datos
                let json_registro = JSON.parse(response);
                //y guardas aca en tu input hidden creado
                $("#id_equipo").val(json_registro['cod_equipo']);
                $("#tipo_equipo").val(json_registro['id_tipo_equipo']);
                $("#color_lst").val(json_registro['id_color']);
                $("#modelo_lst").val(json_registro['id_modelo']);


            }
        }
    });
});