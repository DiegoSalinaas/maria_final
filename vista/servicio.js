

function mostrarListarServicio(){
  const contenido = dameContenido("paginas/movimientos/servicio/servicios/listar.php");
  $(".contenido-principal").html(contenido);

  $("#b_servicio").on("keypress", function(e){ if(e.which === 13) cargarTablaServicio(); });
  $("#estado_lst_servicio").on("change", cargarTablaServicio);

  cargarTablaServicio();
}

function cargarTablaServicio(){
  const buscar = $("#b_servicio").val()?.trim() || "";
  const estado = $("#estado_lst_servicio").val() || "";

  $.ajax({
    url: "controladores/servicio.php",
    method: "POST",
    data: { leer: 1, buscar, estado },
    dataType: "json",
    success: function(lista){
      const $tb = $("#servicio_tb");
      const $empty = $("#servicio_empty_state");
      $tb.empty();
      if (!Array.isArray(lista) || lista.length===0){
        $empty.removeClass("d-none");
        return;
      }
      $empty.addClass("d-none");
      let filas = "";
      lista.forEach(item=>{
        filas += `<tr>
          <td>${item.id_servicio}</td>
          <td>${item.fecha_servicio}</td>
          <td class="text-start">${item.cliente || "-"}</td>
          <td class="text-end">${fmt0(item.total)}</td>
          <td>${badgeEstado(item.estado)}</td>
          <td>
            <div class="btn-group btn-group-sm" role="group">
              <button class='btn btn-outline-secondary' onclick='imprimirServicio(${item.id_servicio}); return false;'>Imprimir</button>
              <button class='btn btn-primary' onclick='editarServicio(${item.id_servicio}); return false;'>Editar</button>
              <button class='btn btn-danger' onclick='eliminarServicio(${item.id_servicio}); return false;'>Eliminar</button>
            </div>
          </td>
        </tr>`;
      });
      $tb.html(filas);
    },
    error: function(xhr){
      console.error("[Servicios] HTTP", xhr.status, xhr.responseText);
      $("#servicio_tb").html("<tr><td colspan='6' class='text-center'>ERROR</td></tr>");
    }
  });
}

function imprimirServicio(id){
  window.open(`paginas/movimientos/servicio/servicios/imprimir.php?id=${encodeURIComponent(id)}`, "_blank", "noopener");
}

// ===== Agregar =====
function mostrarAgregarServicio(){
  const contenido = dameContenido("paginas/movimientos/servicio/servicios/agregar.php");
  $(".contenido-principal").html(contenido);

  $("#editar").val("NO");

  // El campo de producto es texto libre; evita validaciones de selección
  $("#producto_rel_txt").prop("required", false);

  // Tu función existente para clientes
  cargarListaCliente("#cliente_lst");

  // Último id
  $.post("controladores/servicio.php",{ ultimo_registro: 1 }).done(function(resp){
    let idUlt = 1;
    try{
      if(resp && resp !== "0"){
        const j = (typeof resp==="string")? JSON.parse(resp) : resp;
        idUlt = (qdc(j.id_servicio) || 0) + 1;
      }
    }catch(_){}
    $("#id_servicio").val(idUlt);
  });

  // Fecha por defecto
  $("#fecha_servicio").val(new Date().toISOString().slice(0,10));

  // Cambiar cliente -> carga CI/teléfono
  $(document).off("change","#cliente_lst").on("change","#cliente_lst", function(){
    const id = $(this).val();
    if(!id || id==="0"){ $("#ci_cliente,#telefono_cliente").val(""); return; }
    $.post("controladores/cliente.php",{ id }).done(function(datos){
      try{
        const c = (typeof datos==="string")? JSON.parse(datos) : datos;
        $("#ci_cliente").val(c.ci_cliente || "");
        $("#telefono_cliente").val(c.telefono || "");
      }catch(_){
        $("#ci_cliente,#telefono_cliente").val("");
      }
    });
  });
}


// ===== Detalle =====
function agregarDetalle() {
    const tipo    = ($("#tipo_servicio").val() || "").trim();
    const desc    = ($("#desc_servicio").val() || "").trim();
    const prodTxt = ($("#producto_rel_txt").val() || "").trim();

    // Limpiar cantidad: quitar cualquier caracter que no sea dígito o punto/coma
    let cantVal = ($("#cant_servicio").val() || "")
        .replace(/\u00A0/g, "")     // elimina espacios duros
        .replace(/[^\d,.\-]/g, "")  // elimina letras y símbolos
        .replace(",", ".")          // coma a punto
        .trim();
    const cant = parseFloat(cantVal);

    let precioVal = ($("#precio_servicio").val() || "")
        .replace(/\u00A0/g, "")
        .replace(/[^\d,.\-]/g, "")
        .replace(",", ".")
        .trim();
    const precio = parseFloat(precioVal);

    const obs = ($("#obs_detalle").val() || "").trim();

    if (!prodTxt) { alert("Debes escribir el producto."); $("#producto_rel_txt").focus(); return; }
    if (!tipo)    { alert("Debes seleccionar el tipo."); $("#tipo_servicio").focus(); return; }
    if (!desc)    { alert("Debes escribir la descripción."); $("#desc_servicio").focus(); return; }
    if (isNaN(cant) || cant <= 0) {
        console.log("Valor crudo de cantidad:", $("#cant_servicio").val());
        console.log("Cantidad procesada:", cantVal);
        alert("Cantidad inválida.");
        $("#cant_servicio").focus();
        return;
    }
    if (isNaN(precio) || precio <= 0) { alert("Precio inválido."); $("#precio_servicio").focus(); return; }

    const subtotal = cant * precio;

    $("#detalle_servicio").append(`
        <tr>
            <td>${tipo}</td>
            <td>${desc}</td>
            <td>${prodTxt}</td>
            <td class="text-end">${cant}</td>
            <td class="text-end">${precio}</td>
            <td class="text-end">${subtotal.toFixed(2)}</td>
            <td>${obs}</td>
            <td><button type="button" class='btn btn-danger btn-sm quitar-detalle'>Quitar</button></td>
        </tr>
    `);

    $("#tipo_servicio, #desc_servicio, #producto_rel_txt, #precio_servicio, #obs_detalle").val("");
    $("#cant_servicio").val(1);
}


// Eliminar fila
$(document).on("click", ".quitar-detalle", function() {
    $(this).closest("tr").remove();
});


$(document).on("click",".quitar-detalle", function(){
  $(this).closest("tr").remove();
});

// ===== Guardar =====
function guardarServicio(){
  const idCli = $("#cliente_lst").val();
  if (!idCli || idCli === "0"){
    if (window.mensaje_dialogo_info_ERROR) mensaje_dialogo_info_ERROR("Debes seleccionar un cliente", "ATENCIÓN");
    else alert("Debes seleccionar un cliente");
    return;
  }

  const cab = {
    id_cliente:        Number(idCli),
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
      producto_relacionado: t.eq(2).text().trim(),   // <- texto libre
      cantidad:             qdc(t.eq(3).text()),
      precio_unitario:      qdc(t.eq(4).text()),
      subtotal:             subtotal,
      observaciones:        t.eq(6).text().trim()
    });
    cab.total += subtotal;
  });

  if (cab.total <= 0){
    if (window.mensaje_dialogo_info_ERROR) mensaje_dialogo_info_ERROR("Agregá al menos un detalle con importe.", "ATENCIÓN");
    else alert("Agregá al menos un detalle con importe.");
    return;
  }

  const payload = { cabecera: cab, detalles };
  const esEdicion = $("#editar").val() === "SI";
  if (esEdicion) payload.cabecera.id_servicio = Number($("#id_servicio").val() || 0);

  $.ajax({
    url: "controladores/servicio.php",
    method: "POST",
    data: esEdicion ? { actualizar: JSON.stringify(payload) } : { guardar: JSON.stringify(payload) },
    success: function(resp){
      let data = null;
      try { data = (typeof resp === "string") ? JSON.parse(resp) : resp; } catch(_){}
      if (!data || data.ok !== true){
        const msg = (data && data.error) ? data.error : "Respuesta inesperada del servidor.";
        if (window.mensaje_dialogo_info_ERROR) mensaje_dialogo_info_ERROR(msg, "ATENCIÓN");
        else alert(msg);
        return;
      }
      if (window.mensaje_dialogo_info) mensaje_dialogo_info(`Registro guardado (ID ${data.id})`, "ÉXITO");
      else alert(`Registro guardado (ID ${data.id})`);
      mostrarListarServicio();
    },
    error: function(xhr){
      let msg = "Error al guardar (HTTP "+xhr.status+").";
      try { const j = JSON.parse(xhr.responseText); if (j && j.error) msg = j.error; } catch(_){}
      if (window.mensaje_dialogo_info_ERROR) mensaje_dialogo_info_ERROR(msg, "ATENCIÓN");
      else alert(msg);
    }
  });
}

// ===== Editar =====
function editarServicio(id){
  const contenido = dameContenido("paginas/movimientos/servicio/servicios/agregar.php");
  $(".contenido-principal").html(contenido);
  $("#editar").val("SI");

  cargarListaCliente("#cliente_lst");
  // No hay que cargar nada en producto; es texto libre

  $.post("controladores/servicio.php", { id })
    .done(function(resp){
      let json=null; try{ json = (typeof resp==="string")? JSON.parse(resp) : resp; }catch(_){}
      if (!json || json.ok === false){
        if (window.mensaje_dialogo_info_ERROR) mensaje_dialogo_info_ERROR(json?.error || "No encontrado");
        else alert(json?.error || "No encontrado");
        return;
      }
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
            <td><button type="button" class='btn btn-danger btn-sm quitar-detalle'>Quitar</button></td>
          </tr>
        `);
      });
    })
    .fail(function(xhr){
      let msg = "Error al cargar (HTTP "+xhr.status+").";
      try { const j = JSON.parse(xhr.responseText); if (j && j.error) msg = j.error; } catch(_){}
      if (window.mensaje_dialogo_info_ERROR) mensaje_dialogo_info_ERROR(msg);
      else alert(msg);
    });
}

// ===== Eliminar =====
function eliminarServicio(id){
  if (window.Swal){
    Swal.fire({
      title: "ATENCIÓN",
      text: "¿Desea eliminar el registro?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sí",
      cancelButtonText: "No"
    }).then(res=>{
      if (!res.isConfirmed) return;
      $.post("controladores/servicio.php", { eliminar: id })
        .done(function(resp){
          let data=null; try{ data=(typeof resp==="string")?JSON.parse(resp):resp; }catch(_){}
          if (!data || data.ok !== true){
            if (window.mensaje_dialogo_info_ERROR) mensaje_dialogo_info_ERROR("No se pudo eliminar.");
            else alert("No se pudo eliminar.");
            return;
          }
          Swal.fire({ icon:"success", title:"Eliminado", timer:1200, showConfirmButton:false });
          cargarTablaServicio();
        })
        .fail(function(xhr){
          let msg = "Error al eliminar (HTTP "+xhr.status+").";
          try { const j=JSON.parse(xhr.responseText); if(j&&j.error) msg=j.error; }catch(_){}
          if (window.mensaje_dialogo_info_ERROR) mensaje_dialogo_info_ERROR(msg);
          else alert(msg);
        });
    });
  } else {
    if (!confirm("¿Desea eliminar el registro?")) return;
    $.post("controladores/servicio.php", { eliminar: id }).done(function(){ cargarTablaServicio(); });
  }
}

// Exponer a global
window.mostrarListarServicio  = mostrarListarServicio;
window.cargarTablaServicio    = cargarTablaServicio;
window.mostrarAgregarServicio = mostrarAgregarServicio;
window.agregarDetalle         = agregarDetalle;
window.guardarServicio        = guardarServicio;
window.editarServicio         = editarServicio;
window.eliminarServicio       = eliminarServicio;
