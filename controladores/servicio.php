<?php
require_once '../conexion/db.php';

if(isset($_POST['guardar'])){
    guardar($_POST['guardar']);
}
if(isset($_POST['leer'])){
    leer();
}
if(isset($_POST['id'])){
    id($_POST['id']);
}
if(isset($_POST['ultimo_registro'])){
    ultimo_registro();
}
if(isset($_POST['actualizar'])){
    actualizar($_POST['actualizar']);
}
if(isset($_POST['eliminar'])){
    eliminar($_POST['eliminar']);
}

function guardar($lista){
    $datos = json_decode($lista,true);
    $db = new DB();
    $pdo = $db->conectar();
    try{
        $pdo->beginTransaction();
        $cab = $datos['cabecera'];
        $stmt = $pdo->prepare("INSERT INTO servicios(id_cliente,ci_cliente,telefono_cliente,email_cliente,fecha_servicio,estado,tecnico,observaciones,total,created_at) VALUES (?,?,?,?,?,?,?,?,?,NOW())");
        $stmt->execute([
            $cab['id_cliente'],
            $cab['ci_cliente'],
            $cab['telefono_cliente'],
            $cab['email_cliente'],
            $cab['fecha_servicio'],
            $cab['estado'],
            $cab['tecnico'],
            $cab['observaciones'],
            $cab['total']
        ]);
        $id = $pdo->lastInsertId();
        if(!empty($datos['detalles'])){
            $stmtDet = $pdo->prepare("INSERT INTO servicio_detalles(id_servicio,tipo_servicio,descripcion,producto_relacionado,cantidad,precio_unitario,subtotal,observaciones) VALUES (?,?,?,?,?,?,?,?)");
            foreach($datos['detalles'] as $d){
                $stmtDet->execute([$id,$d['tipo_servicio'],$d['descripcion'],$d['producto_relacionado'],$d['cantidad'],$d['precio_unitario'],$d['subtotal'],$d['observaciones']]);
            }
        }
        $pdo->commit();
        echo $id;
    }catch(Exception $e){
        $pdo->rollBack();
        echo "0";
    }
}

function leer(){
    $db = new DB();
    $query = $db->conectar()->prepare("SELECT s.id_servicio,s.fecha_servicio,c.nombre_cliente AS cliente,s.total,s.estado FROM servicios s JOIN cliente c ON c.cod_cliente=s.id_cliente");
    $query->execute();
    if($query->rowCount()){
        echo json_encode($query->fetchAll(PDO::FETCH_OBJ));
    }else{
        echo '0';
    }
}

function id($id){
    $db = new DB();
    $pdo = $db->conectar();
    $cab = $pdo->prepare("SELECT * FROM servicios WHERE id_servicio=?");
    $cab->execute([$id]);
    if(!$cab->rowCount()){
        echo '0';
        return;
    }
    $cabecera = $cab->fetch(PDO::FETCH_ASSOC);
    $det = $pdo->prepare("SELECT * FROM servicio_detalles WHERE id_servicio=?");
    $det->execute([$id]);
    echo json_encode([
        'cabecera'=>$cabecera,
        'detalles'=>$det->fetchAll(PDO::FETCH_ASSOC)
    ]);
}

function ultimo_registro(){
    $db = new DB();
    $query = $db->conectar()->prepare("SELECT id_servicio FROM servicios ORDER BY id_servicio DESC LIMIT 1");
    $query->execute();
    if($query->rowCount()){
        echo json_encode($query->fetch(PDO::FETCH_ASSOC));
    }else{
        echo '0';
    }
}

function actualizar($lista){
    $datos = json_decode($lista,true);
    $db = new DB();
    $pdo = $db->conectar();
    try{
        $pdo->beginTransaction();
        $cab = $datos['cabecera'];
        $stmt = $pdo->prepare("UPDATE servicios SET id_cliente=?,ci_cliente=?,telefono_cliente=?,email_cliente=?,fecha_servicio=?,estado=?,tecnico=?,observaciones=?,total=? WHERE id_servicio=?");
        $stmt->execute([
            $cab['id_cliente'],
            $cab['ci_cliente'],
            $cab['telefono_cliente'],
            $cab['email_cliente'],
            $cab['fecha_servicio'],
            $cab['estado'],
            $cab['tecnico'],
            $cab['observaciones'],
            $cab['total'],
            $cab['id_servicio']
        ]);
        $pdo->prepare("DELETE FROM servicio_detalles WHERE id_servicio=?")->execute([$cab['id_servicio']]);
        if(!empty($datos['detalles'])){
            $stmtDet = $pdo->prepare("INSERT INTO servicio_detalles(id_servicio,tipo_servicio,descripcion,producto_relacionado,cantidad,precio_unitario,subtotal,observaciones) VALUES (?,?,?,?,?,?,?,?)");
            foreach($datos['detalles'] as $d){
                $stmtDet->execute([$cab['id_servicio'],$d['tipo_servicio'],$d['descripcion'],$d['producto_relacionado'],$d['cantidad'],$d['precio_unitario'],$d['subtotal'],$d['observaciones']]);
            }
        }
        $pdo->commit();
        echo $cab['id_servicio'];
    }catch(Exception $e){
        $pdo->rollBack();
        echo "0";
    }
}

function eliminar($id){
    $db = new DB();
    $pdo = $db->conectar();
    try{
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM servicio_detalles WHERE id_servicio=?")->execute([$id]);
        $pdo->prepare("DELETE FROM servicios WHERE id_servicio=?")->execute([$id]);
        $pdo->commit();
        echo '1';
    }catch(Exception $e){
        $pdo->rollBack();
        echo '0';
    }
}
?>
