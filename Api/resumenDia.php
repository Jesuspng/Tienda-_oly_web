<?php
// 1. ACTIVAR REPORTE DE ERRORES (Solo para depurar, quítalo en producción si quieres)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// 2. CORRECCIÓN DEL INCLUDE (Doble guion bajo __DIR__)
// Asegúrate de que el archivo se llame 'coneccion.php' o 'conexion.php'. 
// En tu código pusiste 'coneccion.php'.
include_once __DIR__ . '/coneccion.php';

// Verificación de seguridad: Si la conexión falló, detener todo.
if (!isset($conn) || $conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la Base de Datos"]);
    exit;
}

// 3. OBTENER TOTALES (Usamos IFNULL para evitar nulos)
$queryVentas = "SELECT IFNULL(SUM(total), 0) AS total_ventas FROM ventas WHERE DATE(fecha_hora) = CURDATE()";
$queryRetiros = "SELECT IFNULL(SUM(monto), 0) AS total_retiros FROM retiros_caja WHERE DATE(fecha_hora) = CURDATE()";

$resVentas = $conn->query($queryVentas);
$resRetiros = $conn->query($queryRetiros);

// Validación extra por si la query falla
if (!$resVentas || !$resRetiros) {
    echo json_encode(["success" => false, "message" => "Error en la consulta SQL: " . $conn->error]);
    exit;
}

$rowVentas = $resVentas->fetch_assoc();
$rowRetiros = $resRetiros->fetch_assoc();

$totalVentas = $rowVentas['total_ventas'];
$totalRetiros = $rowRetiros['total_retiros'];
$saldoFinal = $totalVentas - $totalRetiros;

// 4. OBTENER LISTA DE VENTAS
$listaVentas = [];
$ventasHoy = $conn->query("SELECT venta_id, fecha_hora, usuario_id, total FROM ventas WHERE DATE(fecha_hora) = CURDATE()");

if ($ventasHoy && $ventasHoy->num_rows > 0) {
    while ($fila = $ventasHoy->fetch_assoc()) {
        $listaVentas[] = $fila;
    }
}

// 5. OBTENER LISTA DE RETIROS
$listaRetiros = [];
$retirosHoy = $conn->query("SELECT retiro_id, fecha_hora, usuario_id, monto, descripcion FROM retiros_caja WHERE DATE(fecha_hora) = CURDATE()");

if ($retirosHoy && $retirosHoy->num_rows > 0) {
    while ($fila = $retirosHoy->fetch_assoc()) {
        $listaRetiros[] = $fila;
    }
}

// 6. PREPARAR RESPUESTA (Nombres en camelCase para coincidir con Android)
$response = [
    "success" => true,
    "resumen" => [
        "totalVentas"  => (float)$totalVentas,   // Cambiado de total_ventas a totalVentas
        "totalRetiros" => (float)$totalRetiros,  // Cambiado de total_retiros a totalRetiros
        "saldoFinal"   => (float)$saldoFinal     // Cambiado de saldo_final a saldoFinal
    ],
    "lista_ventas" => $listaVentas,   // Asegúrate que tu modelo en Android use @SerializedName("lista_ventas")
    "lista_retiros" => $listaRetiros
];

echo json_encode($response);
$conn->close();
?>