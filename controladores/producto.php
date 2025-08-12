<?php
require_once '../conexion/db.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $pdo = (new DB())->conectar();

    $query = $pdo->prepare("SELECT iva FROM producto WHERE cod_producto = :id LIMIT 1");
    $query->execute(['id' => $id]);
    $producto = $query->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        echo json_encode($producto);
    } else {
        echo json_encode(['error' => 'Producto no encontrado']);
    }
} else {
    echo json_encode(['error' => 'No se recibió ID']);
}