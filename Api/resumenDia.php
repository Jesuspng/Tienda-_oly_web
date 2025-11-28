<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

include_once __DIR__ . '/coneccion.php';

if (!isset($con) || $con->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la Base de Datos"]);
    exit;
}


$queryVentas = "SELECT IFNULL(SUM(total), 0) AS total_ventas FROM ventas WHERE DATE(fecha_hora) = CURDATE()";
$queryRetiros = "SELECT IFNULL(SUM(monto), 0) AS total_retiros FROM retiros_caja WHERE DATE(fecha_hora) = CURDATE()";

$resVentas = $con->query($queryVentas);
$resRetiros = $con->query($queryRetiros);

if (!$resVentas || !$resRetiros) {
    echo json_encode(["success" => false, "message" => "Error en la consulta SQL de totales: " . $con->error]);
    exit;
}

$rowVentas = $resVentas->fetch_assoc();
$rowRetiros = $resRetiros->fetch_assoc();

$totalVentas = $rowVentas['total_ventas'];
$totalRetiros = $rowRetiros['total_retiros'];
$saldoFinal = $totalVentas - $totalRetiros;

$listaVentas = [];
$queryListaVentas = "
    SELECT 
        v.venta_id, 
        v.fecha_hora, 
        v.total, 
        u.alias AS nombre_cajero 
    FROM ventas v
    JOIN usuarios u ON v.usuario_id = u.usuario_id
    WHERE DATE(v.fecha_hora) = CURDATE()
    ORDER BY v.fecha_hora DESC";
    
$ventasHoy = $con->query($queryListaVentas);

if (!$ventasHoy) {

    echo json_encode(["success" => false, "message" => "Error en la consulta de ventas: " . $con->error]);
    $con->close();
    exit;
}

if ($ventasHoy->num_rows > 0) {
    while ($fila = $ventasHoy->fetch_assoc()) {
       
        $fila['total'] = (float)$fila['total'];
        $listaVentas[] = $fila;
    }
}

$listaRetiros = [];
$queryListaRetiros = "
    SELECT 
        r.retiro_id, 
        r.fecha_hora, 
        r.monto, 
        r.descripcion, 
        u.alias AS nombre_cajero 
    FROM retiros_caja r
    JOIN usuarios u ON r.usuario_id = u.usuario_id
    WHERE DATE(r.fecha_hora) = CURDATE()
    ORDER BY r.fecha_hora DESC";

$retirosHoy = $con->query($queryListaRetiros);

if (!$retirosHoy) {

    echo json_encode(["success" => false, "message" => "Error en la consulta de retiros: " . $con->error]);
    $con->close();
    exit;
}

if ($retirosHoy->num_rows > 0) {
    while ($fila = $retirosHoy->fetch_assoc()) {

        $fila['monto'] = (float)$fila['monto'];
        $listaRetiros[] = $fila;
    }
}


$response = [
    "success" => true,
    "resumen" => [
        "totalVentas"  => (float)$totalVentas,
        "totalRetiros" => (float)$totalRetiros,
        "saldoFinal"   => (float)$saldoFinal
    ],
    "lista_ventas" => $listaVentas, 
    "lista_retiros" => $listaRetiros
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$con->close();
?>