
//const fmt0 = (n) => new Intl.NumberFormat("es-PY",{maximumFractionDigits:0}).format(Math.round(n||0));
//
//const qdc = (window.quitarDecimalesConvertir) ? window.quitarDecimalesConvertir : function(valor){
//  if (valor == null) return 0;
//  let s = String(valor).trim();
//  if (!s) return 0;
//  const lastC = Math.max(s.lastIndexOf(","), s.lastIndexOf("."));
//  if (lastC > -1){
//    const intPart  = s.slice(0,lastC).replace(/[.,\s]/g,"");
//    const fracPart = s.slice(lastC+1).replace(/[^\d]/g,"");
//    return Number(intPart + "." + fracPart) || 0;
//  }
//  return Number(s.replace(/[.,\s]/g,"")) || 0;
//};


function mostrarListarServicio(){
  const contenido = dameContenido("paginas/movimientos/servicio/servicios/listar.php");
  $(".contenido-principal").html(contenido);
  cargarTablaServicio();
}

function cargarTablaServicio(){
  $.ajax({
    url: "controladores/servicio.php",
    method: "POST",
    data: { leer: 1 },
    dataType: "json",
    success: function(list){
      const $tb = $("#servicio_tb");
      $tb.empty();

      if (!Array.isArray(list) || list.length === 0){
        $tb.html("<tr><td colspan='6'>SIN REGISTROS</td></tr>");
        return;
      }
      let filas = "";
      list.forEach(item=>{
        filas += `<tr>
          <td>${item.id_servicio}</td>
          <td>${item.fecha_servicio}</td>
          <td class="text-start">${item.cliente || "-"}</td>
          <td class="text-end">${fmt0(item.total)}</td>
          <td>${badgeEstadoSafe(item.estado)}</td>
          <td>
            <button class='btn btn-sm btn-primary' onclick='editarServicio(${item.id_servicio}); return false;'>Editar</button>
            <button class='btn btn-sm btn-danger' onclick='eliminarServicio(${item.id_servicio}); return false;'>Eliminar</button>
          </td>
        </tr>`;
      });
      $tb.html(filas);
    },
    error: function(xhr){
      console.error("[Servicios] HTTP", xhr.status, xhr.responseText);
      const textoPlano = $("<div>").html(xhr.responseText||"").text();
      Swal.fire({
        icon: "error",
        title: "Error al listar servicios",
        text: (textoPlano || "Respuesta no válida del servidor").slice(0, 400)
      });
      $("#servicio_tb").html("<tr><td colspan='6'>ERROR</td></tr>");
    }
  });
}

// =================== Agregar ===================
function mostrarAgregarServicio(){
  const contenido = dameContenido("paginas/movimientos/servicio/servicios/agregar.php");
  $(".contenido-principal").html(contenido);

  $("#editar").val("NO");
  cargarListaCliente("#cliente_lst");
  cargarListaProducto("#producto_rel");

  // último id (acepta '0' o JSON)
  $.post("controladores/servicio.php", { ultimo_registro: 1 })
    .done(function(resp){
      let idUlt = 1;
      if (resp && resp !== "0") {
        try { const j = (typeof resp==="string")? JSON.parse(resp) : resp; idUlt = (qdc(j.id_servicio) || 0) + 1; } catch(_){}
      }
      $("#id_servicio").val(idUlt);
    });

  // fecha actual
  const hoy = new Date().toISOString().slice(0,10);
  $("#fecha_servicio").val(hoy);

  // al cambiar cliente, cargar CI y teléfono
  $(document).off('change','#cliente_lst').on('change','#cliente_lst',function(){
    const id = $(this).val();
    if(id==="0"){ $("#ci_cliente,#telefono_cliente").val(""); return; }
    $.post("controladores/cliente.php",{ id })
      .done(function(datos){
        try {
          const c = (typeof datos==="string")? JSON.parse(datos) : datos;
          $("#ci_cliente").val(c.ci_cliente || "");
          $("#telefono_cliente").val(c.telefono || "");
        } catch(_){
          $("#ci_cliente,#telefono_cliente").val("");
        }
      });
  });
}

function agregarDetalle(){
  const tipo    = $("#tipo_servicio").val().trim();
  const desc    = $("#desc_servicio").val().trim();

  // valor y texto del select de producto (soporta "", "0" o numérico)
  const prodVal = (($("#producto_rel").val() ?? "") + "").trim();
  const prodTxt = $("#producto_rel option:selected").text().trim();

  const cant    = qdc($("#cant_servicio").val());
  const precio  = qdc($("#precio_servicio").val());
  const obs     = $("#obs_detalle").val().trim();

  // ✅ validación clara (producto requerido)
  if (!prodVal || prodVal === "0") {
    mensaje_dialogo_info_ERROR("Debes seleccionar un producto.", "ATENCIÓN");
    $("#producto_rel").focus();
    return;
  }
  if (!tipo || !desc || cant <= 0 || precio <= 0) {
    mensaje_dialogo_info_ERROR("Complete el detalle correctamente.", "ATENCIÓN");
    return;
  }

  const subtotal = cant * precio;

  $("#detalle_servicio").append(`
    <tr>
      <td>${tipo}</td>
      <td>${desc}</td>
      <td>${prodTxt}</td>
      <td class="text-end">${cant}</td>
      <td class="text-end">${precio}</td>
      <td class="text-end">${subtotal}</td>
      <td>${obs}</td>
      <td><button class='btn btn-danger btn-sm quitar-detalle'>Quitar</button></td>
    </tr>
  `);

  // 🔄 reset limpio (compatibles con select “normal”, Select2 o Chosen)
  $("#tipo_servicio, #desc_servicio, #cant_servicio, #precio_servicio, #obs_detalle").val("");
  $("#cant_servicio").val(1);

  // placeholder: preferí value="" (o "0"), como tengas
  $("#producto_rel").val("").trigger("change");       // Select2/normal
  $("#producto_rel").prop("disabled", false);         // por si algún código lo deshabilitó
}


$(document).on("click",".quitar-detalle",function(){
  $(this).closest("tr").remove();
});

// =================== Guardar / Actualizar ===================
function guardarServicio(){
  if ($("#cliente_lst").val()==="0") {
    mensaje_dialogo_info_ERROR("Debes seleccionar un cliente","ATENCIÓN"); return;
  }
  const cab = {
    id_cliente:        Number($("#cliente_lst").val() || 0),
    ci_cliente:        $("#ci_cliente").val(),
    telefono_cliente:  $("#telefono_cliente").val(),
    fecha_servicio:    $("#fecha_servicio").val(),
    estado:            $("#estado_servicio").val(),
    tecnico:           $("#tecnico").val(),
    observaciones:     $("#observaciones").val(),
    total:             0
  };

  const detalles = [];
  $("#detalle_servicio tr").each(function(){
    const t = $(this).find("td");
    if (!t.length) return;
    const subtotal = qdc(t.eq(5).text());
    detalles.push({
      tipo_servicio:        t.eq(0).text().trim(),
      descripcion:          t.eq(1).text().trim(),
      producto_relacionado: t.eq(2).text().trim(),
      cantidad:             qdc(t.eq(3).text()),
      precio_unitario:      qdc(t.eq(4).text()),
      subtotal:             subtotal,
      observaciones:        t.eq(6).text().trim()
    });
    cab.total += subtotal;
  });

  if (cab.total <= 0){ mensaje_dialogo_info_ERROR("Agregá al menos un detalle con importe.","ATENCIÓN"); return; }

  const payload = { cabecera: cab, detalles };
  const esEdicion = $("#editar").val() === "SI";
  if (esEdicion) payload.cabecera.id_servicio = Number($("#id_servicio").val() || 0);

  $.ajax({
    url: "controladores/servicio.php",
    method: "POST",
    data: esEdicion ? { actualizar: JSON.stringify(payload) } : { guardar: JSON.stringify(payload) },
    success: function(resp){
      let data=null; try{ data=(typeof resp==="string")?JSON.parse(resp):resp; }catch(_){}
      if (!data || data.ok !== true){
        const msg = (data && data.error) ? data.error : "Respuesta inesperada del servidor.";
        mensaje_dialogo_info_ERROR(msg,"ATENCIÓN");
        return;
      }
      mensaje_dialogo_info("Registro guardado (ID "+data.id+")","ÉXITO");
      mostrarListarServicio();
    },
    error: function(xhr){
      let msg = "Error al guardar (HTTP "+xhr.status+").";
      try { const j = JSON.parse(xhr.responseText); if (j && j.error) msg = j.error; } catch(_){}
      mensaje_dialogo_info_ERROR(msg,"ATENCIÓN");
    }
  });
}

// =================== Editar ===================
function editarServicio(id){
  const contenido = dameContenido("paginas/movimientos/servicio/servicios/agregar.php");
  $(".contenido-principal").html(contenido);
  $("#editar").val("SI");
  cargarListaCliente("#cliente_lst");
  cargarListaProducto("#producto_rel");

  $.post("controladores/servicio.php", { id })
    .done(function(resp){
      let json; try{ json=(typeof resp==="string")?JSON.parse(resp):resp; }catch(_){ json=null; }
      if (!json || json.ok===false){ mensaje_dialogo_info_ERROR(json?.error || "No encontrado"); return; }

      const cab = json.cabecera || {};
      $("#id_servicio").val(cab.id_servicio);
      $("#fecha_servicio").val(cab.fecha_servicio);
      setTimeout(()=>{ $("#cliente_lst").val(cab.id_cliente); }, 200);
      $("#ci_cliente").val(cab.ci_cliente || "");
      $("#telefono_cliente").val(cab.telefono_cliente || "");
      $("#estado_servicio").val(cab.estado || "");
      $("#tecnico").val(cab.tecnico || "");
      $("#observaciones").val(cab.observaciones || "");

      (json.detalles||[]).forEach(d=>{
        $("#detalle_servicio").append(`
          <tr>
            <td>${d.tipo_servicio}</td>
            <td>${d.descripcion}</td>
            <td>${d.producto_relacionado||""}</td>
            <td class="text-end">${d.cantidad}</td>
            <td class="text-end">${d.precio_unitario}</td>
            <td class="text-end">${d.subtotal}</td>
            <td>${d.observaciones||""}</td>
            <td><button class='btn btn-danger btn-sm quitar-detalle'>Quitar</button></td>
          </tr>
        `);
      });
    })
    .fail(function(xhr){
      let msg = "Error al cargar (HTTP "+xhr.status+").";
      try { const j=JSON.parse(xhr.responseText); if(j&&j.error) msg=j.error; } catch(_){}
      mensaje_dialogo_info_ERROR(msg);
    });
}

// =================== Eliminar ===================
function eliminarServicio(id){
  Swal.fire({
    title:"ATENCIÓN",
    text:"¿Desea eliminar el registro?",
    icon:"question",
    showCancelButton:true,
    confirmButtonText:"Sí",
    cancelButtonText:"No"
  }).then(res=>{
    if(!res.isConfirmed) return;
    $.post("controladores/servicio.php", { eliminar: id })
      .done(function(resp){
        let data=null; try{ data=(typeof resp==="string")?JSON.parse(resp):resp; }catch(_){}
        if (!data || data.ok !== true){ mensaje_dialogo_info_ERROR("No se pudo eliminar."); return; }
        Swal.fire({icon:"success", title:"Eliminado", timer:1200, showConfirmButton:false});
        cargarTablaServicio();
      })
      .fail(function(xhr){
        let msg = "Error al eliminar (HTTP "+xhr.status+").";
        try{ const j=JSON.parse(xhr.responseText); if(j&&j.error) msg=j.error; }catch(_){}
        mensaje_dialogo_info_ERROR(msg);
      });
  });
}
