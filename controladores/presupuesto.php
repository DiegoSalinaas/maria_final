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
        $stmt = $pdo->prepare("INSERT INTO presupuestos(fecha_emision,fecha_vencimiento,id_cliente,estado,observaciones,subtotal_servicios,subtotal_insumos,total,created_at) VALUES (?,?,?,?,?,?,?,?,NOW())");
        $stmt->execute([
            $cab['fecha_emision'],
            $cab['fecha_vencimiento'],
            $cab['id_cliente'],
            $cab['estado'],
            $cab['observaciones'],
            $cab['subtotal_servicios'],
            $cab['subtotal_insumos'],
            $cab['total']
        ]);
        $id = $pdo->lastInsertId();
        if(!empty($datos['servicios'])){
            $stmtServ = $pdo->prepare("INSERT INTO presupuesto_servicios(id_presupuesto,tipo_servicio,descripcion,cantidad,precio_unitario,descuento,total_linea) VALUES (?,?,?,?,?,?,?)");
            foreach($datos['servicios'] as $s){
                $stmtServ->execute([$id,$s['tipo_servicio'],$s['descripcion'],$s['cantidad'],$s['precio_unitario'],$s['descuento'],$s['total_linea']]);
            }
        }
        if(!empty($datos['insumos'])){
            $stmtIns = $pdo->prepare("INSERT INTO presupuesto_insumos(id_presupuesto,descripcion,marca,modelo,cantidad,precio_unitario,total_linea) VALUES (?,?,?,?,?,?,?)");
            foreach($datos['insumos'] as $i){
                $stmtIns->execute([$id,$i['descripcion'],$i['marca'],$i['modelo'],$i['cantidad'],$i['precio_unitario'],$i['total_linea']]);
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
    $query = $db->conectar()->prepare("SELECT p.id_presupuesto,p.fecha_emision,c.descripcion AS cliente,p.total,p.estado FROM presupuestos p JOIN cliente c ON c.id_cliente=p.id_cliente");
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
    $cab = $pdo->prepare("SELECT * FROM presupuestos WHERE id_presupuesto=?");
    $cab->execute([$id]);
    if(!$cab->rowCount()){
        echo '0';
        return;
    }
    $cabecera = $cab->fetch(PDO::FETCH_ASSOC);
    $serv = $pdo->prepare("SELECT * FROM presupuesto_servicios WHERE id_presupuesto=?");
    $serv->execute([$id]);
    $ins = $pdo->prepare("SELECT * FROM presupuesto_insumos WHERE id_presupuesto=?");
    $ins->execute([$id]);
    echo json_encode([
        'cabecera'=>$cabecera,
        'servicios'=>$serv->fetchAll(PDO::FETCH_ASSOC),
        'insumos'=>$ins->fetchAll(PDO::FETCH_ASSOC)
    ]);
}

function ultimo_registro(){
    $db = new DB();
    $query = $db->conectar()->prepare("SELECT id_presupuesto FROM presupuestos ORDER BY id_presupuesto DESC LIMIT 1");
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
        $stmt = $pdo->prepare("UPDATE presupuestos SET fecha_emision=?,fecha_vencimiento=?,id_cliente=?,estado=?,observaciones=?,subtotal_servicios=?,subtotal_insumos=?,total=? WHERE id_presupuesto=?");
        $stmt->execute([
            $cab['fecha_emision'],
            $cab['fecha_vencimiento'],
            $cab['id_cliente'],
            $cab['estado'],
            $cab['observaciones'],
            $cab['subtotal_servicios'],
            $cab['subtotal_insumos'],
            $cab['total'],
            $cab['id_presupuesto']
        ]);
        $pdo->prepare("DELETE FROM presupuesto_servicios WHERE id_presupuesto=?")->execute([$cab['id_presupuesto']]);
        $pdo->prepare("DELETE FROM presupuesto_insumos WHERE id_presupuesto=?")->execute([$cab['id_presupuesto']]);
        if(!empty($datos['servicios'])){
            $stmtServ = $pdo->prepare("INSERT INTO presupuesto_servicios(id_presupuesto,tipo_servicio,descripcion,cantidad,precio_unitario,descuento,total_linea) VALUES (?,?,?,?,?,?,?)");
            foreach($datos['servicios'] as $s){
                $stmtServ->execute([$cab['id_presupuesto'],$s['tipo_servicio'],$s['descripcion'],$s['cantidad'],$s['precio_unitario'],$s['descuento'],$s['total_linea']]);
            }
        }
        if(!empty($datos['insumos'])){
            $stmtIns = $pdo->prepare("INSERT INTO presupuesto_insumos(id_presupuesto,descripcion,marca,modelo,cantidad,precio_unitario,total_linea) VALUES (?,?,?,?,?,?,?)");
            foreach($datos['insumos'] as $i){
                $stmtIns->execute([$cab['id_presupuesto'],$i['descripcion'],$i['marca'],$i['modelo'],$i['cantidad'],$i['precio_unitario'],$i['total_linea']]);
            }
        }
        $pdo->commit();
        echo $cab['id_presupuesto'];
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
        $pdo->prepare("DELETE FROM presupuesto_servicios WHERE id_presupuesto=?")->execute([$id]);
        $pdo->prepare("DELETE FROM presupuesto_insumos WHERE id_presupuesto=?")->execute([$id]);
        $pdo->prepare("DELETE FROM presupuestos WHERE id_presupuesto=?")->execute([$id]);
        $pdo->commit();
        echo '1';
    }catch(Exception $e){
        $pdo->rollBack();
        echo '0';
    }
}
?>
