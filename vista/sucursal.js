function cargarListasSucursal(componente) {
    let datos = ejecutarAjax("controladores/sucursal.php", "leer_sucursal_activos=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona una sucursal</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.id_sucursal}'>${item.suc_descripcion}</option>`;
        });
    }
    $(componente).html(option);
}

