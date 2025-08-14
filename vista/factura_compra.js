// ==========================================
//  FACTURA DE COMPRA (SIN IVA) - JS COMPLETO
// ==========================================
//
// Requisitos externos (ya existentes en tu proyecto):
// - formatearNumero(n)                -> "1.234"
// - quitarDecimalesConvertir("1.234") -> 1234
// - ejecutarAjax(url, data)           -> string (sync)
// - dameContenido(ruta), dameFechaActual(id), dameFechaActualSQL()
// - mensaje_dialogo_info_ERROR(msg, titulo), mensaje_confirmacion(msg, titulo)
// - Swal (SweetAlert2), jQuery ($)
//
// Estructura esperada de la tabla de detalle (#factura_compra):
//   <thead>
//     <tr>
//       <th>Cód.</th><th>Producto</th><th>Costo</th><th>Cant.</th><th>Subtotal</th><th>Acción</th>
//     </tr>
//   </thead>
//   <tbody id="factura_compra"></tbody>
//
// ==========================================

// ------------------------------------------
// Helpers generales
// ------------------------------------------
function _parseJSONSafe(raw, fallback) {
  try { return JSON.parse(raw); } catch (e) { return fallback; }
}

function productoYaEnTabla(codProducto) {
  let existe = false;
  $("#factura_compra tr").each(function () {
    const cod = $(this).find("td:eq(0)").text().trim();
    if (cod === String(codProducto)) { existe = true; return false; }
  });
  return existe;
}

// Normaliza cabecera OC -> proveedor
function normalizarCabeceraOC(js) {
  if (!js || typeof js !== 'object') return null;
  return {
    cod_proveedor: String(js.cod_proveedor ?? js.id_proveedor ?? js.proveedor_id ?? js.cod_prov ?? ""),
    razon_social:  String(js.razon_social_prov ?? js.razon_social ?? js.nombre_proveedor ?? js.proveedor ?? "")
  };
}

// Selecciona proveedor en el <select>. Si no existe la opción, la agrega.
function setProveedorSeleccionado(codProv, label) {
  const $sel = $("#proveedor_compra_lst");
  if (!$sel.length || !codProv) return;
  let existe = false;
  $sel.find("option").each(function () {
    if (String($(this).val()) === String(codProv)) { existe = true; return false; }
  });
  if (!existe) $sel.append(`<option value="${codProv}">${label || ("Proveedor " + codProv)}</option>`);
  $sel.val(String(codProv));
}

/**
 * Trae un producto desde controladores/proyecto.php.
 * Intenta primero "id=...", si no, "leer=1" y filtra.
 * Normaliza campos: { id, nombre, costo }
 */
function getProductoPorIdDesdeProyecto(idProd) {
  // 1) directo por ID
  let raw = ejecutarAjax("controladores/proyecto.php", "id=" + encodeURIComponent(idProd));
  try {
    const js = JSON.parse(raw);
    if (js && typeof js === "object") {
      return {
        id:     String(js.cod_producto ?? js.id ?? js.producto_id ?? idProd),
        nombre: String(js.nombre_producto ?? js.nombre ?? js.descripcion ?? js.descripcion_corta ?? ""),
        costo:  Number(js.costo ?? js.precio ?? js.precio_venta ?? 0)
      };
    }
  } catch (e) { /* sigue */ }

  // 2) fallback leer=1
  raw = ejecutarAjax("controladores/proyecto.php", "leer=1");
  try {
    const arr = JSON.parse(raw);
    if (Array.isArray(arr)) {
      const p = arr.find(x => String(x.cod_producto ?? x.id ?? x.producto_id ?? "") === String(idProd));
      if (p) {
        return {
          id:     String(p.cod_producto ?? p.id ?? p.producto_id ?? idProd),
          nombre: String(p.nombre_producto ?? p.nombre ?? p.descripcion ?? p.descripcion_corta ?? ""),
          costo:  Number(p.costo ?? p.precio ?? p.precio_venta ?? 0)
        };
      }
    }
  } catch (e2) { /* sin datos */ }

  return null;
}

// ------------------------------------------
// Vistas principal / agregar
// ------------------------------------------
function mostrarListarFacturaCompra() {
  let contenido = dameContenido("paginas/movimientos/compra/factura_compra/listar.php");
  $(".contenido-principal").html(contenido);
  wireEventosListadoFacturaCompra();
  cargarTablaFacturaCompra();
}

function wireEventosListadoFacturaCompra() {
  $("#b_cliente2").off("keyup").on("keyup", function (e) {
    if (e.key === "Enter") cargarTablaFacturaCompra();
  });
  $("#btn_limpiar_fc").off("click").on("click", function () {
    $("#b_cliente2").val("");
    cargarTablaFacturaCompra();
  });
  $("#estado_lst_fc").off("change").on("change", cargarTablaFacturaCompra);
  $("#fecha_desde_fc, #fecha_hasta_fc").off("change").on("change", cargarTablaFacturaCompra);
}

function mostrarAgregarFacturaCompra() {
  const contenido = dameContenido("paginas/movimientos/compra/factura_compra/agregar.php");
  $(".contenido-principal").html(contenido);

  // Fecha por defecto
  dameFechaActual("fecha");

  // Serie/timbrado/nro
  cargarSerieActivaCompra();

  // Combos
  cargarListaProducto("#producto_lst");
  cargarListaProveedorActivos("#proveedor_compra_lst");
  cargarListaOrdenPendiente("#orden_compra_lst");

  // Siguiente código local (visual)
  const ultimo = ejecutarAjax("controladores/factura_compra.php", "ultimo_registro=1");
  try {
    if (ultimo && ultimo !== "0") {
      const j = JSON.parse(ultimo);
      $("#cod").val((parseInt(j.cod_compra || j.cod_registro || 0, 10) || 0) + 1);
    } else {
      $("#cod").val("1");
    }
  } catch (e) { $("#cod").val("1"); }
}

// ------------------------------------------
// Cancelar
// ------------------------------------------
function cancelarFacturaCompra() {
  Swal.fire({
    title: "ATENCION",
    text: "Desea cancelar la operacion?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    cancelButtonText: "No",
    confirmButtonText: "Si"
  }).then((result) => {
    if (result.isConfirmed) {
      mostrarListarFacturaCompra();
    }
  });
}

// ------------------------------------------
// Agregar item a la tabla (SIN IVA)
// ------------------------------------------
function agregarTablaFacturaCompra() {
  const codProducto = $("#producto_lst").val();
  const nombreProducto = $("#producto_lst option:selected").text();
  const cantidad = quitarDecimalesConvertir($("#cantidad_txt").val());
  const costo = quitarDecimalesConvertir($("#costo_txt").val());

  if (!codProducto || codProducto === "0") {
    mensaje_dialogo_info_ERROR("Debes seleccionar un producto", "ATENCION");
    return;
  }
  if (!cantidad || cantidad <= 0) {
    mensaje_dialogo_info_ERROR("Debes ingresar una cantidad válida", "ATENCION");
    return;
  }
  if (!costo || costo <= 0) {
    mensaje_dialogo_info_ERROR("Debes ingresar un costo válido", "ATENCION");
    return;
  }

  // Evitar duplicados
  if (productoYaEnTabla(codProducto)) {
    mensaje_dialogo_info_ERROR("El producto ya ha sido agregado anteriormente", "ATENCION");
    return;
  }

  // (Opcional) validar que el producto exista en proyecto.php
  const prod = getProductoPorIdDesdeProyecto(codProducto);
  if (!prod) {
    mensaje_dialogo_info_ERROR(
      "No se pudo consultar el producto en proyecto.php. Asegúrate de que devuelva JSON.",
      "ERROR"
    );
    return;
  }

  const subtotal = cantidad * costo;

  $("#factura_compra").append(`
    <tr>
      <td>${codProducto}</td>
      <td>${nombreProducto}</td>
      <td>${formatearNumero(costo)}</td>
      <td>${formatearNumero(cantidad)}</td>
      <td>${formatearNumero(subtotal)}</td>
      <td><button class="btn btn-danger remover-item-factura_compra">Remover</button></td>
    </tr>
  `);

  calcularTotalFacturaCompra();
}

// ------------------------------------------
// Calcular totales (SIN IVA)
// ------------------------------------------
function calcularTotalFacturaCompra() {
  let total = 0;

  $("#factura_compra tr").each(function () {
    const sub = quitarDecimalesConvertir($(this).find("td:eq(4)").text());
    total += sub;
  });

  // Solo un total general
  $("#total").text(formatearNumero(total));

  // Si hay crédito (cuotas)
  const intervalo = quitarDecimalesConvertir($("#intervalo").val());
  if (intervalo > 0) {
    $("#monto_cuota").val(formatearNumero(Math.round(total / intervalo)));
  }
}


$(document).on("click", ".remover-item-factura_compra", function () {
  const tr = $(this).closest("tr");
  Swal.fire({
    title: "ATENCION",
    text: "Desea remover el registro?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    cancelButtonText: "No",
    confirmButtonText: "Si"
  }).then((result) => {
    if (result.isConfirmed) {
      tr.remove();
      calcularTotalFacturaCompra();
    }
  });
});


function guardarFacturaCompra() {
  if ($("#fecha").val() < dameFechaActualSQL()) {
    mensaje_dialogo_info_ERROR("La fecha no puede ser menor a la fecha actual", "ATENCION");
    return;
  }

  const cabecera = {
    'cod_compra': $("#cod").val(),
    'fecha_compra': $("#fecha").val(),
    'condicion': $("#condicion_lst").val(),
    'timbrado': $("#timbrado").val(),
    'fecha_venc_timbrado': $("#fecha_venc").val(),
    'nro_factura': $("#nro_factura").val(),
    'cod_proveedor': $("#proveedor_compra_lst").val(),
    'cod_orden_compra': $("#orden_compra_lst").val()
  };

  // Si es CREDITO -> crear cuentas a pagar
  if ($("#condicion_lst").val() === "CREDITO") {
    let fecha = new Date($("#fecha").val());
    for (let i = 1; i <= quitarDecimalesConvertir($("#intervalo").val()); i++) {
      fecha.setMonth(fecha.getMonth() + 1);
      const cuenta = {
        'cod_compra': $("#cod").val(),
        'monto_pagar': quitarDecimalesConvertir($("#monto_cuota").val()),
        'fecha_pago': fecha.toISOString().slice(0, 10),
        'saldo': quitarDecimalesConvertir($("#monto_cuota").val()),
        'estado': 'NO PAGADO'
      };
      ejecutarAjax("controladores/cuenta_pagar.php", "guardar=" + JSON.stringify(cuenta));
    }
  }

  // Guardar cabecera
  let response = ejecutarAjax("controladores/factura_compra.php", "guardar=" + JSON.stringify(cabecera));
  console.log("CABECERA -> " + response);

  // Guardar detalle
  $("#factura_compra tr").each(function () {
    const detalle = {
      'cod_compra': $("#cod").val(),
      'cod_producto': $(this).find("td:eq(0)").text(),
      'costo': quitarDecimalesConvertir($(this).find("td:eq(2)").text()),
      'cantidad': quitarDecimalesConvertir($(this).find("td:eq(3)").text())
    };
    const r = ejecutarAjax("controladores/factura_compra_detalle.php", "guardar=" + JSON.stringify(detalle));
    console.log("DETALLE -> " + r);
  });

 
  const total_general = quitarDecimalesConvertir($("#total").text());
  const libro = {
    'cod_compra': $("#cod").val(),
    'iva5': 0,
    'grav5': 0,
    'grav10': 0,
    'exenta': Math.round(total_general),
    'total': Math.round(total_general),
    'iva10': 0
  };
  const rLibro = ejecutarAjax("controladores/libro_compra.php", "guardar=" + JSON.stringify(libro));
  console.log("LIBRO -> " + rLibro);
  

  mensaje_confirmacion("Se ha guardado correctamente, GUARDADO");
  mostrarListarFacturaCompra();
}


function cargarTablaFacturaCompra() {
  const buscar = $("#b_cliente2").val() || "";
  const estado = $("#estado_lst_fc").val() || "";
  const desde = $("#fecha_desde_fc").val() || "";
  const hasta = $("#fecha_hasta_fc").val() || "";

  const params = `leer=1&buscar=${encodeURIComponent(buscar)}&estado=${encodeURIComponent(estado)}&desde=${encodeURIComponent(desde)}&hasta=${encodeURIComponent(hasta)}`;
  const data = ejecutarAjax("controladores/factura_compra.php", params);

  let fila = "";
  if (data === "0") {
    $("#factura_compra").html("");
    $("#fc_empty_state").removeClass("d-none");
  } else {
    const json_data = JSON.parse(data);
    json_data.map(function (item) {
      fila += `<tr>`;
      fila += `<td>${item.cod_compra}</td>`;
      fila += `<td>${item.fecha_compra}</td>`;
      fila += `<td>${item.nro_factura}</td>`;
      fila += `<td>${item.razon_social_prov}</td>`;
      fila += `<td>${item.condicion}</td>`;
      fila += `<td>${formatearNumero(item.total)}</td>`;
      fila += `<td><span class="badge badge-${(item.estado === "PENDIENTE") ? 'info' : (item.estado === "ANULADO") ? 'danger' : 'success'}">${item.estado}</span></td>`;
      fila += `<td>
                 <button onclick="imprimirFacturaCompra(${item.cod_compra}); return false;" class='btn btn-warning btn-sm imprimir-orden_compra'><i class='typcn typcn-printer'></i></button>
                 <button ${(item.estado === "CONFIRMADO" || item.estado === "ANULADO") ? "disabled" : ""} class='btn btn-danger btn-sm anular-factura_compra'><i class='typcn typcn-delete'></i></button>
               </td>`;
      fila += `</tr>`;
    });
    $("#fc_empty_state").addClass("d-none");
    $("#factura_compra").html(fila);
  }
}

function imprimirFacturaCompra(id) {
  window.open("paginas/movimientos/compra/factura_compra/print.php?id=" + id, "_blank");
}

$(document).on("click", ".anular-factura_compra", function () {
  const id = $(this).closest("tr").find("td:eq(0)").text();
  Swal.fire({
    title: "Atencion",
    text: "Desea anular el registro?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    cancelButtonText: "No",
    confirmButtonText: "Si"
  }).then((result) => {
    if (result.isConfirmed) {
      const response = ejecutarAjax("controladores/factura_compra.php", "anular=" + id);
      console.log(response);
      mensaje_confirmacion("Anulado Correctamente", "Eliminado");
      mostrarListarFacturaCompra();
    }
  });
});

// ------------------------------------------
// Cargar Órdenes de Compra pendientes
// ------------------------------------------
function cargarListaOrdenPendiente(sel) {
  const raw = ejecutarAjax("controladores/orden_compra.php", "pendientes=1");
  let arr = [];
  try { arr = JSON.parse(raw) || []; } catch (e) { arr = []; }

  const $s = $(sel);
  $s.html("<option value='0'>Selecciona un orden de compra</option>");

  if (!Array.isArray(arr) || arr.length === 0) return;

  arr.forEach(o => {
    const total = formatearNumero(o.total || 0);
    const texto = `Nro Orden ${o.cod_orden_compra} | ${o.nom_ape_prov} | Total: ${total}`;
    $s.append(`<option value="${o.cod_orden_compra}">${texto}</option>`);
  });
}

// Al cambiar la OC, cargar proveedor + detalle SIN IVA
$(document).on("change", "#orden_compra_lst", function () {
  const idOC = $("#orden_compra_lst").val();

  if (!idOC || idOC === "0") {
    $("#factura_compra").html("");
    $("#proveedor_compra_lst").val("0");
    calcularTotalFacturaCompra();
    return;
  }

  // 1) Cabecera -> proveedor
  const rawCab = ejecutarAjax("controladores/orden_compra.php", "id=" + encodeURIComponent(idOC));
  const cab = _parseJSONSafe(rawCab, null);
  if (cab) {
    const cabNorm = normalizarCabeceraOC(cab);
    if (cabNorm && cabNorm.cod_proveedor) {
      setProveedorSeleccionado(cabNorm.cod_proveedor, cabNorm.razon_social);
    }
  }

  // 2) Detalle -> pinta filas (SIN IVA)
  const rawDet = ejecutarAjax("controladores/orden_compra_detalle.php", "id=" + encodeURIComponent(idOC));
  if (!rawDet || rawDet === "0") {
    $("#factura_compra").html("");
    calcularTotalFacturaCompra();
    mensaje_dialogo_info_ERROR("La Orden de Compra no tiene detalles.", "ATENCIÓN");
    return;
  }

  const arr = _parseJSONSafe(rawDet, []);
  if (!Array.isArray(arr) || arr.length === 0) {
    $("#factura_compra").html("");
    calcularTotalFacturaCompra();
    mensaje_dialogo_info_ERROR("No se pudo obtener el detalle de la OC o vino vacío.", "ATENCIÓN");
    return;
  }

  $("#factura_compra").html("");
  arr.forEach(item => {
    const cod_producto = String(item.cod_producto ?? item.id_producto ?? item.producto_id ?? item.id ?? "");
    if (!cod_producto) return;
    if (productoYaEnTabla(cod_producto)) return;

    const nombre   = String(item.nombre_producto ?? item.producto ?? item.descripcion ?? item.nombre ?? "");
    const costo    = Number(item.costo ?? item.precio ?? item.precio_unitario ?? 0);
    const cantidad = Number(item.cantidad ?? item.cant ?? 0);
    const subtotal = Math.round(costo * cantidad);

    $("#factura_compra").append(`
      <tr>
        <td>${cod_producto}</td>
        <td>${nombre}</td>
        <td>${formatearNumero(costo)}</td>
        <td>${formatearNumero(cantidad)}</td>
        <td>${formatearNumero(subtotal)}</td>
        <td><button class="btn btn-danger remover-item-factura_compra">Remover</button></td>
      </tr>
    `);
  });

  calcularTotalFacturaCompra();
});

// ------------------------------------------
// Contado / Crédito UI
// ------------------------------------------
$(document).on("change", "#condicion_lst", function () {
  if ($("#condicion_lst").val() === "CONTADO") {
    $(".bloque-credito").attr("hidden", true);
  } else {
    $(".bloque-credito").removeAttr("hidden");
  }
});

// Recalcular cuota cuando cambia el número de cuotas
$(document).on("change", "#intervalo", function () {
  const intervalo = quitarDecimalesConvertir($("#intervalo").val());
  const total = quitarDecimalesConvertir($("#total").text());
  if (intervalo > 0) {
    $("#monto_cuota").val(formatearNumero(Math.round(total / intervalo)));
  }
});

// ------------------------------------------
// Serie activa (compra)
// ------------------------------------------
function cargarSerieActivaCompra() {
  const raw = ejecutarAjax("controladores/factura_compra.php", "serie_activa=1");
  let js;
  try { js = JSON.parse(raw); } catch (e) { js = null; }
  if (!js || js.ok !== true) {
    console.error("serie_activa (compra) =>", raw);
    alert(js?.msg || "No hay serie activa vigente para Factura Compra");
    return;
  }

  const s = js.data;
  const prox = String(Number(s.numero_actual || 0) + 1).padStart(7, '0');

  $("#nro_factura").val(prox).prop("disabled", true);     // Nro de Factura
  $("#timbrado").val(s.timbrado).prop("disabled", true);   // Timbrado
  $("#fecha_venc").val(s.vig_hasta).prop("disabled", true);// Venc. Timbrado

  if ($("#est_txt").length) $("#est_txt").val(s.establecimiento).prop("disabled", true);
  if ($("#pto_txt").length) $("#pto_txt").val(s.punto_expedicion).prop("disabled", true);
}
