function mostrarListarMateriales() {
    let contenido = dameContenido("paginas/referenciales/materiales/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaMateriales("#materiales_tb");
    console.log(contenido);
}
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
function mostrarAgregarMaterial() {
    let contenido = dameContenido("paginas/referenciales/materiales/agregar.php");
    $(".contenido-principal").html(contenido);
    cargarListaTipoMaterial("#tipo_met_lst");
    cargarListaMarca("#marca_lst");
    
    
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
function cancelarMateriales() {
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
            let contenido = dameContenido("paginas/referenciales/materiales/listar.php");
            $(".contenido-principal").html(contenido);
            cargarTablaMateriales();
        }
    });

}


//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
function guardarMateriales() {

    
        // Validar 'Nombre del Material'
        if ($("#nombre_material").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes ingresar el Nombre del Material");
            return false;
        }
    
        // Validar 'Descripción del Material'
        if ($("#descripcion_material").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes ingresar la Descripción del Material");
            return false;
        }
    
        // Validar 'Costo del Material'
        if ($("#costo_material").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes ingresar el Costo del Material");
            return false;
        }
    
        // Validar que el costo sea un número
        if (isNaN($("#costo_material").val().trim()) || parseFloat($("#costo_material").val().trim()) <= 0) {
            mensaje_dialogo_info_ERROR("Atención", "El Costo del Material debe ser un número positivo");
            return false;
        }
    
        // Validar 'Precio del Material'
        if ($("#precio_material").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes ingresar el Precio del Material");
            return false;
        }
    
        // Validar que el precio sea un número
        if (isNaN($("#precio_material").val().trim()) || parseFloat($("#precio_material").val().trim()) <= 0) {
            mensaje_dialogo_info_ERROR("Atención", "El Precio del Material debe ser un número positivo");
            return false;
        }
    
        // Validar 'Fecha de Ingreso'
        if ($("#fecha_ingreso").val().trim().length === 0) {
            mensaje_dialogo_info_ERROR("Atención", "Debes ingresar la Fecha de Ingreso");
            return false;
        }
    
        // Validar 'Tipo de Material'
        if ($("#tipo_met_lst").val() === "" || $("#tipo_met_lst").val() === "0") {
            mensaje_dialogo_info_ERROR("Atención", "Debes seleccionar el Tipo de Material");
            return false;
        }

       
    
        // Validar 'Marca'
        if ($("#marca_lst").val() === "" || $("#marca_lst").val() === "0") {
            mensaje_dialogo_info_ERROR("Atención", "Debes seleccionar una Marca");
            return false;
        }
    
     
    
    
   
    

    let data = {
        'nombre_material': $("#nombre_material").val(),
        'descripcion_material': $("#descripcion_material").val(),
        'costo_material': $("#costo_material").val(),
        'precio_material': $("#precio_material").val(),
        'fecha_ingreso': $("#fecha_ingreso").val(),
        'cod_tipo_material': $("#tipo_met_lst").val(),
        'estado': $("#estado").val(),
        'cod_marca': $("#marca_lst").val()
        
    };
console.log(data);
    
    if($("#id_materiales").val() === "0"){
        
        let response = ejecutarAjax("controladores/materiales.php", "guardar=" + JSON.stringify(data));
//        console.log(response);
         mensaje_confirmacion("Guardado correctamente", "Guardado");
        mostrarListarMateriales();
    }else{
        data = {...data , 'cod_material' : $("#id_materiales").val()};
         let response = ejecutarAjax("controladores/materiales.php",
         "actualizar=" + JSON.stringify(data));
//        console.log(response);
        mensaje_confirmacion("Actualizado Correctamente","Actualizado");
        mostrarListarMateriales();
    }
       


}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function cargarTablaMateriales() {
    let data = ejecutarAjax("controladores/materiales.php", "leer=1");

    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_material}</td>`;
            fila += `<td>${item.nombre_material}</td>`;
            fila += `<td>${item.descripcion_material}</td>`;
            fila += `<td>${item.costo_material}</td>`;
            fila += `<td>${item.precio_material}</td>`;
            fila += `<td>${item.fecha_ingreso}</td>`;
            fila += `<td>${item.descripcion_tipo_material}</td>`;
            fila += `<td>${item.descripcion_marca}</td>`;
            fila += `<td>${item.estado}</td>`;
            fila += `<td>
                        <button class='btn btn-warning editar-materiales'><i class='fa fa-edit'></i></button>
                        <button class='btn btn-danger eliminar-materiales'><i class='fa fa-trash'></i></button>
                    </td>`;
            fila += `</tr>`;
        });
    }

    $("#materiales_tb").html(fila);
}


//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".editar-materiales", function (evt) {
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
            let response = ejecutarAjax("controladores/materiales.php", "id=" + id);
            console.log(response);
            if (response === "0") {

            } else {
                let json_data = JSON.parse(response);
                //abrir ventana
                let contenido = dameContenido("paginas/referenciales/materiales/agregar.php");
                $(".contenido-principal").html(contenido);

    
                        // Cargar los datos
                let json_registro = JSON.parse(response);
                console.log(json_registro);
                $("#id_materiales").val(id);
                $("#nombre_material").val(json_registro['nombre_material']);
                $("#descripcion_material").val(json_registro['descripcion_material']);
                $("#costo_material").val(json_registro['costo_material']);
                $("#precio_material").val(json_registro['precio_material']);
                $("#fecha_ingreso").val(json_registro['fecha_ingreso']);
                $("#estado").val(json_registro['estado']);

                // Cargar listas desplegables
                cargarListaMarca("#marca_lst");
                $("#marca_lst").val(json_registro['cod_marca']); // Cambia a 'cod_marca'

                cargarListaTipoMaterial("#tipo_met_lst");
                $("#tipo_met_lst").val(json_registro['cod_tipo_material']); // Cambia a 'cod_tipo_material'
            }
            
        }
    });
});

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".eliminar-materiales", function (evt) {
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
            let response = ejecutarAjax("controladores/materiales.php",
            "eliminar=" + id);

            console.log(response);
            mensaje_confirmacion("Eliminado Correctamente", "Eliminado");
            mostrarListarMateriales();
        }
    });
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("keyup", "#b_materiales", function (evt) {
    let data = ejecutarAjax("controladores/materiales.php", "leer_descripcion_materiales="+$("#b_materiales").val());

console.log(data);
    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_material}</td>`;
            fila += `<td>${item.nombre_material}</td>`;
            fila += `<td>${item.descripcion_material}</td>`;
            fila += `<td>${item.costo_material}</td>`;
            fila += `<td>${item.precio_material}</td>`;
            fila += `<td>${item.fecha_ingreso}</td>`;
            fila += `<td>${item.descripcion_tipo_material}</td>`;
            fila += `<td>${item.descripcion_marca}</td>`;
            fila += `<td>${item.estado}</td>`;
            fila += `<td>
                        <button class='btn btn-warning editar-materiales'><i class='fa fa-edit'></i> Editar</button>
                        <button class='btn btn-danger eliminar-materiales'><i class='fa fa-trash'></i> Eliminar</button>
                    </td>`;
            fila += `</tr>`;
        });
    }

    $("#materiales_tb").html(fila);
});
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
function imprimirMateriales(){
    window.open("paginas/referenciales/materiales/print.php");
}


function cargarListaServicioConPrecio(componente) {
    let datos = ejecutarAjax("controladores/Materiales.php", "leer_Materiales_activos=1");
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

function cargarListasMaterial(componente) {
    let datos = ejecutarAjax("controladores/materiales.php", "leer_materiales_activos=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona un material</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_material}'>${item.nombre_material} | Tipo: ${item.descripcion_material}</option>`;
        });
    }
    $(componente).html(option);
}



