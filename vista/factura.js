
let detallesFactura = [];
let listaClientes = [];
let listaProductos = [];

// ----------------------------------------
// Utilidades
// ----------------------------------------
function formatearPY(n){
  const num = Math.trunc(Number(n) || 0); // aseguramos entero
  try { 
    return formatearNumero(String(num));  // tu función de util.js
  } catch(e){
    return String(num);
  }
}
function debounce(fn, ms){ let t; return function(){ clearTimeout(t); t=setTimeout(()=>fn.apply(this, arguments), ms); }; }
function unformatPY(str){
  return String(str||'').replace(/\./g,'').replace(',', '.'); // por si acaso
}

// ----------------------------------------
// Listado principal
// ----------------------------------------
function mostrarListarFacturas(){
  console.log("mostrarListarFacturas() ejecutada");
  const url = "paginas/movimientos/venta/factura_venta/listar.php";
  const c = dameContenido(url);
  if(!c || c === "0"){
    $(".contenido-principal").html(`<div class="alert alert-danger m-3">
      No se pudo cargar <b>${url}</b>. Verifica la ruta/archivo.
    </div>`);
    return;
  }
  $(".contenido-principal").html(c);

  // estado "cargando"
  $("#tabla_facturas").html(`
    <tr><td colspan="7" class="text-center text-muted py-4">
      <span class="spinner-border spinner-border-sm"></span> Cargando...
    </td></tr>`);

  wireListEvents();
  cargarTablaFacturas();
}
window.mostrarListarFacturas = mostrarListarFacturas;

function wireListEvents(){
  $("#b_factura").off("input").on("input", debounce(cargarTablaFacturas, 250));
  $("#estado_lst").off("change").on("change", cargarTablaFacturas);
  $("#btn_buscar").off("click").on("click", function(e){ e.preventDefault(); cargarTablaFacturas(); });
}

// ----------------------------------------
// Agregar/Editar
// ----------------------------------------
function mostrarAgregarFactura(){
  const c = dameContenido("paginas/movimientos/venta/factura_venta/agregar.php");
  $(".contenido-principal").html(c);
  detallesFactura = [];

  // cargar serie antes que combos (para ya ver bloqueos)
  cargarSerieActiva();

  cargarListaClientes();
  cargarListaProductos();
  wireInputsNumericos();

  // Asegura estado del select desde el inicio
  marcarProductosUsados();
}
window.mostrarAgregarFactura = mostrarAgregarFactura;

function cargarSerieActiva(){
  const js = ejecutarAjaxJSON("controladores/factura.php", "serie_activa=1");
  console.log("serie JS =>", js);
  if(!js || !js.ok){ alert(js?.msg || "No hay serie activa"); return; }

  const s = js.data;
  $("#timbrado_txt").val(s.timbrado).prop("disabled", true);
  $("#vig_desde_txt").val(s.vig_desde).prop("disabled", true);
  $("#vig_hasta_txt").val(s.vig_hasta).prop("disabled", true);
  $("#est_txt").val(s.establecimiento).prop("disabled", true);
  $("#pto_txt").val(s.punto_expedicion).prop("disabled", true);

  const prox = String(Number(s.numero_actual||0)+1).padStart(7,'0');
  $("#num_txt").val(prox).prop("disabled", true);
}

// Evitar el problema del "0" inicial que se concatena
function wireInputsNumericos(){
  // Cantidad: entero simple
  const $cant = $("#cant_txt");
  $cant.on('focus', function(){ if(this.value==='0') this.value=''; });
  $cant.on('input', function(){ this.value = this.value.replace(/\D/g,''); });
  $cant.on('blur', function(){ if(this.value==='') this.value='1'; });

  // Precio y Descuento: formateo visual
  ["#precio_txt", "#desc_txt"].forEach(sel=>{
    const $el = $(sel);
    $el.on('focus', function(){
      this.value = unformatPY(this.value);
      if(this.value==='0') this.value='';
    });
    $el.on('input', function(){
      this.value = this.value.replace(/\D/g,'');
    });
    $el.on('blur', function(){
      let v = unformatPY(this.value);
      const n = Math.trunc(Number(v)||0);
      this.value = formatearPY(n); // mostrar con puntos
    });
  });
}

function ejecutarAjaxJSON(url, data){
  const r = ejecutarAjax(url, data);
  if(!r || r === '0') return null;
  try { return JSON.parse(r); } catch(e){ console.error('JSON inválido', r); return null; }
}

// ----------------------------------------
// Clientes
// ----------------------------------------
function cargarListaClientes(){
  const raw = ejecutarAjax("controladores/cliente.php", "leer=1");
  console.log("clientes RAW =>", raw);

  let data = [];
  try {
    const js = JSON.parse(raw);
    if (Array.isArray(js)) {
      data = js;
    } else if (js && js.ok) {
      data = js.data || [];
    } else if (js && js.length >= 0) {
      data = js;
    }
  } catch (e) {
    console.error("JSON inválido de clientes", e, raw);
    alert("No se pudieron cargar clientes. Revisa consola.");
    return;
  }

  listaClientes = data;
  renderListaClientes(listaClientes);
}

function renderListaClientes(arr){
  const $s = $("#id_cliente_lst");
  $s.html('<option value="">-- Seleccione --</option>');
  arr.forEach(c => $s.append(`<option value="${c.cod_cliente}">${c.nombre_cliente} – ${c.ruc}</option>`));
}

// ----------------------------------------
// Productos
// ----------------------------------------
function cargarListaProductos(){
  // 1) intenta con tu endpoint de productos existente
  const raw = ejecutarAjax("controladores/proyecto.php", "leer=1");
  console.log("productos RAW =>", raw);

  let data = [];
  try {
    const js = JSON.parse(raw);
    if (Array.isArray(js)) {
      data = js;
    } else if (js && js.ok) {
      data = js.data || [];
    } else if (js) {
      data = js;
    }
  } catch(e){
    console.warn("productos.php no devolvió JSON válido, probando fallback a factura.php...", e);
    // 2) fallback a nuestro controlador factura.php (productos=1)
    const raw2 = ejecutarAjax("controladores/factura.php", "productos=1");
    console.log("productos (fallback) RAW =>", raw2);
    try {
      const js2 = JSON.parse(raw2);
      if (js2 && js2.ok) data = js2.data || [];
    } catch(e2){
      console.error("JSON inválido de productos", e2, raw2);
      alert("No se pudieron cargar productos. Revisa consola.");
      return;
    }
  }

  // Normaliza posibles nombres de campos
  listaProductos = data.map(p => ({
    cod_producto: Number(p.cod_producto ?? p.producto_id ?? p.id_producto ?? p.id ?? 0),
    nombre:       String(p.nombre ?? p.descripcion_corta ?? p.producto ?? ""),
    descripcion:  String(p.descripcion ?? ""),
    precio:       Number(p.precio ?? p.precio_venta ?? 0),
    iva:          Number(p.iva ?? p.iva_tipo ?? 0),
    stock:        Number(p.stock ?? 0)
  })).filter(p => p.cod_producto);

  renderListaProductos(listaProductos);
  wireProductoChange();
  marcarProductosUsados(); // sincroniza estado tras cargar
}

function renderListaProductos(arr){
  const $s = $("#id_producto_lst");
  $s.html('<option value="">-- Seleccione --</option>');
  arr.forEach(p => $s.append(
    `<option value="${p.cod_producto}" data-precio="${p.precio}">
       ${p.nombre}
     </option>`
  ));
}

// ---------- Anti-duplicados ----------
function existeProductoEnDetalle(cod){
  cod = parseInt(cod||0,10);
  return detallesFactura.some(d => parseInt(d.cod_producto,10) === cod);
}

function marcarProductosUsados(){
  const usados = new Set(detallesFactura.map(d => String(d.cod_producto)));
  const $sel = $("#id_producto_lst");
  const el = $sel[0];
  if(!el) return;

  for (let i=0; i<el.options.length; i++){
    const opt = el.options[i];
    if(!opt.value) continue; // "-- Seleccione --"
    if (usados.has(opt.value)){
      opt.disabled = true;
      if(!opt.text.includes(" (ya agregado)")) opt.text += " (ya agregado)";
    } else {
      opt.disabled = false;
      opt.text = opt.text.replace(" (ya agregado)","");
    }
  }

  // Si la opción seleccionada quedó deshabilitada, reset
  const selOpt = el.options[el.selectedIndex || 0];
  if(selOpt && selOpt.disabled){ $sel.val(''); }
}

function wireProductoChange(){
  $("#id_producto_lst").off("change").on('change', function(){
    const cod = parseInt(this.value||0,10);
    if(cod && existeProductoEnDetalle(cod)){
      alert("Este producto ya fue agregado al detalle. Edita la cantidad o quita el ítem.");
      $(this).val('');
      return;
    }
    const opt = this.options[this.selectedIndex];
    if(!opt || !opt.dataset) return;
    $("#precio_txt").val(opt.dataset.precio || '0').trigger('blur');
    $("#desc_txt").val('0').trigger('blur');
    $("#cant_txt").val('1');
  });
}

// ----------------------------------------
// Detalles
// ----------------------------------------
function limpiarDetalleUI(){
  $("#id_producto_lst").val('');
  $("#cant_txt").val('1');
  $("#precio_txt").val('0');
  $("#desc_txt").val('0');
}

function agregarDetalle(){
  const cod = parseInt($("#id_producto_lst").val()||0,10);
  if(!cod){ alert('Seleccione un producto'); return; }

  // Bloqueo de repetidos
  if (existeProductoEnDetalle(cod)){
    alert("Este producto ya se encuentra en el detalle. Modifique la cantidad desde la tabla o elimine el ítem.");
    return;
  }

  const prod   = listaProductos.find(p=> parseInt(p.cod_producto,10) === cod);
  const cant   = parseInt(unformatPY($("#cant_txt").val())||'0',10);
  const precio = parseInt(unformatPY($("#precio_txt").val())||'0',10);
  const desc   = parseInt(unformatPY($("#desc_txt").val())||'0',10);

  if(cant <= 0){ alert('Cantidad inválida'); return; }
  if(precio <= 0){ alert('Precio debe ser > 0'); return; }
  if(desc < 0){ alert('Descuento inválido'); return; }

  detallesFactura.push({
    cod_producto: cod,
    nombre: prod?.nombre || '',
    cantidad: cant,
    precio_unitario: precio,
    descuento: desc,
    iva: parseInt(prod?.iva||0,10)
  });

  renderDetalles();
  limpiarDetalleUI();
  marcarProductosUsados();   // actualizar el select
}

function quitarDetalle(idx){
  detallesFactura.splice(idx,1);
  renderDetalles();
  marcarProductosUsados();   // re-habilita en el select
}

function renderDetalles(){
  const $tb = $("#tabla_detalle");
  $tb.html('');
  let ex=0, g5=0, g10=0, iva=0, total=0;

  detallesFactura.forEach((d,i)=>{
    const neto = Math.max(d.cantidad * d.precio_unitario - d.descuento, 0);

    // IVA calculado por si lo querés mostrar
    const ivaMonto = d.iva===5 ? Math.round(neto/21) : d.iva===10 ? Math.round(neto/11) : 0;

    // Acumuladores de base imponible (sin IVA)
    ex  += (d.iva===0 ? neto : 0);
    g5  += (d.iva===5 ? neto : 0);
    g10 += (d.iva===10? neto : 0);
    iva += ivaMonto;

    // TOTAL SOLO PRECIOS (sin IVA)
    total += neto;

    $tb.append(`
      <tr>
        <td>${i+1}</td>
        <td>${d.nombre}</td>
        <td>${d.cantidad}</td>
        <td>${formatearPY(d.precio_unitario)}</td>
        <td>${formatearPY(d.descuento)}</td>
        <td>—</td>                     <!-- sin IVA -->
        <td>${formatearPY(neto)}</td>  <!-- subtotal = neto -->
        <td>
          <button class="btn btn-sm btn-danger" onclick="quitarDetalle(${i}); return false;">
            <i class="typcn typcn-times"></i>
          </button>
        </td>
      </tr>
    `);
  });

  $("#tot_exenta").text(formatearPY(ex));
  $("#tot_5").text(formatearPY(g5));
  $("#tot_10").text(formatearPY(g10));
  $("#tot_general").text(formatearPY(total)); 

  // Mantener el select actualizado luego de render
  marcarProductosUsados();
}

// ----------------------------------------
// Guardado
// ----------------------------------------
function armarCabeceraDesdeUI(){
  return {
    fecha_emision: $("#fecha_txt").val(),
    cod_cliente: parseInt($("#id_cliente_lst").val()||'0',10),
    condicion_venta: $("#condicion_lst").val(),
    moneda: $("#moneda_txt").val()||'PYG',
    timbrado: $("#timbrado_txt").val(),
    timbrado_vigencia_desde: $("#vig_desde_txt").val(),
    timbrado_vigencia_hasta: $("#vig_hasta_txt").val(),
    establecimiento: $("#est_txt").val(),
    punto_expedicion: $("#pto_txt").val(),
    numero: $("#num_txt").val(),
    observacion: $("#obs_txt").val()
  };
}

function validarCabecera(c){
  if(!c.fecha_emision) return 'Fecha requerida';
  if(!c.cod_cliente) return 'Cliente requerido';
  if(!c.timbrado || c.timbrado.length!==8) return 'Timbrado (8 dígitos)';
  if(!c.establecimiento || c.establecimiento.length!==3) return 'Establecimiento (3)';
  if(!c.punto_expedicion || c.punto_expedicion.length!==3) return 'Punto de expedición (3)';
  if(!c.numero || c.numero.length!==7) return 'Número (7)';
  return null;
}

function guardarFacturaCompleta(){
  const cab = armarCabeceraDesdeUI();
  const idEdit = parseInt($("#id_factura").val()||'0',10);

  if(!cab.fecha_emision){ alert('Fecha requerida'); return; }
  if(!cab.cod_cliente){ alert('Cliente requerido'); return; }
  if(detallesFactura.length===0){ alert('Agregue al menos un ítem'); return; }

  if (idEdit > 0) {
    // Normaliza CRÉDITO -> CREDITO para evitar choques con ENUM
    const cond = ($("#condicion_lst").val() || "CONTADO")
                   .toUpperCase()
                   .normalize("NFD").replace(/[\u0300-\u036f]/g, ""); // quita tildes

    const cabUpd = {
      id_factura: idEdit,
      fecha_emision: $("#fecha_txt").val(),
      cod_cliente: parseInt($("#id_cliente_lst").val()||'0',10),
      condicion_venta: cond, // CONTADO | CREDITO
      moneda: $("#moneda_txt").val() || "PYG",
      observacion: $("#obs_txt").val() || null
    };

    const jCab = ejecutarAjaxJSON("controladores/factura.php",
      "actualizar_cabecera="+ encodeURIComponent(JSON.stringify(cabUpd)));
    console.log("actualizar_cabecera =>", jCab);
    if(!jCab || !jCab.ok){
      alert("No se pudo actualizar cabecera. "
            + (jCab?.msg || "") + (jCab?.extra ? (" | " + jCab.extra) : ""));
      return;
    }

    const payloadUpd = { id_factura: idEdit, detalles: detallesFactura };
    const jDet = ejecutarAjaxJSON("controladores/factura.php",
      "reemplazar_detalles="+ encodeURIComponent(JSON.stringify(payloadUpd)));
    console.log("reemplazar_detalles =>", jDet);
    if(!jDet || !jDet.ok){
      alert("No se pudo actualizar detalles. "
            + (jDet?.msg || "") + (jDet?.extra ? (" | " + jDet.extra) : ""));
      return;
    }

    alert("Factura actualizada correctamente");
    mostrarListarFacturas();
    return;
  }

  // ---- MODO NUEVA ----
  const r1 = ejecutarAjaxJSON("controladores/factura.php",
    "guardar_cabecera_auto="+ encodeURIComponent(JSON.stringify(cab)));
  if(!r1 || !r1.ok){ alert("No se pudo guardar cabecera (auto)"); return; }

  const idNew = r1.data.id_factura;
  const payload = { id_factura: idNew, detalles: detallesFactura };
  const raw2 = ejecutarAjax("controladores/factura.php",
    "guardar_detalles="+ encodeURIComponent(JSON.stringify(payload)));
  console.log("guardar_detalles RAW =>", raw2);
  let r2; try { r2 = JSON.parse(raw2); } catch(e){ r2 = null; }
  if(!r2 || !r2.ok){
    alert("No se pudo guardar detalles. " + (r2?.msg || "") + (r2?.extra?(" | "+r2.extra):""));
    return;
  }

  alert(`Factura ${r1.data.numero_completo} guardada correctamente`);
  mostrarListarFacturas();
}

// ----------------------------------------
// Tabla listado (buscar)
// ----------------------------------------
function cargarTablaFacturas(){
  const buscar = $("#b_factura").val()||'';
  const estado = $("#estado_lst").val()||'';
  const js = ejecutarAjaxJSON("controladores/factura.php",
    `leer=1&buscar=${encodeURIComponent(buscar)}&estado=${encodeURIComponent(estado)}`);
  const $tb = $("#tabla_facturas");
  $tb.html('');

  if(!js){
    $tb.html(`<tr><td colspan="7" class="text-danger text-center">Error cargando datos. Revisá consola/Network.</td></tr>`);
    return;
  }
  if(!js.ok){
    $tb.html(`<tr><td colspan="7" class="text-danger text-center">${js.msg || 'Error'}</td></tr>`);
    return;
  }
  if (!Array.isArray(js.data) || js.data.length === 0){
    $tb.html(`<tr><td colspan="7" class="text-muted text-center py-4">
      <i class="typcn typcn-info-outline"></i> Sin resultados para <b>${buscar || '—'}</b>.
    </td></tr>`);
    return;
  }

  js.data.forEach(f => {
    $tb.append(`
      <tr>
        <td>${f.id_factura}</td>
        <td>${f.fecha_emision}</td>
        <td>${f.establecimiento}-${f.punto_expedicion}-${f.numero}</td>
        <td class="text-left">${f.nombre_cliente}</td>
        <td>${formatearPY(f.total_neto ?? f.total_general)}</td>
        <td>
          <span class="badge ${f.estado==='ANULADO'?'badge-danger':'badge-success'}">${f.estado}</span>
        </td>
        <td class="d-flex gap-1 justify-content-center">
          <button class="btn btn-sm btn-outline-dark" onclick="imprimirFactura(${f.id_factura}); return false;">Imprimir</button>
          <button class="btn btn-sm btn-warning" onclick="editarFactura(${f.id_factura}); return false;" ${f.estado==='ANULADO'?'disabled':''}>Editar</button>
          <button class="btn btn-sm btn-danger" onclick="anularFactura(${f.id_factura}); return false;" ${f.estado==='ANULADO'?'disabled':''}>Anular</button>
        </td>
      </tr>`);
  });
}

function anularFactura(id){
  if(!confirm('¿Anular factura?')) return;
  const js = ejecutarAjaxJSON("controladores/factura.php", "anular="+id);
  if(js && js.ok){ cargarTablaFacturas(); }
}

// ----------------------------------------
// Acciones varias
// ----------------------------------------
function verFactura(id){
  const js = ejecutarAjaxJSON("controladores/factura.php", "traer="+id);
  if(!js || !js.ok){ alert('No encontrado'); return; }
  const f = js.data.cabecera;
  alert(`Factura ${f.establecimiento}-${f.punto_expedicion}-${f.numero}\nCliente: ${f.cod_cliente}\nTotal: ${formatearPY(f.total_general)}`);
}

function editarFactura(id){
  mostrarAgregarFactura();
  setTimeout(()=>{
    const js = ejecutarAjaxJSON("controladores/factura.php", "traer="+id);
    if(!js || !js.ok){ alert('No encontrado'); return; }
    const c = js.data.cabecera;

    $("#id_factura").val(c.id_factura);
    $("#fecha_txt").val(c.fecha_emision);
    $("#id_cliente_lst").val(c.cod_cliente);
    $("#condicion_lst").val(c.condicion_venta);
    $("#moneda_txt").val(c.moneda);
    $("#obs_txt").val(c.observacion||'');

    // sobrescribe lo que cargó cargarSerieActiva()
    $("#timbrado_txt").val(c.timbrado).prop("disabled", true);
    $("#vig_desde_txt").val(c.timbrado_vigencia_desde).prop("disabled", true);
    $("#vig_hasta_txt").val(c.timbrado_vigencia_hasta).prop("disabled", true);
    $("#est_txt").val(c.establecimiento).prop("disabled", true);
    $("#pto_txt").val(c.punto_expedicion).prop("disabled", true);
    $("#num_txt").val(c.numero).prop("disabled", true);

    // Detalles…
    detallesFactura = (js.data.detalles||[]).map(d=>({
      cod_producto: Number(d.cod_producto),
      nombre: d.producto,
      cantidad: Number(d.cantidad),
      precio_unitario: Number(d.precio_unitario),
      descuento: Number(d.descuento),
      iva: Number(d.iva ?? 0)
    }));
    renderDetalles();           // re-pinta detalle
    marcarProductosUsados();    // deshabilita en el select los ya agregados
  }, 150);
}

function imprimirFactura(id){
  const url = `paginas/movimientos/venta/factura_venta/imprimir.php?id=${encodeURIComponent(id)}`;
  window.open(url, '_blank', 'noopener');
}
