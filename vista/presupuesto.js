function mostrarListarPresupuestoServicio(){
    const contenido = dameContenido("paginas/movimientos/servicio/presupuesto/listar.php");
    $(".contenido-principal").html(contenido);
    cargarTablaPresupuestoServicio();
}

function cargarTablaPresupuestoServicio(){
    const datos = ejecutarAjax("controladores/presupuesto.php","leer=1");
    let filas = "";
    if(datos !== "0"){
        const json = JSON.parse(datos);
        json.forEach(item=>{
            filas += `<tr>
                <td>${item.id_presupuesto}</td>
                <td>${item.fecha_emision}</td>
                <td>${item.cliente}</td>
                <td>${item.total}</td>
                <td>${item.estado}</td>
                <td>
                    <button class='btn btn-sm btn-primary' onclick='editarPresupuestoServicio(${item.id_presupuesto}); return false;'>Editar</button>
                    <button class='btn btn-sm btn-danger' onclick='eliminarPresupuestoServicio(${item.id_presupuesto}); return false;'>Eliminar</button>
                </td>
            </tr>`;
        });
    }
    $("#presupuesto_servicio").html(filas);
}

function mostrarAgregarPresupuestoServicio(){
    const contenido = dameContenido("paginas/movimientos/servicio/presupuesto/agregar.php");
    $(".contenido-principal").html(contenido);
    dameFechaActual("fecha_emision");
    cargarListaCliente("#cliente_lst");
    const ultimo = ejecutarAjax("controladores/presupuesto.php","ultimo_registro=1");
    if(ultimo === "0"){
        $("#id_presupuesto").val("1");
    }else{
        const json = JSON.parse(ultimo);
        $("#id_presupuesto").val(quitarDecimalesConvertir(json.id_presupuesto)+1);
    }
}

function agregarServicio(){
    const tipo = $("#tipo_servicio").val().trim();
    const desc = $("#desc_servicio").val().trim();
    const cant = quitarDecimalesConvertir($("#cant_servicio").val());
    const precio = quitarDecimalesConvertir($("#precio_servicio").val());
    const descu = quitarDecimalesConvertir($("#descuento_servicio").val());
    if(tipo.length===0 || desc.length===0 || cant<=0 || precio<=0){return;}
    const total = cant*precio - descu;
    $("#detalle_servicios").append(`
        <tr>
            <td>${tipo}</td>
            <td>${desc}</td>
            <td>${cant}</td>
            <td>${precio}</td>
            <td>${descu}</td>
            <td>${total}</td>
            <td><button class='btn btn-danger btn-sm quitar-servicio'>Quitar</button></td>
        </tr>`);
    $("#tipo_servicio,#desc_servicio,#cant_servicio,#precio_servicio,#descuento_servicio").val("");
    $("#cant_servicio").val(1);
}

$(document).on("click",".quitar-servicio",function(){
    $(this).closest("tr").remove();
});

function agregarInsumo(){
    const desc = $("#desc_insumo").val().trim();
    const marca = $("#marca_insumo").val().trim();
    const modelo = $("#modelo_insumo").val().trim();
    const cant = quitarDecimalesConvertir($("#cant_insumo").val());
    const precio = quitarDecimalesConvertir($("#precio_insumo").val());
    if(desc.length===0 || cant<=0 || precio<=0){return;}
    const total = cant*precio;
    $("#detalle_insumos").append(`
        <tr>
            <td>${desc}</td>
            <td>${marca}</td>
            <td>${modelo}</td>
            <td>${cant}</td>
            <td>${precio}</td>
            <td>${total}</td>
            <td><button class='btn btn-danger btn-sm quitar-insumo'>Quitar</button></td>
        </tr>`);
    $("#desc_insumo,#marca_insumo,#modelo_insumo,#cant_insumo,#precio_insumo").val("");
    $("#cant_insumo").val(1);
}

$(document).on("click",".quitar-insumo",function(){
    $(this).closest("tr").remove();
});

function guardarPresupuestoServicio(){
    if($("#cliente_lst").val()==="0"){
        mensaje_dialogo_info_ERROR("Debes seleccionar un cliente","ATENCION");
        return;
    }
    let cab = {
        fecha_emision: $("#fecha_emision").val(),
        fecha_vencimiento: $("#fecha_vencimiento").val(),
        id_cliente: $("#cliente_lst").val(),
        estado: "PENDIENTE",
        observaciones: $("#observaciones").val(),
        subtotal_servicios: 0,
        subtotal_insumos: 0,
        total: 0
    };
    let servicios = [];
    $("#detalle_servicios tr").each(function(){
        let tds = $(this).find("td");
        servicios.push({
            tipo_servicio: tds.eq(0).text(),
            descripcion: tds.eq(1).text(),
            cantidad: tds.eq(2).text(),
            precio_unitario: tds.eq(3).text(),
            descuento: tds.eq(4).text(),
            total_linea: tds.eq(5).text()
        });
        cab.subtotal_servicios += quitarDecimalesConvertir(tds.eq(5).text());
    });
    let insumos = [];
    $("#detalle_insumos tr").each(function(){
        let tds = $(this).find("td");
        insumos.push({
            descripcion: tds.eq(0).text(),
            marca: tds.eq(1).text(),
            modelo: tds.eq(2).text(),
            cantidad: tds.eq(3).text(),
            precio_unitario: tds.eq(4).text(),
            total_linea: tds.eq(5).text()
        });
        cab.subtotal_insumos += quitarDecimalesConvertir(tds.eq(5).text());
    });
    cab.total = cab.subtotal_servicios + cab.subtotal_insumos;
    let payload = {cabecera:cab, servicios:servicios, insumos:insumos};
    let resp = "";
    if($("#editar").val()==="NO"){
        resp = ejecutarAjax("controladores/presupuesto.php","guardar="+encodeURIComponent(JSON.stringify(payload)));
    }else{
        payload.cabecera.id_presupuesto = $("#id_presupuesto").val();
        resp = ejecutarAjax("controladores/presupuesto.php","actualizar="+encodeURIComponent(JSON.stringify(payload)));
    }
    if(resp !== "0"){
        mensaje_dialogo_info("Registro guardado","EXITO");
        mostrarListarPresupuestoServicio();
    }else{
        mensaje_dialogo_info_ERROR("No se pudo guardar","ATENCION");
    }
}

function editarPresupuestoServicio(id){
    const contenido = dameContenido("paginas/movimientos/servicio/presupuesto/agregar.php");
    $(".contenido-principal").html(contenido);
    $("#editar").val("SI");
    cargarListaCliente("#cliente_lst");
    const datos = ejecutarAjax("controladores/presupuesto.php","id="+id);
    if(datos === "0") return;
    const json = JSON.parse(datos);
    const cab = json.cabecera;
    $("#id_presupuesto").val(cab.id_presupuesto);
    $("#fecha_emision").val(cab.fecha_emision);
    $("#fecha_vencimiento").val(cab.fecha_vencimiento);
    setTimeout(()=>{$("#cliente_lst").val(cab.id_cliente);},300);
    $("#observaciones").val(cab.observaciones);
    json.servicios.forEach(s=>{
        $("#detalle_servicios").append(`<tr><td>${s.tipo_servicio}</td><td>${s.descripcion}</td><td>${s.cantidad}</td><td>${s.precio_unitario}</td><td>${s.descuento}</td><td>${s.total_linea}</td><td><button class='btn btn-danger btn-sm quitar-servicio'>Quitar</button></td></tr>`);
    });
    json.insumos.forEach(i=>{
        $("#detalle_insumos").append(`<tr><td>${i.descripcion}</td><td>${i.marca}</td><td>${i.modelo}</td><td>${i.cantidad}</td><td>${i.precio_unitario}</td><td>${i.total_linea}</td><td><button class='btn btn-danger btn-sm quitar-insumo'>Quitar</button></td></tr>`);
    });
}

function eliminarPresupuestoServicio(id){
    Swal.fire({
        title:"ATENCION",
        text:"Desea eliminar el registro?",
        icon:"question",
        showCancelButton:true,
        confirmButtonText:"Si",
        cancelButtonText:"No"
    }).then(res=>{
        if(res.isConfirmed){
            const r = ejecutarAjax("controladores/presupuesto.php","eliminar="+id);
            if(r!=="0"){
                cargarTablaPresupuestoServicio();
            }
        }
    });
}

function postPresupuesto(data, onOk){
  $.ajax({
    url: "controladores/presupuesto.php",
    method: "POST",
    data: { guardar: JSON.stringify(data) }, // IMPORTANTE: el PHP espera un campo llamado "guardar"
    success: function(resp){
      // resp puede ser el id (string) o un JSON. Intentamos parsear:
      try {
        const j = JSON.parse(resp);
        if (j.ok === false) {
          console.error("Error servidor:", j.error);
          mensaje_dialogo_info_ERROR(j.error || "Error al guardar.");
          return;
        }
        onOk && onOk(j);
      } catch(_){
        // No es JSON: seguramente devolvió el ID
        onOk && onOk(resp);
      }
    },
    error: function(xhr){
      console.error("Guardar 400:", xhr.status, xhr.responseText);
      // Si tu PHP devolvió {"ok":false,"error":"..."}
      try {
        const j = JSON.parse(xhr.responseText);
        mensaje_dialogo_info_ERROR(j.error || "Error al guardar.");
      } catch(_){
        mensaje_dialogo_info_ERROR("Error al guardar (400). Revisá Network/Response.");
      }
    }
  });
}
function getPresupuestos(onOk){
  $.ajax({
    url: "controladores/presupuesto.php",
    method: "POST",
    data: { leer: 1 },
    success: onOk,
    error: function(xhr){
      console.error("Leer 400:", xhr.status, xhr.responseText);
      try {
        const j = JSON.parse(xhr.responseText);
        mensaje_dialogo_info_ERROR(j.error || "Error al listar.");
      } catch(_){
        mensaje_dialogo_info_ERROR("Error al listar (400).");
      }
    }
  });
}
