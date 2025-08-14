// ===== Helpers =====
const fmt0 = (n) => new Intl.NumberFormat("es-PY",{maximumFractionDigits:0}).format(Math.round(n||0));

function sumarDias(fechaISO, dias){
  const d = new Date(fechaISO || new Date());
  d.setDate(d.getDate() + dias);
  return d.toISOString().slice(0,10);
}

// Fallback si no existe tu helper global
const qdc = (window.quitarDecimalesConvertir) ? window.quitarDecimalesConvertir : function(valor){
  if (valor == null) return 0;
  let s = String(valor).trim();
  if (!s) return 0;
  const lastC = Math.max(s.lastIndexOf(","), s.lastIndexOf("."));
  if (lastC > -1){
    const intPart  = s.slice(0,lastC).replace(/[.,\s]/g,"");
    const fracPart = s.slice(lastC+1).replace(/[^\d]/g,"");
    return Number(intPart + "." + fracPart) || 0;
  }
  return Number(s.replace(/[.,\s]/g,"")) || 0;
};

// ===== Listado =====
function mostrarListarPresupuestoServicio(){
  const contenido = dameContenido("paginas/movimientos/servicio/presupuesto/listar.php");
  $(".contenido-principal").html(contenido);
  cargarTablaPresupuestoServicio();

  // Filtros (si existen en tu listar)
  $("#buscar-presupuesto, #fecha_desde, #fecha_hasta, #estado_filtro").on("input change", function(){
    cargarTablaPresupuestoServicio();
  });
}

function cargarTablaPresupuestoServicio(){
  const buscar = $("#buscar-presupuesto").val()?.trim() || "";
  const desde  = $("#fecha_desde").val() || "";
  const hasta  = $("#fecha_hasta").val() || "";
  const estado = $("#estado_filtro").val() || "";

  if (desde && hasta && new Date(desde) > new Date(hasta)) {
    mensaje_dialogo_info_ERROR("La fecha 'Desde' no puede ser mayor que 'Hasta'.", "ATENCIÓN");
    return;
  }

  const datos = ejecutarAjax("controladores/presupuesto.php", $.param({
    leer: 1, buscar, fecha_desde: desde, fecha_hasta: hasta, estado
  }));

  let filas = "";
  if (datos !== "0" && datos) {
    let json;
    try { json = JSON.parse(datos); } catch(e){ console.error(datos); json = []; }
    $("#presupuesto_count").text(json.length || 0);
    json.forEach(item=>{
      filas += `<tr>
        <td>${item.id_presupuesto}</td>
        <td>${item.fecha_emision}</td>
        <td class="text-start">${item.cliente || "-"}</td>
        <td class="text-end">${fmt0(item.total)}</td>
        <td>${badgeEstado(item.estado)}</td>
        <td>
          <div class="btn-group btn-group-sm" role="group">
            <button class='btn btn-outline-primary' onclick='editarPresupuestoServicio(${item.id_presupuesto}); return false;'>
              <i class="bi bi-pencil"></i> Editar
            </button>
            <button class='btn btn-outline-secondary imprimir-presupuesto' data-id='${item.id_presupuesto}'>
              <i class="bi bi-printer"></i> Imprimir
            </button>
            <button class='btn btn-outline-danger anular-presupuesto' data-id='${item.id_presupuesto}' ${item.estado==="ANULADO"?"disabled":""}>
              <i class="bi bi-x-circle"></i> Anular
            </button>
          </div>
        </td>
      </tr>`;
    });
  } else {
    $("#presupuesto_count").text("0");
    filas = "<tr><td colspan='6'>NO HAY REGISTROS</td></tr>";
  }

  $("#presupuesto_servicio").html(filas);
}

// ===== Agregar / Editar =====
function mostrarAgregarPresupuestoServicio(){
  const contenido = dameContenido("paginas/movimientos/servicio/presupuesto/agregar.php");
  $(".contenido-principal").html(contenido);

  // Por si tu form trae #editar oculto
  $("#editar").val("NO");

  // Fechas
  const hoy = new Date().toISOString().slice(0,10);
  $("#fecha_emision").val(hoy);
  $("#fecha_vencimiento").val(sumarDias(hoy, 7));

  // Cliente
  cargarListaCliente("#cliente_lst");

  // Siguiente ID visible
  const ultimo = ejecutarAjax("controladores/presupuesto.php","ultimo_registro=1");
  if(ultimo === "0") {
    $("#id_presupuesto").val("1");
  } else {
    const json = JSON.parse(ultimo);
    $("#id_presupuesto").val(qdc(json.id_presupuesto) + 1);
  }
}

// Añadir/Remover filas (Servicios)
function agregarServicio(){
  const tipo   = $("#tipo_servicio").val().trim();
  const desc   = $("#desc_servicio").val().trim();
  const cant   = qdc($("#cant_servicio").val());
  const precio = qdc($("#precio_servicio").val());
  const descu  = qdc($("#descuento_servicio").val());
  if (!tipo || !desc || cant<=0 || precio<=0) { mensaje_dialogo_info_ERROR("Complete servicio correctamente."); return; }
  const total = Math.max(0, cant*precio - descu);

  $("#detalle_servicios").append(`
    <tr>
      <td>${tipo}</td>
      <td>${desc}</td>
      <td class="text-end">${cant}</td>
      <td class="text-end">${precio}</td>
      <td class="text-end">${descu}</td>
      <td class="text-end">${total}</td>
      <td><button class='btn btn-danger btn-sm quitar-servicio'>Quitar</button></td>
    </tr>
  `);

  $("#tipo_servicio,#desc_servicio,#cant_servicio,#precio_servicio,#descuento_servicio").val("");
  $("#cant_servicio").val(1);
}
$(document).on("click",".quitar-servicio",function(){ $(this).closest("tr").remove(); });

// Añadir/Remover filas (Insumos)
function agregarInsumo(){
  const desc   = $("#desc_insumo").val().trim();
  const marca  = $("#marca_insumo").val().trim();
  const modelo = $("#modelo_insumo").val().trim();
  const cant   = qdc($("#cant_insumo").val());
  const precio = qdc($("#precio_insumo").val());
  if (!desc || cant<=0 || precio<=0) { mensaje_dialogo_info_ERROR("Complete insumo correctamente."); return; }
  const total = cant*precio;

  $("#detalle_insumos").append(`
    <tr>
      <td>${desc}</td>
      <td>${marca}</td>
      <td>${modelo}</td>
      <td class="text-end">${cant}</td>
      <td class="text-end">${precio}</td>
      <td class="text-end">${total}</td>
      <td><button class='btn btn-danger btn-sm quitar-insumo'>Quitar</button></td>
    </tr>
  `);

  $("#desc_insumo,#marca_insumo,#modelo_insumo,#cant_insumo,#precio_insumo").val("");
  $("#cant_insumo").val(1);
}
$(document).on("click",".quitar-insumo",function(){ $(this).closest("tr").remove(); });

// Guardar (crear/actualizar) con JSON (valida ok:true)
function guardarPresupuestoServicio(){
  if ($("#cliente_lst").val()==="0") {
    mensaje_dialogo_info_ERROR("Debes seleccionar un cliente","ATENCIÓN"); return;
  }

  const femi = $("#fecha_emision").val() || new Date().toISOString().slice(0,10);
  let fven = $("#fecha_vencimiento").val();
  if (!fven) { fven = sumarDias(femi, 7); $("#fecha_vencimiento").val(fven); }

  let cab = {
    fecha_emision: femi,
    fecha_vencimiento: fven,
    id_cliente: Number($("#cliente_lst").val() || 0),
    estado: "PENDIENTE",
    observaciones: $("#observaciones").val(),
    subtotal_servicios: 0,
    subtotal_insumos: 0,
    total: 0
  };

  let servicios = [];
  $("#detalle_servicios tr").each(function(){
    const t = $(this).find("td");
    if (!t.length) return;
    const tot = qdc(t.eq(5).text());
    servicios.push({
      tipo_servicio:   t.eq(0).text().trim(),
      descripcion:     t.eq(1).text().trim(),
      cantidad:        qdc(t.eq(2).text()),
      precio_unitario: qdc(t.eq(3).text()),
      descuento:       qdc(t.eq(4).text()),
      total_linea:     tot
    });
    cab.subtotal_servicios += tot;
  });

  let insumos = [];
  $("#detalle_insumos tr").each(function(){
    const t = $(this).find("td");
    if (!t.length) return;
    const tot = qdc(t.eq(5).text());
    insumos.push({
      descripcion:     t.eq(0).text().trim(),
      marca:           t.eq(1).text().trim(),
      modelo:          t.eq(2).text().trim(),
      cantidad:        qdc(t.eq(3).text()),
      precio_unitario: qdc(t.eq(4).text()),
      total_linea:     tot
    });
    cab.subtotal_insumos += tot;
  });

  cab.total = cab.subtotal_servicios + cab.subtotal_insumos;

  const payload = { cabecera: cab, servicios, insumos };
  const esEdicion = $("#editar").val() === "SI";
  if (esEdicion) payload.cabecera.id_presupuesto = Number($("#id_presupuesto").val() || 0);

  $.ajax({
    url: "controladores/presupuesto.php",
    method: "POST",
    data: esEdicion ? { actualizar: JSON.stringify(payload) } : { guardar: JSON.stringify(payload) },
    success: function(resp){
      let data = null;
      try { data = (typeof resp==="string") ? JSON.parse(resp) : resp; } catch(e){}
      if (!data || data.ok !== true) {
        const msg = data && data.error ? data.error : "Respuesta inesperada del servidor.";
        console.error("Guardar error lógico:", resp);
        mensaje_dialogo_info_ERROR(msg, "ATENCIÓN");
        return;
      }
      mensaje_dialogo_info("Registro guardado (ID "+data.id+")","ÉXITO");
      mostrarListarPresupuestoServicio();
    },
    error: function(xhr){
      let msg = "Error al guardar (HTTP "+xhr.status+").";
      try { const j = JSON.parse(xhr.responseText); if (j && j.error) msg = j.error; } catch(_){}
      console.error("Guardar 400:", xhr.status, xhr.responseText);
      mensaje_dialogo_info_ERROR(msg, "ATENCIÓN");
    }
  });
}

// Editar (carga)
function editarPresupuestoServicio(id){
  const contenido = dameContenido("paginas/movimientos/servicio/presupuesto/agregar.php");
  $(".contenido-principal").html(contenido);
  $("#editar").val("SI");
  cargarListaCliente("#cliente_lst");

  const datos = ejecutarAjax("controladores/presupuesto.php","id="+id);
  if (datos === "0" || !datos){ mensaje_dialogo_info_ERROR("No encontrado"); return; }

  let json; try { json = JSON.parse(datos); } catch(e){ console.error(datos); return; }
  const cab = json.cabecera;
  $("#id_presupuesto").val(cab.id_presupuesto);
  $("#fecha_emision").val(cab.fecha_emision);
  $("#fecha_vencimiento").val(cab.fecha_vencimiento);
  setTimeout(()=>{$("#cliente_lst").val(cab.id_cliente);},200);
  $("#observaciones").val(cab.observaciones || "");

  (json.servicios||[]).forEach(s=>{
    $("#detalle_servicios").append(`
      <tr>
        <td>${s.tipo_servicio}</td>
        <td>${s.descripcion||""}</td>
        <td class="text-end">${s.cantidad}</td>
        <td class="text-end">${s.precio_unitario}</td>
        <td class="text-end">${s.descuento}</td>
        <td class="text-end">${s.total_linea}</td>
        <td><button class='btn btn-danger btn-sm quitar-servicio'>Quitar</button></td>
      </tr>
    `);
  });
  (json.insumos||[]).forEach(i=>{
    $("#detalle_insumos").append(`
      <tr>
        <td>${i.descripcion}</td>
        <td>${i.marca||""}</td>
        <td>${i.modelo||""}</td>
        <td class="text-end">${i.cantidad}</td>
        <td class="text-end">${i.precio_unitario}</td>
        <td class="text-end">${i.total_linea}</td>
        <td><button class='btn btn-danger btn-sm quitar-insumo'>Quitar</button></td>
      </tr>
    `);
  });
}

// Eliminar (SweetAlert + JSON ok)
function eliminarPresupuestoServicio(id){
  Swal.fire({
    title:"ATENCIÓN",
    text:"¿Desea eliminar el registro?",
    icon:"question",
    showCancelButton:true,
    confirmButtonText:"Sí",
    cancelButtonText:"No"
  }).then(res=>{
    if(!res.isConfirmed) return;
    $.post("controladores/presupuesto.php", { eliminar: id })
      .done(function(resp){
        let data=null; try{ data = (typeof resp==="string")?JSON.parse(resp):resp; }catch(e){}
        if (!data || data.ok !== true){ mensaje_dialogo_info_ERROR("No se pudo eliminar."); return; }
        Swal.fire({icon:"success", title:"Eliminado", timer:1200, showConfirmButton:false});
        cargarTablaPresupuestoServicio();
      })
      .fail(function(xhr){
        let msg = "Error al eliminar (HTTP "+xhr.status+").";
        try{ const j=JSON.parse(xhr.responseText); if(j&&j.error) msg=j.error;}catch(_){}
        mensaje_dialogo_info_ERROR(msg);
      });
  });
}

// Anular (SweetAlert + JSON ok)
$(document).on("click",".anular-presupuesto",function(){
  const id = $(this).data("id");
  Swal.fire({
    title: "¿Anular presupuesto?",
    text: "Esta acción no se puede deshacer.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, anular",
    cancelButtonText: "Cancelar"
  }).then((r)=>{
    if (!r.isConfirmed) return;
    $.post("controladores/presupuesto.php", { anular: id })
      .done(function(resp){
        let data=null; try{ data = (typeof resp==="string")?JSON.parse(resp):resp; }catch(e){}
        if (!data || data.ok !== true){ Swal.fire({icon:"error", title:"No se pudo anular"}); return; }
        Swal.fire({icon:"success", title:"Anulado", timer:1200, showConfirmButton:false});
        cargarTablaPresupuestoServicio();
      })
      .fail(function(xhr){
        let msg = "Error al anular (HTTP "+xhr.status+").";
        try{ const j=JSON.parse(xhr.responseText); if(j&&j.error) msg=j.error;}catch(_){}
        Swal.fire({icon:"error", title:"Error", text: msg});
      });
  });
});

// Imprimir
$(document).on("click",".imprimir-presupuesto",function(){
  const id = $(this).data("id");
  window.open("/examen_maria_anibal/maria_final/paginas/movimientos/servicio/presupuesto/imprimir.php?id=" + encodeURIComponent(id), "_blank", "noopener");
});



