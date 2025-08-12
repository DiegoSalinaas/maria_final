function mostrarListar() {
    let contenido = dameContenido("paginas/referenciales/proveedores/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaProveedores("#proveedores_tb");
    console.log(contenido);
}
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
function mostrarAgregar() {
    let contenido = dameContenido("paginas/referenciales/proveedores/agregar.php");
    $(".contenido-principal").html(contenido);
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
function cancelarInsumo() {
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
            let contenido = dameContenido("paginas/referenciales/proveedores/listar.php");
            $(".contenido-principal").html(contenido);
            cargarTablaProveedores();
        }
    });

}


//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
function guardarProveedores() {

   
    

    let data = {
        'descripcion': $("#descripcion").val(),
        'costo_compra': $("#costo_compra").val(),
        'precio_venta': $("#precio_venta").val(),
        'stock': $("#stock").val(),
        'stock_minimo': $("#stock_minimo").val(),
        'marca': $("#marca").val(),
        'estado': $("#estado").val()
        
    };

    
    if($("#id_insumo").val() === "0"){
        
        let response = ejecutarAjax("controladores/insumo.php", "guardar=" + JSON.stringify(data));
//        console.log(response);
         mensaje_confirmacion("Guardado correctamente", "Guardado");
//        mostrarListarInsumo();
    }else{
        data = {...data , 'id_insumo' : $("#id_insumo").val()};
         let response = ejecutarAjax("controladores/insumo.php",
         "actualizar=" + JSON.stringify(data));
//        console.log(response);
        mensaje_confirmacion("Actualizado Correctamente","Actualizado");
    }
        $("#id_insumo").val("0");
        mostrarListarInsumo();


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
            fila += `<td>${item.nom_ape_prov}</td>`;
            fila += `<td>${item.razon_social_prov}</td>`;
            fila += `<td>${item.telefono_prov}</td>`;
            fila += `<td>${item.ruc_prov}</td>`;
            fila += `<td>${item.direccion_prov}</td>`;
            fila += `<td>${item.email_prov}</td>`;
            fila += `<td>${item.estado}</td>`;
            fila += `<td>${item.cod_ciudad}</td>`;
            fila += `<td>
                        <button class='btn btn-warning editar-proveedores'><i class='fa fa-edit'></i> Editar</button>
                        <button class='btn btn-danger eliminar-proveedores'><i class='fa fa-trash'></i> Eliminar</button>
                    </td>`;
            fila += `</tr>`;
        });
    }

    $("#proveedores_tb").html(fila);
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".editar-", function (evt) {
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
                let contenido = dameContenido("paginas/referenciales/insumo/agregar.php");
                $(".contenido-principal").html(contenido);

    
                //cargar los datos
                let json_registro = JSON.parse(response);
                $("#cod_proveedor").val(id);
                $("#nom_ape_prov").val(json_registro['nom_ape_prov']);
                $("#razon_social_prov").val(json_registro['razon_social_prov']);
                $("#telefono_prov").val(json_registro['telefono_prov']);
                $("#ruc_prov").val(json_registro['ruc_prov']);
                $("#direccion_prov").val(json_registro['direccion_prov']);
                $("#email_prov").val(json_registro['email_prov']);
                $("#estado").val(json_registro['estado']);
                $("#cod_ciudad").val(json_registro['cod_ciudad']);
            }
        }
    });
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("click", ".eliminar-", function (evt) {
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
            let response = ejecutarAjax("controladores/insumo.php",
            "eliminar=" + id);

            console.log(response);
            mensaje_confirmacion("Eliminado Correctamente", "Eliminado");
            mostrarListarProveedores();
        }
    });
});
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).on("keyup", "#b_insumo", function (evt) {
    let data = ejecutarAjax("controladores/insumo.php", "leer_descripcion="+$("#b_insumo").val());


    let fila = "";
    if (data === "0") {
        fila = "NO HAY REGISTROS";
    } else {
        let json_data = JSON.parse(data);
        json_data.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.cod_proveedor}</td>`;
            fila += `<td>${item.nom_ape_prov}</td>`;
            fila += `<td>${item.razon_social_prov}</td>`;
            fila += `<td>${item.telefono_prov}</td>`;
            fila += `<td>${item.ruc_prov}</td>`;
            fila += `<td>${item.direccion_prov}</td>`;
            fila += `<td>${item.email_prov}</td>`;
            fila += `<td>${item.estado}</td>`;
            fila += `<td>${item.cod_ciudad}</td>`;
            fila += `<td>
                        <button class='btn btn-warning editar-proveedores'><i class='fa fa-edit'></i> Editar</button>
                        <button class='btn btn-danger eliminar-proveedores'><i class='fa fa-trash'></i> Eliminar</button>
                    </td>`;
            fila += `</tr>`;
        });
    }

    $("#proveedores_tb").html(fila);
});
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
function imprimirInsumo(){
    window.open("paginas/referenciales/proveedores/print.php");
}


function cargarListaTipoMaterial(componente) {
    let datos = ejecutarAjax("controladores/tipo_material.php", "leer_material_activos=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona un material</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_tipo_material}'>${item.descripcion}</option>`;
        });
    }
    $(componente).html(option);
}
