<?php

require_once '../conexion/db.php';

if (isset($_POST['leer_usuario_activos'])) {
    leer_usuario_activos();
}

function leer_usuario_activos() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare("SELECT cod_usuario, nombre_apellido, nick_name, password, estado, cod_rol, intentos, limite_intentos
FROM usuarios
");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}