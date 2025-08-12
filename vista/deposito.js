function cargarListaDeposito(componente) {
    let datos = ejecutarAjax("controladores/deposito.php", "leer_activos=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona una deposito</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_deposito}'>${item.nombre_deposito}</option>`;
        });
    }
    $(componente).html(option);
}

