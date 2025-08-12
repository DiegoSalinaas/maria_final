<?php

require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

function guardar($lista) {
    //crea un arreglo del texto que se le pasa
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("INSERT INTO `cliente_1`"
            . "(`cod_cliente`, `nombre_cliente`, `ci_cliente`, `ruc`, "
            . "`telefono`, `estado_cliente`, `cod_ciudad`) VALUES (:cod_cliente,"
            . ":nombre_cliente,:ci_cliente,:ruc,:telefono,:estado_cliente,"
            . ":cod_ciudad)");

    $query->execute($json_datos);
}

if(isset($_POST['ultimo_registro'])){
    ultimo_registro();
}

function ultimo_registro(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT 
cod_cliente
FROM cliente_1
order by cod_cliente DESC limit 1");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}


   if (isset($_POST['leer_ciudad_c'])) {
    leer_ciudad_c();
}

function leer_ciudad_c() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare("SELECT `cod_ciudad`, 
        `nombre_ciud`, `estado_ciud` FROM `ciudad` 
        WHERE estado_ciud = 'ACTIVO'
");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------

if(isset($_POST['leer'])){
    leer();
}

function leer(){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT c.`cod_cliente`, c.`nombre_cliente`, c.`ci_cliente`, c.`ruc`, c.`telefono`, c.`estado_cliente`, ciu.nombre_ciud FROM `cliente_1` c
JOIN ciudad ciu
ON ciu.cod_ciudad = c.cod_ciudad
");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
//-----------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------
if(isset($_POST['id'])){
    id($_POST['id']);
}

function id($id){
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("SELECT c.`cod_cliente`, 
        c.`nombre_cliente`, c.`ci_cliente`, 
        c.`ruc`, c.`telefono`, c.`estado_cliente`,
        c.cod_ciudad,
        ciu.nombre_ciud FROM `cliente_1` c
JOIN ciudad ciu
ON ciu.cod_ciudad = c.cod_ciudad
      WHERE c.cod_cliente  = $id ");
    
    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

//----------------------------------------------------------------
//----------------------------------------------------------------
//----------------------------------------------------------------
if(isset($_POST['actualizar'])){
    actualizar($_POST['actualizar']);
}

function actualizar($lista){
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE `cliente_1` "
            . "SET `nombre_cliente`=:nombre_cliente,`ci_cliente`=:ci_cliente,"
            . "`ruc`=:ruc,`telefono`=:telefono,"
            . "`estado_cliente`=:estado_cliente,`cod_ciudad`=:cod_ciudad "
            . "WHERE `cod_cliente`=:cod_cliente");

    $query->execute($json_datos);
}

if(isset($_POST['eliminar'])){
    eliminar($_POST['eliminar']);
}

function eliminar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("DELETE FROM `cliente_1` where cod_cliente = $id");

    $query->execute();
}

//----------------------------------------------------------------------------------
//----------------------------------------------------------------------------------
//----------------------------------------------------------------------------------

if(isset($_POST['desactivar'])){
    desactivar($_POST['desactivar']);
}

function desactivar($id){
   
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare("UPDATE `cliente_1` SET `estado_cliente`='DESACTIVADO' WHERE `cod_cliente`= $id");

    $query->execute();
}



if (isset($_POST["leer_descripcion"])) {
    
    leer_descripcion($_POST["leer_descripcion"]);
   }
   function leer_descripcion ($descripcion){
       $base = new DB();
       $query = $base ->conectar()->prepare("SELECT id_insumo, descripcion, costo_compra, precio_venta, stock, stock_minimo, marca, estado
FROM insumo
WHERE CONCAT(id_insumo, descripcion, costo_compra, precio_venta, stock, stock_minimo, marca, estado) LIKE '%$descripcion%'
ORDER BY id_insumo DESC
LIMIT 50");
       $query ->execute ();
       
      if ($query->rowCount()) {
           print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
       } else {
           echo '0';
       }
   }

   

   if (isset($_POST['leer_ciudad_activos'])) {
    leer_ciudad_activos();
}

function leer_ciudad_activos() {
//    $json_datos = json_decode($lista, true);
    $base_datos = new DB();

    $query = $base_datos->conectar()->prepare(" SELECT `cod_ciudad`, 
    `descripcion_ciud`, `estado` 
    FROM `ciudad`
");

    $query->execute();

    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

if (isset($_POST['leer_descripcion_cliente'])) {
    leer_descripcion_cliente($_POST['leer_descripcion_cliente']);
    exit;
}


function leer_descripcion_cliente($busqueda) {
    header('Content-Type: application/json; charset=utf-8');

    try {
        $base = new DB();
        $query = $base->conectar()->prepare("
            SELECT 
                c.cod_cliente, 
                c.nombre_cliente, 
                c.telefono, 
                c.ci_cliente, 
                c.ruc,
                c.estado_cliente,
                      ci.nombre_ciud,  
                c.cod_ciudad
            FROM cliente_1 c
            JOIN ciudad ci ON ci.cod_ciudad = c.cod_ciudad
            WHERE CONCAT(c.cod_cliente, ' ', c.nombre_cliente, ' ', c.ruc) LIKE :busqueda
        ");

        $query->bindValue(':busqueda', "%$busqueda%");
        $query->execute();

        if ($query->rowCount() > 0) {
            echo json_encode($query->fetchAll(PDO::FETCH_OBJ), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([]); // Retorna array vacío si no hay resultados
        }

    } catch (PDOException $e) {
        echo json_encode(['error' => "Error en la consulta: " . $e->getMessage()]);
    }
}




