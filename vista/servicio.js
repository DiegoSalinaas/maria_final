function mostrarListarServicio(){
    const contenido = dameContenido("paginas/movimientos/servicio/servicios/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaServicio();
}

function cargarTablaServicio(){
    const datos = ejecutarAjax("controladores/servicio.php","leer=1");
    let filas = "";
    if(datos !== "0"){
        const json = JSON.parse(datos);
        json.forEach(item=>{
            filas += `<tr>
                <td>${item.id_servicio}</td>
                <td>${item.fecha_servicio}</td>
                <td>${item.cliente}</td>
                <td>${item.equipo}</td>
                <td>${item.total}</td>
                <td>${item.estado}</td>
                <td>
                    <button class='btn btn-sm btn-primary' onclick='editarServicio(${item.id_servicio}); return false;'>Editar</button>
                    <button class='btn btn-sm btn-danger' onclick='eliminarServicio(${item.id_servicio}); return false;'>Eliminar</button>
                </td>
            </tr>`;
        });
    }
    $("#servicio_tb").html(filas);
}

function mostrarAgregarServicio(){
    const contenido = dameContenido("paginas/movimientos/servicio/servicios/agregar.php");
    $(".contenido-principal").html(contenido);
    cargarListaCliente("#cliente_lst");
    cargarListaEquipo("#equipo_lst");
    const ultimo = ejecutarAjax("controladores/servicio.php","ultimo_registro=1");
    if(ultimo === "0"){
        $("#id_servicio").val("1");
    }else{
        const json = JSON.parse(ultimo);
        $("#id_servicio").val(quitarDecimalesConvertir(json.id_servicio)+1);
    }
    dameFechaActual("fecha_servicio");
}

function agregarDetalle(){
    const desc = $("#desc_detalle").val().trim();
    const costo = quitarDecimalesConvertir($("#costo_detalle").val());
    const estado = $("#estado_detalle").val();
    const fecha = $("#fecha_detalle").val();
    if(desc.length===0 || costo<=0){return;}
    $("#detalle_servicio").append(`
        <tr>
            <td>${desc}</td>
            <td>${costo}</td>
            <td>${estado}</td>
            <td>${fecha}</td>
            <td><button class='btn btn-danger btn-sm quitar-detalle'>Quitar</button></td>
        </tr>`);
    $("#desc_detalle,#costo_detalle,#fecha_detalle").val("");
}

$(document).on("click",".quitar-detalle",function(){
    $(this).closest("tr").remove();
});

function guardarServicio(){
    if($("#cliente_lst").val()==="0" || $("#equipo_lst").val()==="0"){
        mensaje_dialogo_info_ERROR("Debes seleccionar cliente y equipo","ATENCION");
        return;
    }
    let cab = {
        id_cliente: $("#cliente_lst").val(),
        id_equipo: $("#equipo_lst").val(),
        fecha_servicio: $("#fecha_servicio").val(),
        estado: $("#estado_servicio").val(),
        tecnico: $("#tecnico").val(),
        observaciones: $("#observaciones").val(),
        total: 0
    };
    let detalles = [];
    $("#detalle_servicio tr").each(function(){
        let tds = $(this).find("td");
        detalles.push({
            descripcion: tds.eq(0).text(),
            costo: tds.eq(1).text(),
            estado: tds.eq(2).text(),
            fecha_realizada: tds.eq(3).text()
        });
        cab.total += quitarDecimalesConvertir(tds.eq(1).text());
    });
    let payload = {cabecera:cab,detalles:detalles};
    let resp = "";
    if($("#editar").val()==="NO"){
        resp = ejecutarAjax("controladores/servicio.php","guardar="+JSON.stringify(payload));
    }else{
        payload.cabecera.id_servicio = $("#id_servicio").val();
        resp = ejecutarAjax("controladores/servicio.php","actualizar="+JSON.stringify(payload));
    }
    if(resp !== "0"){
        mensaje_dialogo_info("Registro guardado","EXITO");
        mostrarListarServicio();
    }else{
        mensaje_dialogo_info_ERROR("No se pudo guardar","ATENCION");
    }
}

function editarServicio(id){
    const contenido = dameContenido("paginas/movimientos/servicio/servicios/agregar.php");
    $(".contenido-principal").html(contenido);
    $("#editar").val("SI");
    cargarListaCliente("#cliente_lst");
    cargarListaEquipo("#equipo_lst");
    const datos = ejecutarAjax("controladores/servicio.php","id="+id);
    if(datos === "0") return;
    const json = JSON.parse(datos);
    const cab = json.cabecera;
    $("#id_servicio").val(cab.id_servicio);
    $("#fecha_servicio").val(cab.fecha_servicio);
    setTimeout(()=>{
        $("#cliente_lst").val(cab.id_cliente);
        $("#equipo_lst").val(cab.id_equipo);
    },300);
    $("#estado_servicio").val(cab.estado);
    $("#tecnico").val(cab.tecnico);
    $("#observaciones").val(cab.observaciones);
    json.detalles.forEach(d=>{
        $("#detalle_servicio").append(`<tr><td>${d.descripcion}</td><td>${d.costo}</td><td>${d.estado}</td><td>${d.fecha_realizada}</td><td><button class='btn btn-danger btn-sm quitar-detalle'>Quitar</button></td></tr>`);
    });
}

function eliminarServicio(id){
    Swal.fire({
        title:"ATENCION",
        text:"Desea eliminar el registro?",
        icon:"question",
        showCancelButton:true,
        confirmButtonText:"Si",
        cancelButtonText:"No"
    }).then(res=>{
        if(res.isConfirmed){
            const r = ejecutarAjax("controladores/servicio.php","eliminar="+id);
            if(r !== "0"){cargarTablaServicio();}
        }
    });
}

function cargarListaEquipo(componente){
    const datos = ejecutarAjax("controladores/equipo.php","leer=1");
    let option = "<option value='0'>Selecciona un equipo</option>";
    if(datos !== "0"){
        const json = JSON.parse(datos);
        json.forEach(item=>{
            option += `<option value='${item.cod_equipo}'>${item.cod_equipo}</option>`;
        });
    }
    $(componente).html(option);
}
