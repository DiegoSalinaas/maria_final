function mostrarListarVehiculo() {
    let contenido = dameContenido("paginas/referenciales/vehiculo/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaVehiculo("#vehiculo_tb");
    console.log(contenido);
}
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
function mostrarAgregarVehiculo() {
    let contenido = dameContenido("paginas/referenciales/vehiculo/agregar.php");
    $(".contenido-principal").html(contenido);
    cargarListaMarca("#marca_lst")
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
function cancelarVehiculo() {
    Swal.fire({
        title: "Atencion",
        text: "Desea cancelar la operacion?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Si"
    }).then((result) => {
        if (result.isConfirmed) {
            let contenido = dameContenido("paginas/referenciales/vehiculo/listar.php");
            $(".contenido-principal").html(contenido);
            cargarTablaVehiculo();
        }
    });

}


//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
function guardarVehiculo() {

 
        // Validar 'Descripción'
        if ($("#descripcion").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes ingresar la Descripción del Vehículo");
            return false;
        }
    
        // Validar 'Modelo'
        if ($("#modelo").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes ingresar el Modelo del Vehículo");
            return false;
        }
    
        // Validar 'Marca'
        if ($("#marca_lst").val() === "" || $("#marca_lst").val() === "0") {
            mensaje_dialogo_info_ERROR("Atención", "Debes seleccionar una Marca");
            return false;
        }
    
        // Validar 'Color'
        if ($("#color").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes ingresar el Color del Vehículo");
            return false;
        }
    
        // Validar 'Placa'
        if ($("#placa").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes ingresar la Placa del Vehículo");
            return false;
        }
    
        // Validar 'Tipo de Vehículo'
        if ($("#tipo_vehiculo").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes seleccionar el Tipo de Vehículo");
            return false;
        }
    
        // Validar 'Fecha de Ingreso'
        if ($("#fecha_ingreso").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes ingresar la Fecha de Ingreso");
            return false;
        }
    
        // Validar 'Fecha de Salida'
        if ($("#fecha_salida").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes ingresar la Fecha de Salida");
            return false;
        }
    
    
    
     
    

   
    

    let data = {
        'descripcion': $("#descripcion").val(),
        'modelo': $("#modelo").val(),
        'cod_marca': $("#marca_lst").val(),
        'color': $("#color").val(),
        'placa': $("#placa").val(),
        'tipo_vehiculo': $("#tipo_vehiculo").val(),
        'fecha_salida': $("#fecha_salida").val(),
        'estado': $("#estado").val(),
        'fecha_ingreso': $("#fecha_ingreso").val()
        
    };
console.log(data);
    
    if($("#id_vehiculo").val() === "0"){
        
        let response = ejecutarAjax("controladores/vehiculo.php", "guardar=" + JSON.stringify(data));
//        console.log(response);
         mensaje_confirmacion("Guardado correctamente", "Guardado");
       mostrarListarVehiculo();
    }else{
        data = {...data , 'cod_vehiculo' : $("#id_vehiculo").val()};
         let response = ejecutarAjax("controladores/vehiculo.php",
         "actualizar=" + JSON.stringify(data));
//        console.log(response);
        mensaje_confirmacion("Actualizado Correctamente","Actualizado");
    }
    
    


}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function cargarTablaVehiculo() {
    let data = ejecutarAjax("controladores/vehiculo.php", "leer=1");


    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_vehiculo}</td>`;
            fila += `<td>${item.descripcion}</td>`;
            fila += `<td>${item.modelo}</td>`;
            fila += `<td>${item.descripcion_marcas}</td>`;
            fila += `<td>${item.color}</td>`;
            fila += `<td>${item.placa}</td>`;
            fila += `<td>${item.tipo_vehiculo}</td>`;
            fila += `<td>${item.fecha_ingreso}</td>`;
            fila += `<td>${item.fecha_salida}</td>`;
            fila += `<td>${item.estado}</td>`;
            fila += `<td>
                        <button class='btn btn-warning editar-vehiculo'><i class='fa fa-edit'></i> Editar</button>
                        <button class='btn btn-danger eliminar-vehiculo'><i class='fa fa-trash'></i> Eliminar</button>
                    </td>`;
            fila += `</tr>`;
        });
    }

    $("#vehiculo_tb").html(fila);
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".editar-vehiculo", function (evt) {
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
            let response = ejecutarAjax("controladores/vehiculo.php", "id=" + id);
            console.log(response);
            if (response === "0") {

            } else {
                let json_data = JSON.parse(response);
                //abrir ventana
                let contenido = dameContenido("paginas/referenciales/vehiculo/agregar.php");
                $(".contenido-principal").html(contenido);

    
                //cargar los datos
                let json_registro = JSON.parse(response);
                $("#id_vehiculo").val(id);
                $("#descripcion").val(json_registro['descripcion']);
                $("#modelo").val(json_registro['modelo']);
                $("#color").val(json_registro['color']);
                $("#placa").val(json_registro['placa']);
                $("#tipo_vehiculo").val(json_registro['tipo_vehiculo']);
                $("#fecha_ingreso").val(json_registro['fecha_ingreso']);
                $("#fecha_salida").val(json_registro['fecha_salida']);
                cargarListaMarca("#marca_lst")
                $("#marca_lst").val(json_registro['cod_marca']);
                $("#estado").val(json_registro['estado']);
            }
            
            
        }
        
    });
});

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".eliminar-vehiculo", function (evt) {
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
            let response = ejecutarAjax("controladores/vehiculo.php",
            "eliminar=" + id);

            console.log(response);
            mensaje_confirmacion("Eliminado Correctamente", "Eliminado");
            mostrarListarVehiculo();
        }
    });
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("keyup", "#b_vehiculo", function (evt) {
    let data = ejecutarAjax("controladores/vehiculo.php", "leer_descripcion_vehiculo="+$("#b_vehiculo").val());
console.log(data);

    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_vehiculo}</td>`;
            fila += `<td>${item.descripcion}</td>`;
            fila += `<td>${item.modelo}</td>`;
            fila += `<td>${item.descripcion_marcas}</td>`;
            fila += `<td>${item.color}</td>`;
            fila += `<td>${item.placa}</td>`;
            fila += `<td>${item.tipo_vehiculo}</td>`;
            fila += `<td>${item.fecha_ingreso}</td>`;
            fila += `<td>${item.fecha_salida}</td>`;
            fila += `<td>${item.estado}</td>`;
            fila += `<td>
                        <button class='btn btn-warning editar-vehiculo'><i class='fa fa-edit'></i> Editar</button>
                        <button class='btn btn-danger eliminar-vehiculo'><i class='fa fa-trash'></i> Eliminar</button>
                    </td>`;
            fila += `</tr>`;
        });
    }

    $("#vehiculo_tb").html(fila);
});
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
function imprimirVehiculo(){
    window.open("paginas/referenciales/vehiculo/print.php");
}


function cargarListaVehiculo(componente) {
    let datos = ejecutarAjax("controladores/vehiculo.php", "leer_activo=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona un vehiculo</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_vehiculo}'>${item.descripcion_marcas} ${item.modelo} ${item.placa}</option>`;
        });
    }
    $(componente).html(option);
}
