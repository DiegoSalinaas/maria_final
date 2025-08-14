/* ===================== Helpers ===================== */
function parseJSONSafe(raw){
  if (raw == null) return null;
  if (typeof raw === 'object') return raw;
  try { return JSON.parse(raw); } catch(e){ console.error('JSON inválido:', raw); return null; }
}
function toArrayResponse(raw){
  const js = parseJSONSafe(raw);
  if (!js) return [];
  if (Array.isArray(js)) return js;
  if (Array.isArray(js.data)) return js.data;
  return [];
}
function isPosInt(v){ return Number.isInteger(v) && v > 0; }
function getValEntero(sel){ return quitarDecimalesConvertir($(sel).val().trim()); }
function productoYaEnTabla(id){
  let dup = false;
  $("#orden_compra_compra tr").each(function(){
    if($(this).find("td:eq(0)").text() === String(id)) { dup = true; return false; }
  });
  return dup;
}
function validarCamposCabeceraOC(){
  if ($("#proveedor_compra_lst").val() === "0" || !$("#proveedor_compra_lst").val()){
    return "Debes seleccionar un proveedor.";
  }
  if (!$("#fecha").val()){
    return "Debes seleccionar una fecha.";
  }
  if ($("#fecha").val() < dameFechaActualSQL()){
    return "La fecha no puede ser menor a la fecha actual.";
  }
  return null;
}

/* ===================== Vistas ===================== */
function mostrarListarOrdenCompra() {
  const contenido = dameContenido("paginas/movimientos/compra/orden_compra/listar.php");
  $(".contenido-principal").html(contenido);
  cargarTablaOrdenCompra();
}

function cargarListaProducto(componente) {
  const datos = ejecutarAjax("controladores/proyecto.php", "leer=1");
  let option = "<option value='0'>Selecciona un Producto</option>";
  if (datos && datos !== "0") {
    const json_datos = parseJSONSafe(datos) || [];
    json_datos.forEach(item => {
      option += `<option value='${item.cod_producto}'>${item.nombre}</option>`;
    });
  }
  $(componente).html(option);
}

function mostrarAgregarOrdenCompra() {
  const contenido = dameContenido("paginas/movimientos/compra/orden_compra/agregar.php");
  $(".contenido-principal").html(contenido);

  // Fecha por defecto
  dameFechaActual("fecha");

  // Siguiente número
  const ultimo = ejecutarAjax("controladores/orden_compra.php", "ultimo_registro=1");
  if (ultimo === "0") {
    $("#cod").val("1");
  } else {
    const json_ultimo = parseJSONSafe(ultimo) || {};
    $("#cod").val(quitarDecimalesConvertir(json_ultimo['cod_orden']) + 1);
  }

  // Combos
  cargarListaProducto("#producto_lst");
  cargarListaProveedorActivos("#proveedor_compra_lst");
  cargarListaPresupuestoPendientes("#presupuesto_compra_lst");
}

/* ===================== Acciones UI ===================== */
function cancelarOrdenCompra() {
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
      mostrarListarOrdenCompra();
    }
  });
}

function agregarTablaOrdenCompra() {
  const idProd = $("#producto_lst").val();
  if (idProd === "0" || !idProd){
    mensaje_dialogo_info_ERROR("Debes seleccionar un producto", "ATENCION");
    return;
  }

  if ($("#cantidad_txt").val().trim().length === 0){
    mensaje_dialogo_info_ERROR("Debes ingresar una cantidad", "ATENCION");
    $("#cantidad_txt").focus();
    return;
  }
  const cantidad = toIntPY($("#cantidad_txt").val());
  if (!isPosInt(cantidad)){
    mensaje_dialogo_info_ERROR("La cantidad debe ser un entero mayor a 0", "ATENCION");
    $("#cantidad_txt").focus();
    return;
  }

  if ($("#precio_txt").val().trim().length === 0){
    mensaje_dialogo_info_ERROR("Debes ingresar un precio", "ATENCION");
    $("#precio_txt").focus();
    return;
  }
  const precio   = toIntPY($("#precio_txt").val());
  if (!isPosInt(precio)){
    mensaje_dialogo_info_ERROR("El precio debe ser un entero mayor a 0", "ATENCION");
    $("#precio_txt").focus();
    return;
  }

  if (productoYaEnTabla(idProd)){
    mensaje_dialogo_info_ERROR("El producto ya fue agregado.", "ATENCION");
    return;
  }

  const nombre = $("#producto_lst option:selected").text().split(" | ")[0];
  const total = cantidad * precio;

  $("#orden_compra_compra").append(`
    <tr>
      <td>${idProd}</td>
      <td>${nombre}</td>
      <td><input class="form-control costo-presu" value="${formatearNumero(precio)}"></td>
      <td>${formatearNumero(cantidad)}</td>
      <td>${formatearNumero(total)}</td>
      <td>
        <button class="btn btn-danger remover-item-orden_compra">Remover</button>
      </td>
    </tr>
  `);

  // limpiar inputs de línea
  $("#producto_lst").val("0");
  $("#cantidad_txt").val("1");
  $("#precio_txt").val("1");

  calcularTotalOrdenCompra();
}

function calcularTotalOrdenCompra() {
  let total = 0;
  $("#orden_compra_compra tr").each(function () {
    total += quitarDecimalesConvertir($(this).find("td:eq(4)").text());
  });
  $("#total").text(formatearNumero(total));
}

/* Quitar fila */
$(document).on("click", ".remover-item-orden_compra", function () {
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
      calcularTotalOrdenCompra();
    }
  });
});

/* Forzar dígitos y recalcular total al editar precio por fila */
$(document).on("input", ".costo-presu", function(){
  this.value = this.value.replace(/\D/g, '');
});
$(document).on("blur", ".costo-presu", function(){
  let v = toIntPY($(this).val()); // antes quitarDecimalesConvertir
  if (!isPosInt(v)){
    mensaje_dialogo_info_ERROR("El precio por ítem debe ser mayor a 0", "ATENCION");
    v = 1;
  }
  $(this).val(formatearNumero(v));
  const $tr = $(this).closest("tr");
  const cant = toIntPY($tr.find("td:eq(3)").text()); // desformatea cantidad
  $tr.find("td:eq(4)").text(formatearNumero(v * cant));
  calcularTotalOrdenCompra();
});

/* ===================== Guardar ===================== */
function toIntPY(s){ const n = parseInt(String(s||'').replace(/[^\d-]/g,''),10); return Number.isFinite(n)?n:0; }

function guardarOrdenCompra() {
  const errCab = validarCamposCabeceraOC();
  if (errCab){ mensaje_dialogo_info_ERROR(errCab,"ATENCION"); return; }

  const $filas = $("#orden_compra_compra tr");
  if ($filas.length === 0){
    mensaje_dialogo_info_ERROR("Debes agregar al menos un producto.","ATENCION");
    return;
  }

  // 1) Guardar cabecera -> recibimos ID real de la BD
  const cabecera = {
    'fecha_orden': $("#fecha").val(),
    'cod_presupuesto_comp': 1, // si no usás presupuesto, poné 0
    'cod_proveedor': $("#proveedor_compra_lst").val(),
    'estado': 'PENDIENTE'
  };
  const rCab = ejecutarAjax("controladores/orden_compra.php", "guardar=" + JSON.stringify(cabecera));
  const jCab = parseJSONSafe(rCab);
  if (!jCab || jCab.ok !== true || !jCab.id){
    mensaje_dialogo_info_ERROR("No se pudo guardar la cabecera.","ATENCION");
    console.error("CAB-ERR =>", rCab);
    return;
  }
  const idOC = parseInt(jCab.id,10);
  $("#cod").val(idOC); // por si lo querés mostrar

  // 2) Preparar lote de detalles con el ID real
  const lote = [];
  let errorFila = null;

  $filas.each(function(){
    const idProd = toIntPY($(this).find("td:eq(0)").text());
    const costo  = toIntPY($(this).find("input.costo-presu").val());
    const cant   = toIntPY($(this).find("td:eq(3)").text());

    if(!idProd){ errorFila = "Fila sin producto"; return false; }
    if(cant<=0){ errorFila = "Cantidad debe ser > 0"; return false; }
    if(costo<=0){ errorFila = "Precio debe ser > 0"; return false; }

    lote.push({ cod_orden: idOC, cod_producto: idProd, cantidad: cant, costo: costo });
  });

  if (errorFila){
    mensaje_dialogo_info_ERROR(errorFila,"ATENCION");
    return;
  }

  // 3) Guardar lote
  const rDet = ejecutarAjax("controladores/orden_compra.php", "guardar_detalles=" + encodeURIComponent(JSON.stringify(lote)));
  const jDet = parseJSONSafe(rDet);
  if (!jDet || jDet.ok !== true){
    mensaje_dialogo_info_ERROR("No se pudieron guardar los detalles.","ATENCION");
    console.error("DET-ERR =>", rDet);
    return;
  }

  mensaje_confirmacion("Se ha guardado correctamente","GUARDADO");
  mostrarListarOrdenCompra();
}


/* ===================== Listado / Búsqueda ===================== */
function cargarTablaOrdenCompra() {
  $("#orden_compra_compra").html(`
    <tr><td colspan="6" class="text-center text-muted py-3">
      <span class="spinner-border spinner-border-sm"></span> Cargando...
    </td></tr>`);

  const desde = $("#fecha_desde_oc").val();
  const hasta = $("#fecha_hasta_oc").val();
  const estado = $("#estado_lst_oc").val();

  if (desde && hasta && desde > hasta) {
    mensaje_dialogo_info_ERROR("La fecha desde no puede ser mayor a la hasta", "ATENCION");
    return;
  }

  const params = new URLSearchParams();
  params.append("leer", 1);
  if (desde) params.append("desde", desde);
  if (hasta) params.append("hasta", hasta);
  if (estado) params.append("estado", estado);

  const raw = ejecutarAjax("controladores/orden_compra.php", params.toString());
  console.log("OC leer =>", raw);

  const arr = toArrayResponse(raw);
  if (!arr.length){
    $("#orden_compra_compra").html(`
      <tr><td colspan="6" class="text-center text-muted py-3">No hay registros</td></tr>`);
    return;
  }

  let html = "";
  arr.forEach(item=>{
    const proveedor = item.nom_ape_prov || item.pro_razonsocial || item.proveedor || "-";
    const total     = item.total ?? item.total_general ?? 0;
    const estado    = String(item.estado||"").toUpperCase();
    const badge     = estado==="PENDIENTE" ? "info" : (estado==="ANULADO" ? "danger" : "success");

    html += `
      <tr>
        <td>${item.cod_orden_compra}</td>
        <td>${item.fecha_orden}</td>
        <td>${proveedor}</td>
        <td>${formatearNumero(total)}</td>
        <td><span class="badge badge-${badge}">${estado}</span></td>
        <td>
          <button onclick="imprimirOrdenCompra(${item.cod_orden_compra}); return false;"
                  class='btn btn-warning btn-sm imprimir-orden_compra'>
            <i class='typcn typcn-printer'></i>
          </button>
          <button ${(estado==="CONFIRMADO"||estado==="ANULADO") ? "disabled" : ""}
                  class='btn btn-danger btn-sm anular-orden_compra'>
            <i class='typcn typcn-delete'></i>
          </button>
        </td>
      </tr>`;
  });

  $("#orden_compra_compra").html(html);
}

$(document).on("keyup", "#b_cliente2", function () {
  const q = $("#b_cliente2").val().trim();
  const desde = $("#fecha_desde_oc").val();
  const hasta = $("#fecha_hasta_oc").val();
  const estado = $("#estado_lst_oc").val();

  if (desde && hasta && desde > hasta) {
    mensaje_dialogo_info_ERROR("La fecha desde no puede ser mayor a la hasta", "ATENCION");
    return;
  }

  const params = new URLSearchParams();
  params.append("leer_buscar", q);
  if (desde) params.append("desde", desde);
  if (hasta) params.append("hasta", hasta);
  if (estado) params.append("estado", estado);

  const raw = ejecutarAjax("controladores/orden_compra.php", params.toString());
  console.log("OC buscar =>", raw);

  const arr = toArrayResponse(raw);
  if (!arr.length){
    $("#orden_compra_compra").html(`
      <tr><td colspan="6" class="text-center text-muted py-3">Sin resultados</td></tr>`);
    return;
  }

  let html = "";
  arr.forEach(item=>{
    const proveedor = item.nom_ape_prov || item.pro_razonsocial || item.proveedor || "-";
    const total     = item.total ?? item.total_general ?? 0;
    const estado    = String(item.estado||"").toUpperCase();
    const badge     = estado==="PENDIENTE" ? "info" : (estado==="ANULADO" ? "danger" : "success");

    html += `
      <tr>
        <td>${item.cod_orden_compra}</td>
        <td>${item.fecha_orden}</td>
        <td>${proveedor}</td>
        <td>${formatearNumero(total)}</td>
        <td><span class="badge badge-${badge}">${estado}</span></td>
        <td>
          <button onclick="imprimirOrdenCompra(${item.cod_orden_compra}); return false;"
                  class='btn btn-warning btn-sm imprimir-orden_compra'>
            <i class='typcn typcn-printer'></i>
          </button>
          <button ${(estado==="CONFIRMADO"||estado==="ANULADO") ? "disabled" : ""}
                  class='btn btn-danger btn-sm anular-orden_compra'>
            <i class='typcn typcn-delete'></i>
          </button>
        </td>
      </tr>`;
  });

  $("#orden_compra_compra").html(html);
});

$(document).on("change", "#fecha_desde_oc, #fecha_hasta_oc", function(){
  const q = $("#b_cliente2").val().trim();
  if (q) {
    $("#b_cliente2").trigger("keyup");
  } else {
    cargarTablaOrdenCompra();
  }
});

$(document).on("change", "#estado_lst_oc", function(){
  const q = $("#b_cliente2").val().trim();
  if (q){
    $("#b_cliente2").trigger("keyup");
  } else {
    cargarTablaOrdenCompra();
  }
});

/* ===================== Otros eventos ===================== */
function imprimirOrdenCompra(id) {
  window.open("paginas/movimientos/compra/orden_compra/print.php?id=" + id);
}

$(document).on("change", "#producto_lst", function () {
  if ($("#producto_lst").val() === "0") {
    $("#precio_txt").val("1");
  } else {
    const producto = ejecutarAjax("controladores/proyecto.php", "id=" + $("#producto_lst").val());
    if (producto && producto !== "0") {
      const json_producto = parseJSONSafe(producto) || {};
      $("#precio_txt").val(formatearNumero(json_producto['precio'] || 1));
    }
  }
});

$(document).on("change", "#presupuesto_compra_lst", function () {
  if ($("#presupuesto_compra_lst").val() === "0") {
    $("#orden_compra_compra").html("");
    $("#proveedor_compra_lst").val("0");
  } else {
    const cabecera = ejecutarAjax("controladores/presupuesto.php", "id=" + $("#presupuesto_compra_lst").val());
    const json_cabecera = parseJSONSafe(cabecera) || {};
    $("#proveedor_compra_lst").val(json_cabecera['cod_proveedor'] || "0");

    const data = ejecutarAjax("controladores/presupuesto_detalle.php", "id=" + $("#presupuesto_compra_lst").val());
    if (!data || data === "0") {
      $("#orden_compra_compra").html("");
    } else {
      const json_data = parseJSONSafe(data) || [];
      $("#orden_compra_compra").html("");
      let omitidos = 0;
      json_data.forEach(item=>{
        if (productoYaEnTabla(item.cod_material)) { omitidos++; return; }
        const costo = quitarDecimalesConvertir(item.costo);
        const cant  = quitarDecimalesConvertir(item.cantidad);
        $("#orden_compra_compra").append(`
          <tr>
            <td>${item.cod_material}</td>
            <td>${item.nombre_material}</td>
            <td><input class="form-control costo-presu" value="${formatearNumero(costo)}"></td>
            <td>${formatearNumero(cant)}</td>
            <td>${formatearNumero(costo * cant)}</td>
            <td>
              <button class="btn btn-danger remover-item-orden_compra">Remover</button>
            </td>
          </tr>
        `);
      });
      if (omitidos > 0){
        mensaje_dialogo_info_ERROR(`Se omitieron ${omitidos} productos repetidos`, "ATENCION");
      }
    }
  }
  calcularTotalOrdenCompra();
});

$(document).on("click", ".anular-orden_compra", function () {
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
      const response = ejecutarAjax("controladores/orden_compra.php","anular=" + id);
      console.log(response);
      mensaje_confirmacion("Anulado Correctamente", "Eliminado");
      mostrarListarOrdenCompra();
    }
  });
});

// Quita todo menos dígitos (maneja 1.000, 1,000, espacios, etc.)
function unformatPYStr(s){ return String(s||'').replace(/[^\d-]/g, ''); }
function toIntPY(s){
  const n = parseInt(unformatPYStr(s), 10);
  return Number.isFinite(n) ? n : 0;
}

// Acepta {ok:true}, "1", o cualquier string no-vacío que no sea "0"
function isOkResponse(raw){
  const js = parseJSONSafe(raw);
  if (js && js.ok) return true;
  if (raw === 1 || raw === "1") return true;
  if (typeof raw === "string" && raw.trim() !== "" && raw.trim() !== "0") return true;
  return false;
}
