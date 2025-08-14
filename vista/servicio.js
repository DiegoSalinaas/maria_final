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
    cargarListaProducto("#producto_rel");
    const ultimo = ejecutarAjax("controladores/servicio.php","ultimo_registro=1");
    if(ultimo === "0"){
        $("#id_servicio").val("1");
    }else{
        const json = JSON.parse(ultimo);
        $("#id_servicio").val(quitarDecimalesConvertir(json.id_servicio)+1);
    }
    dameFechaActual("fecha_servicio");
    $(document).off('change','#cliente_lst').on('change','#cliente_lst',function(){
        const id = $(this).val();
        if(id==="0"){ $("#ci_cliente,#telefono_cliente").val(""); return; }
        const datos = ejecutarAjax("controladores/cliente.php","id="+id);
        if(datos!=="0"){ const c = JSON.parse(datos); $("#ci_cliente").val(c.ci_cliente||""); $("#telefono_cliente").val(c.telefono||""); }
    });
}

function agregarDetalle(){
    const tipo = $("#tipo_servicio").val().trim();
    const desc = $("#desc_servicio").val().trim();
    const prodVal = $("#producto_rel").val();
    const prod = prodVal === "0" ? "" : $("#producto_rel option:selected").text();
    const cant = quitarDecimalesConvertir($("#cant_servicio").val());
    const precio = quitarDecimalesConvertir($("#precio_servicio").val());
    const obs = $("#obs_detalle").val().trim();
    if(tipo.length===0 || desc.length===0 || cant<=0 || precio<=0){return;}
    const subtotal = cant*precio;
    $("#detalle_servicio").append(`
        <tr>
            <td>${tipo}</td>
            <td>${desc}</td>
            <td>${prod}</td>
            <td>${cant}</td>
            <td>${precio}</td>
            <td>${subtotal}</td>
            <td>${obs}</td>
            <td><button class='btn btn-danger btn-sm quitar-detalle'>Quitar</button></td>
        </tr>`);
    $("#tipo_servicio,#desc_servicio,#cant_servicio,#precio_servicio,#obs_detalle").val("");
    $("#producto_rel").val("0");
    $("#cant_servicio").val(1);
}

$(document).on("click",".quitar-detalle",function(){
    $(this).closest("tr").remove();
});

function guardarServicio(){
    if($("#cliente_lst").val()==="0"){
        mensaje_dialogo_info_ERROR("Debes seleccionar un cliente","ATENCION");
        return;
    }
    let cab = {
        id_cliente: $("#cliente_lst").val(),
        ci_cliente: $("#ci_cliente").val(),
        telefono_cliente: $("#telefono_cliente").val(),
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
            tipo_servicio: tds.eq(0).text(),
            descripcion: tds.eq(1).text(),
            producto_relacionado: tds.eq(2).text(),
            cantidad: tds.eq(3).text(),
            precio_unitario: tds.eq(4).text(),
            subtotal: tds.eq(5).text(),
            observaciones: tds.eq(6).text()
        });
        cab.total += quitarDecimalesConvertir(tds.eq(5).text());
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
    cargarListaProducto("#producto_rel");
    const datos = ejecutarAjax("controladores/servicio.php","id="+id);
    if(datos === "0") return;
    const json = JSON.parse(datos);
    const cab = json.cabecera;
    $("#id_servicio").val(cab.id_servicio);
    $("#fecha_servicio").val(cab.fecha_servicio);
    setTimeout(()=>{
        $("#cliente_lst").val(cab.id_cliente);
    },300);
    $("#ci_cliente").val(cab.ci_cliente);
    $("#telefono_cliente").val(cab.telefono_cliente);
    $("#estado_servicio").val(cab.estado);
    $("#tecnico").val(cab.tecnico);
    $("#observaciones").val(cab.observaciones);
    json.detalles.forEach(d=>{
        $("#detalle_servicio").append(`<tr><td>${d.tipo_servicio}</td><td>${d.descripcion}</td><td>${d.producto_relacionado}</td><td>${d.cantidad}</td><td>${d.precio_unitario}</td><td>${d.subtotal}</td><td>${d.observaciones}</td><td><button class='btn btn-danger btn-sm quitar-detalle'>Quitar</button></td></tr>`);
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
