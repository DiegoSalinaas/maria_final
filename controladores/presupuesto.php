<?php
require_once '../conexion/db.php';

if (isset($_POST['anular'])) anular($_POST['anular']);

if (isset($_POST['guardar']))          guardar($_POST['guardar']);
if (isset($_POST['leer']))             leer();
if (isset($_POST['id']))               id($_POST['id']);
if (isset($_POST['ultimo_registro']))  ultimo_registro();
if (isset($_POST['actualizar']))       actualizar($_POST['actualizar']);
if (isset($_POST['eliminar']))         eliminar($_POST['eliminar']);

function getPDO() {
    $db = new DB();
    $pdo = $db->conectar();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

/**
 * Normaliza strings numéricos como:
 *  - "1.234.567,89"  -> 1234567.89
 *  - "1,234,567.89"  -> 1234567.89
 *  - "1234567"       -> 1234567.00
 */
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
    $datos = json_decode($lista, true);
    $pdo   = getPDO();

    try {
        if (!is_array($datos))               throw new Exception("Payload inválido.");
        if (empty($datos['cabecera']))       throw new Exception("Falta cabecera.");

        $cab = $datos['cabecera'];
        $fecha_emision     = $cab['fecha_emision']     ?? '';
        $fecha_vencimiento = $cab['fecha_vencimiento'] ?? '';
        $id_cliente        = isset($cab['id_cliente']) ? (int)$cab['id_cliente'] : 0;
        $estado            = trim($cab['estado'] ?? '');
        $observaciones     = trim($cab['observaciones'] ?? '');

        $subtotal_servicios = toFloat($cab['subtotal_servicios'] ?? 0);
        $subtotal_insumos   = toFloat($cab['subtotal_insumos']   ?? 0);
        $total              = toFloat($cab['total']              ?? 0);

        // Validaciones mínimas
        if (!$fecha_emision || !$fecha_vencimiento) throw new Exception("Fechas requeridas.");
        if ($id_cliente <= 0)                        throw new Exception("Cliente inválido.");
        if ($estado === '')                          throw new Exception("Estado es requerido.");
        if ($total <= 0)                             throw new Exception("Total debe ser > 0.");

        // Verifica FK a cliente_1(cod_cliente)
        $st = $pdo->prepare("SELECT 1 FROM cliente_1 WHERE cod_cliente = ?");
        $st->execute([$id_cliente]);
        if (!$st->fetchColumn()) throw new Exception("El cliente ($id_cliente) no existe en cliente_1.");

        $pdo->beginTransaction();

        // Insert cabecera (created_at queda por DEFAULT CURRENT_TIMESTAMP)
        $sqlCab = "INSERT INTO presupuestos
            (fecha_emision, fecha_vencimiento, id_cliente, estado, observaciones,
             subtotal_servicios, subtotal_insumos, total)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sqlCab)->execute([
            $fecha_emision,
            $fecha_vencimiento,
            $id_cliente,
            $estado,
            $observaciones !== '' ? $observaciones : null,
            number_format($subtotal_servicios, 2, '.', ''),
            number_format($subtotal_insumos,  2, '.', ''),
            number_format($total,             2, '.', ''),
        ]);

        $id = (int)$pdo->lastInsertId();

        // Insert detalles SERVICIOS (con DESCUENTO)
        if (!empty($datos['servicios']) && is_array($datos['servicios'])) {
            $sqlServ = "INSERT INTO presupuesto_servicios
                (id_presupuesto, tipo_servicio, descripcion, cantidad, precio_unitario, descuento, total_linea)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $ps = $pdo->prepare($sqlServ);

            foreach ($datos['servicios'] as $s) {
                $tipo_servicio   = trim($s['tipo_servicio'] ?? '');
                $descripcion     = trim($s['descripcion']   ?? '');
                $cantidad        = (int)($s['cantidad']     ?? 0);
                $precio_unitario = toFloat($s['precio_unitario'] ?? 0);
                $descuento       = toFloat($s['descuento']       ?? 0);
                // Si no vino total_linea, calculamos (precio*cant - desc), sin negativos
                $calc_total      = max(0, ($cantidad * $precio_unitario) - $descuento);
                $total_linea     = toFloat($s['total_linea'] ?? $calc_total);

                if ($tipo_servicio === '' || $cantidad <= 0 || $precio_unitario <= 0) {
                    throw new Exception("Detalle servicio inválido.");
                }

                $ps->execute([
                    $id,
                    $tipo_servicio,
                    $descripcion !== '' ? $descripcion : null,
                    $cantidad,
                    number_format($precio_unitario, 2, '.', ''),
                    number_format($descuento,       2, '.', ''),
                    number_format($total_linea,     2, '.', ''),
                ]);
            }
        }

        // Insert detalles INSUMOS (si corresponde)
        if (!empty($datos['insumos']) && is_array($datos['insumos'])) {
            $sqlIns = "INSERT INTO presupuesto_insumos
                (id_presupuesto, descripcion, marca, modelo, cantidad, precio_unitario, total_linea)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $pi = $pdo->prepare($sqlIns);

            foreach ($datos['insumos'] as $i) {
                $descripcion     = trim($i['descripcion']   ?? '');
                $marca           = trim($i['marca']         ?? '');
                $modelo          = trim($i['modelo']        ?? '');
                $cantidad        = (int)($i['cantidad']     ?? 0);
                $precio_unitario = toFloat($i['precio_unitario'] ?? 0);
                $calc_total      = $cantidad * $precio_unitario;
                $total_linea     = toFloat($i['total_linea'] ?? $calc_total);

                if ($descripcion === '' || $cantidad <= 0 || $precio_unitario <= 0) {
                    throw new Exception("Detalle insumo inválido.");
                }

                $pi->execute([
                    $id,
                    $descripcion,
                    $marca   !== '' ? $marca   : null,
                    $modelo  !== '' ? $modelo  : null,
                    $cantidad,
                    number_format($precio_unitario, 2, '.', ''),
                    number_format($total_linea,     2, '.', ''),
                ]);
            }
        }

        $pdo->commit();
        echo $id;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
}

function leer(){
    try {
        $pdo = getPDO();

        $buscar = trim($_POST['buscar'] ?? '');
        $desde  = $_POST['fecha_desde'] ?? '';
        $hasta  = $_POST['fecha_hasta'] ?? '';
        $estado = trim($_POST['estado'] ?? '');

        $where = [];
        $pars  = [];

        if ($buscar !== '') {
            // busca por nro de presupuesto o por nombre de cliente
            $where[] = "(p.id_presupuesto = ? OR c.nombre_cliente LIKE ?)";
            $pars[]  = $buscar;
            $pars[]  = "%$buscar%";
        }
        if ($desde !== '') {
            $where[] = "p.fecha_emision >= ?";
            $pars[]  = $desde;
        }
        if ($hasta !== '') {
            $where[] = "p.fecha_emision <= ?";
            $pars[]  = $hasta;
        }
        if ($estado !== '') {
            $where[] = "p.estado = ?";
            $pars[]  = $estado;
        }

        $sql = "
            SELECT p.id_presupuesto,
                   p.fecha_emision,
                   c.nombre_cliente AS cliente,
                   p.total,
                   p.estado
            FROM presupuestos p
            JOIN cliente_1 c ON c.cod_cliente = p.id_cliente
        ";
        if ($where) $sql .= " WHERE ".implode(" AND ", $where);
        $sql .= " ORDER BY p.id_presupuesto DESC";

        $q = $pdo->prepare($sql);
        $q->execute($pars);

        if ($q->rowCount()) echo json_encode($q->fetchAll(PDO::FETCH_OBJ));
        else echo '0';
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}


function id($id){
    try {
        $pdo = getPDO();
        $cab = $pdo->prepare("SELECT * FROM presupuestos WHERE id_presupuesto=?");
        $cab->execute([$id]);
        $cabecera = $cab->fetch(PDO::FETCH_ASSOC);
        if (!$cabecera) { echo '0'; return; }

        $serv = $pdo->prepare("SELECT * FROM presupuesto_servicios WHERE id_presupuesto=?");
        $serv->execute([$id]);

        $ins = $pdo->prepare("SELECT * FROM presupuesto_insumos WHERE id_presupuesto=?");
        $ins->execute([$id]);

        echo json_encode([
            'cabecera'  => $cabecera,
            'servicios' => $serv->fetchAll(PDO::FETCH_ASSOC),
            'insumos'   => $ins->fetchAll(PDO::FETCH_ASSOC)
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}

function ultimo_registro(){
    try {
        $pdo = getPDO();
        $q = $pdo->prepare("SELECT id_presupuesto FROM presupuestos ORDER BY id_presupuesto DESC LIMIT 1");
        $q->execute();
        $row = $q->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo json_encode($row);
        } else {
            echo '0';
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}

function actualizar($lista){
    $datos = json_decode($lista, true);
    $pdo   = getPDO();

    try {
        if (!is_array($datos))               throw new Exception("Payload inválido.");
        if (empty($datos['cabecera']))       throw new Exception("Falta cabecera.");

        $cab = $datos['cabecera'];
        $id_presupuesto    = (int)($cab['id_presupuesto'] ?? 0);
        if ($id_presupuesto <= 0) throw new Exception("ID de presupuesto inválido.");

        $fecha_emision     = $cab['fecha_emision']     ?? '';
        $fecha_vencimiento = $cab['fecha_vencimiento'] ?? '';
        $id_cliente        = isset($cab['id_cliente']) ? (int)$cab['id_cliente'] : 0;
        $estado            = trim($cab['estado'] ?? '');
        $observaciones     = trim($cab['observaciones'] ?? '');

        $subtotal_servicios = toFloat($cab['subtotal_servicios'] ?? 0);
        $subtotal_insumos   = toFloat($cab['subtotal_insumos']   ?? 0);
        $total              = toFloat($cab['total']              ?? 0);

        if (!$fecha_emision || !$fecha_vencimiento) throw new Exception("Fechas requeridas.");
        if ($id_cliente <= 0)                        throw new Exception("Cliente inválido.");
        if ($estado === '')                          throw new Exception("Estado es requerido.");
        if ($total <= 0)                             throw new Exception("Total debe ser > 0.");

        // Verifica FK cliente
        $st = $pdo->prepare("SELECT 1 FROM cliente_1 WHERE cod_cliente = ?");
        $st->execute([$id_cliente]);
        if (!$st->fetchColumn()) throw new Exception("El cliente ($id_cliente) no existe en cliente_1.");

        $pdo->beginTransaction();

        // Update cabecera
        $sqlUp = "UPDATE presupuestos
                  SET fecha_emision=?, fecha_vencimiento=?, id_cliente=?, estado=?, observaciones=?,
                      subtotal_servicios=?, subtotal_insumos=?, total=?
                  WHERE id_presupuesto=?";
        $pdo->prepare($sqlUp)->execute([
            $fecha_emision,
            $fecha_vencimiento,
            $id_cliente,
            $estado,
            $observaciones !== '' ? $observaciones : null,
            number_format($subtotal_servicios, 2, '.', ''),
            number_format($subtotal_insumos,  2, '.', ''),
            number_format($total,             2, '.', ''),
            $id_presupuesto
        ]);

        // Limpia detalles y re-inserta
        $pdo->prepare("DELETE FROM presupuesto_servicios WHERE id_presupuesto=?")->execute([$id_presupuesto]);
        $pdo->prepare("DELETE FROM presupuesto_insumos   WHERE id_presupuesto=?")->execute([$id_presupuesto]);

        // Reinsertar SERVICIOS (con descuento)
        if (!empty($datos['servicios']) && is_array($datos['servicios'])) {
            $sqlServ = "INSERT INTO presupuesto_servicios
                (id_presupuesto, tipo_servicio, descripcion, cantidad, precio_unitario, descuento, total_linea)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $ps = $pdo->prepare($sqlServ);

            foreach ($datos['servicios'] as $s) {
                $tipo_servicio   = trim($s['tipo_servicio'] ?? '');
                $descripcion     = trim($s['descripcion']   ?? '');
                $cantidad        = (int)($s['cantidad']     ?? 0);
                $precio_unitario = toFloat($s['precio_unitario'] ?? 0);
                $descuento       = toFloat($s['descuento']       ?? 0);
                $calc_total      = max(0, ($cantidad * $precio_unitario) - $descuento);
                $total_linea     = toFloat($s['total_linea'] ?? $calc_total);

                if ($tipo_servicio === '' || $cantidad <= 0 || $precio_unitario <= 0) {
                    throw new Exception("Detalle servicio inválido.");
                }

                $ps->execute([
                    $id_presupuesto,
                    $tipo_servicio,
                    $descripcion !== '' ? $descripcion : null,
                    $cantidad,
                    number_format($precio_unitario, 2, '.', ''),
                    number_format($descuento,       2, '.', ''),
                    number_format($total_linea,     2, '.', ''),
                ]);
            }
        }

        // Reinsertar INSUMOS
        if (!empty($datos['insumos']) && is_array($datos['insumos'])) {
            $sqlIns = "INSERT INTO presupuesto_insumos
                (id_presupuesto, descripcion, marca, modelo, cantidad, precio_unitario, total_linea)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $pi = $pdo->prepare($sqlIns);

            foreach ($datos['insumos'] as $i) {
                $descripcion     = trim($i['descripcion']   ?? '');
                $marca           = trim($i['marca']         ?? '');
                $modelo          = trim($i['modelo']        ?? '');
                $cantidad        = (int)($i['cantidad']     ?? 0);
                $precio_unitario = toFloat($i['precio_unitario'] ?? 0);
                $calc_total      = $cantidad * $precio_unitario;
                $total_linea     = toFloat($i['total_linea'] ?? $calc_total);

                if ($descripcion === '' || $cantidad <= 0 || $precio_unitario <= 0) {
                    throw new Exception("Detalle insumo inválido.");
                }

                $pi->execute([
                    $id_presupuesto,
                    $descripcion,
                    $marca   !== '' ? $marca   : null,
                    $modelo  !== '' ? $modelo  : null,
                    $cantidad,
                    number_format($precio_unitario, 2, '.', ''),
                    number_format($total_linea,     2, '.', ''),
                ]);
            }
        }

        $pdo->commit();
        echo $id_presupuesto;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
}

function eliminar($id){
    $pdo = getPDO();
    try {
        $pdo->beginTransaction();
        // Si tus FKs de detalles tienen ON DELETE CASCADE, las dos líneas siguientes son opcionales.
        $pdo->prepare("DELETE FROM presupuesto_servicios WHERE id_presupuesto=?")->execute([$id]);
        $pdo->prepare("DELETE FROM presupuesto_insumos   WHERE id_presupuesto=?")->execute([$id]);
        $pdo->prepare("DELETE FROM presupuestos          WHERE id_presupuesto=?")->execute([$id]);
        $pdo->commit();
        echo '1';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}
function anular($id){
    $pdo = getPDO();
    try {
        $pdo->beginTransaction();

        // Evita re-anular; podés bloquear otros estados si querés (p.ej. APROBADO)
        $st = $pdo->prepare("UPDATE presupuestos SET estado='ANULADO' WHERE id_presupuesto=? AND estado <> 'ANULADO'");
        $st->execute([$id]);

        $pdo->commit();
        echo $st->rowCount() > 0 ? '1' : '0';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}
