function cargarListasUsuario(componente) {
    let datos = ejecutarAjax("controladores/usuario.php", "leer_usuario_activos=1");
    console.log(datos);
    let option = "<option value='0'>Selecciona un usuario</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.cod_usuario}'>${item.nombre_apellido}</option>`;
        });
    }
    $(componente).html(option);
}

