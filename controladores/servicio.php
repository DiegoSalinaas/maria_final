<?php
// Ruta robusta a la conexión
require_once __DIR__ . '/../conexion/db.php';

if (isset($_POST['guardar']))           guardar($_POST['guardar']);
if (isset($_POST['leer']))              leer();
if (isset($_POST['id']))                id($_POST['id']);
if (isset($_POST['ultimo_registro']))   ultimo_registro();
if (isset($_POST['actualizar']))        actualizar($_POST['actualizar']);
if (isset($_POST['anular']))            anular($_POST['anular']);
if (isset($_POST['eliminar']))          eliminar($_POST['eliminar']);

function getPDO(){
    $db = new DB();
    $pdo = $db->conectar();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

/** Normaliza "1.234.567,89" | "1,234,567.89" | "1234567" -> float */
function toFloat($v) {
    if ($v === null) return 0.0;
    $s = trim((string)$v);
    if ($s === '') return 0.0;
    $lastC = strrpos($s, ',');
    $lastD = strrpos($s, '.');
    $decPos = max((int)$lastC, (int)$lastD);
    if ($decPos > 0) {
        $int  = preg_replace('/[^\d]/', '', substr($s, 0, $decPos));
        $frac = preg_replace('/[^\d]/', '', substr($s, $decPos + 1));
        return floatval($int . '.' . $frac);
    }
    return floatval(preg_replace('/[^\d]/', '', $s));
}

function guardar($lista){
    header('Content-Type: application/json; charset=utf-8');
    $pdo = getPDO();

    try {
        $datos = json_decode($lista, true);
        if (!is_array($datos))             throw new Exception("Payload inválido.");
        if (empty($datos['cabecera']))     throw new Exception("Falta cabecera.");

        $cab = $datos['cabecera'];
        $id_cliente        = (int)($cab['id_cliente'] ?? 0);
        $ci_cliente        = trim($cab['ci_cliente'] ?? '');
        $tel_cliente       = trim($cab['telefono_cliente'] ?? '');
        $fecha_servicio    = trim($cab['fecha_servicio'] ?? '');
        $estado            = trim($cab['estado'] ?? '');
        $tecnico           = trim($cab['tecnico'] ?? '');
        $observaciones     = trim($cab['observaciones'] ?? '');
        $total             = toFloat($cab['total'] ?? 0);

        if ($id_cliente <= 0)         throw new Exception("Cliente inválido.");
        if ($fecha_servicio === '')   throw new Exception("Fecha servicio requerida.");
        if ($estado === '')           throw new Exception("Estado requerido.");
        if ($total <= 0)              throw new Exception("Total debe ser > 0.");

        // FK cliente
        $st = $pdo->prepare("SELECT 1 FROM cliente_1 WHERE cod_cliente=?");
        $st->execute([$id_cliente]);
        if (!$st->fetchColumn()) throw new Exception("El cliente ($id_cliente) no existe en cliente_1.");

        $pdo->beginTransaction();

        // CABECERA (created_at por DEFAULT en la tabla)
        $sql = "INSERT INTO servicios
                (id_cliente, ci_cliente, telefono_cliente, fecha_servicio, estado, tecnico, observaciones, total, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $pdo->prepare($sql)->execute([
            $id_cliente,
            $ci_cliente !== '' ? $ci_cliente : null,
            $tel_cliente !== '' ? $tel_cliente : null,
            $fecha_servicio,
            $estado,
            $tecnico !== '' ? $tecnico : null,
            $observaciones !== '' ? $observaciones : null,
            number_format($total, 2, '.', '')
        ]);

        $id = (int)$pdo->lastInsertId();

        // DETALLES
        if (!empty($datos['detalles']) && is_array($datos['detalles'])) {
            $sqlD = "INSERT INTO servicio_detalles
                    (id_servicio, tipo_servicio, descripcion, producto_relacionado, cantidad, precio_unitario, subtotal, observaciones)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $pd = $pdo->prepare($sqlD);

            foreach ($datos['detalles'] as $d) {
                $tipo  = trim($d['tipo_servicio'] ?? '');
                $desc  = trim($d['descripcion'] ?? '');
                $prod  = trim($d['producto_relacionado'] ?? '');
                $cant  = toFloat($d['cantidad'] ?? 0);  // <-- CORREGIDO
                $prec  = toFloat($d['precio_unitario'] ?? 0);
                $calc  = $cant * $prec;
                $subt  = toFloat($d['subtotal'] ?? $calc);
                $obs   = trim($d['observaciones'] ?? '');

                if ($tipo === '' || $desc === '' || $cant <= 0 || $prec <= 0) {
                    throw new Exception("Detalle inválido.");
                }

                $pd->execute([
                    $id,
                    $tipo,
                    $desc,
                    $prod !== '' ? $prod : null,
                    $cant,
                    number_format($prec, 2, '.', ''),
                    number_format($subt, 2, '.', ''),
                    $obs !== '' ? $obs : null
                ]);
            }
        }

        $pdo->commit();
        echo json_encode(['ok'=>true, 'id'=>$id]);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
    }
}

function leer(){
    header('Content-Type: application/json; charset=utf-8');
    try {
        $pdo   = getPDO();
        $where = '';
        $params = [];

        if (!empty($_REQUEST['estado'])) {
            $where .= ' AND s.estado = :estado';
            $params[':estado'] = $_REQUEST['estado'];
        }

        if (!empty($_REQUEST['buscar'])) {
            $b = '%'.$_REQUEST['buscar'].'%';
            $where .= ' AND (c.nombre_cliente LIKE :b OR s.id_servicio LIKE :b)';
            $params[':b'] = $b;
        }

        $sql = "SELECT s.id_servicio,
                       s.fecha_servicio,
                       c.nombre_cliente AS cliente,
                       s.total,
                       s.estado
                FROM servicios s
                JOIN cliente_1 c ON c.cod_cliente = s.id_cliente
                WHERE 1=1 {$where}
                ORDER BY s.id_servicio DESC";
        $q = $pdo->prepare($sql);
        $q->execute($params);
        $rows = $q->fetchAll(PDO::FETCH_OBJ);
        echo json_encode($rows ?: []);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
    }
}

function id($id){
    header('Content-Type: application/json; charset=utf-8');
    try {
        $pdo = getPDO();
        $cab = $pdo->prepare("SELECT * FROM servicios WHERE id_servicio=?");
        $cab->execute([$id]);
        $cabecera = $cab->fetch(PDO::FETCH_ASSOC);
        if (!$cabecera){ echo json_encode(['ok'=>false,'error'=>'No encontrado']); return; }

        $det = $pdo->prepare("SELECT * FROM servicio_detalles WHERE id_servicio=?");
        $det->execute([$id]);
        echo json_encode([
            'cabecera'=>$cabecera,
            'detalles'=>$det->fetchAll(PDO::FETCH_ASSOC)
        ]);
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}

function ultimo_registro(){
    header('Content-Type: application/json; charset=utf-8');
    try {
        $pdo = getPDO();
        $q = $pdo->prepare("SELECT id_servicio FROM servicios ORDER BY id_servicio DESC LIMIT 1");
        $q->execute();
        $row = $q->fetch(PDO::FETCH_ASSOC);
        echo $row ? json_encode($row) : '0';
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}

function actualizar($lista){
    header('Content-Type: application/json; charset=utf-8');
    $pdo = getPDO();

    try {
        $datos = json_decode($lista, true);
        if (!is_array($datos))           throw new Exception("Payload inválido.");
        if (empty($datos['cabecera']))   throw new Exception("Falta cabecera.");

        $cab = $datos['cabecera'];
        $id_servicio       = (int)($cab['id_servicio'] ?? 0);
        if ($id_servicio <= 0) throw new Exception("ID inválido.");

        $id_cliente        = (int)($cab['id_cliente'] ?? 0);
        $ci_cliente        = trim($cab['ci_cliente'] ?? '');
        $tel_cliente       = trim($cab['telefono_cliente'] ?? '');
        $fecha_servicio    = trim($cab['fecha_servicio'] ?? '');
        $estado            = trim($cab['estado'] ?? '');
        $tecnico           = trim($cab['tecnico'] ?? '');
        $observaciones     = trim($cab['observaciones'] ?? '');
        $total             = toFloat($cab['total'] ?? 0);

        if ($id_cliente <= 0)         throw new Exception("Cliente inválido.");
        if ($fecha_servicio === '')   throw new Exception("Fecha servicio requerida.");
        if ($estado === '')           throw new Exception("Estado requerido.");
        if ($total <= 0)              throw new Exception("Total debe ser > 0.");

        // FK cliente
        $st = $pdo->prepare("SELECT 1 FROM cliente_1 WHERE cod_cliente=?");
        $st->execute([$id_cliente]);
        if (!$st->fetchColumn()) throw new Exception("El cliente ($id_cliente) no existe en cliente_1.");

        $pdo->beginTransaction();

        $sql = "UPDATE servicios
                SET id_cliente=?, ci_cliente=?, telefono_cliente=?, fecha_servicio=?, estado=?, tecnico=?, observaciones=?, total=?
                WHERE id_servicio=?";
        $pdo->prepare($sql)->execute([
            $id_cliente,
            $ci_cliente !== '' ? $ci_cliente : null,
            $tel_cliente !== '' ? $tel_cliente : null,
            $fecha_servicio,
            $estado,
            $tecnico !== '' ? $tecnico : null,
            $observaciones !== '' ? $observaciones : null,
            number_format($total, 2, '.', ''),
            $id_servicio
        ]);

        // Reemplazar detalles
        $pdo->prepare("DELETE FROM servicio_detalles WHERE id_servicio=?")->execute([$id_servicio]);

        if (!empty($datos['detalles']) && is_array($datos['detalles'])) {
            $sqlD = "INSERT INTO servicio_detalles
                    (id_servicio, tipo_servicio, descripcion, producto_relacionado, cantidad, precio_unitario, subtotal, observaciones)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $pd = $pdo->prepare($sqlD);

            foreach ($datos['detalles'] as $d) {
                $tipo  = trim($d['tipo_servicio'] ?? '');
                $desc  = trim($d['descripcion'] ?? '');
                $prod  = trim($d['producto_relacionado'] ?? '');
                $cant  = toFloat($d['cantidad'] ?? 0); // <-- CORREGIDO
                $prec  = toFloat($d['precio_unitario'] ?? 0);
                $calc  = $cant * $prec;
                $subt  = toFloat($d['subtotal'] ?? $calc);
                $obs   = trim($d['observaciones'] ?? '');

                if ($tipo === '' || $desc === '' || $cant <= 0 || $prec <= 0) {
                    throw new Exception("Detalle inválido.");
                }

                $pd->execute([
                    $id_servicio,
                    $tipo,
                    $desc,
                    $prod !== '' ? $prod : null,
                    $cant,
                    number_format($prec, 2, '.', ''),
                    number_format($subt, 2, '.', ''),
                    $obs !== '' ? $obs : null
                ]);
            }
        }

        $pdo->commit();
        echo json_encode(['ok'=>true, 'id'=>$id_servicio]);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}

function anular($id){
    header('Content-Type: application/json; charset=utf-8');
    $pdo = getPDO();
    try {
        $pdo->prepare("UPDATE servicios SET estado='ANULADO' WHERE id_servicio=?")->execute([$id]);
        echo json_encode(['ok'=>true]);
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}

function eliminar($id){
    header('Content-Type: application/json; charset=utf-8');
    $pdo = getPDO();
    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM servicio_detalles WHERE id_servicio=?")->execute([$id]);
        $pdo->prepare("DELETE FROM servicios WHERE id_servicio=?")->execute([$id]);
        $pdo->commit();
        echo json_encode(['ok'=>true]);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}
