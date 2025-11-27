<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Cajero') {
    echo "<script>alert('Acceso denegado: solo cajeros.'); window.location.href='login.php';</script>";
    exit;
}

require_once 'conexion.php';
date_default_timezone_set('America/Mexico_City');

$usuario_id = $_SESSION['usuario_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Cajero</title>
    <link rel="stylesheet" href="stylesAdmin.css">
	  <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
  <link rel="manifest" href="favicon_io/site.webmanifest">
</head>
<body>

<header>
    <div class="header-left">
        <img src="logo.png" alt="Logo" />
        <h1>Panel de Cajero</h1>
    </div>

    <nav>
        <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" class="logout">Cerrar sesión</button>
        </form>
    </nav>
</header>

<main>

<h2>Ventas realizadas por ti hoy</h2>

<?php
$sqlVentas = "
    SELECT venta_id, fecha_hora, total
    FROM ventas
    WHERE usuario_id = ? AND DATE(fecha_hora) = CURDATE()
";

$stmt = $conn->prepare($sqlVentas);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resVentas = $stmt->get_result();

if ($resVentas && $resVentas->num_rows > 0) {
    echo "<table>
            <tr>
                <th>ID Venta</th>
                <th>Fecha y Hora</th>
                <th>Total</th>
            </tr>";

    while ($fila = $resVentas->fetch_assoc()) {
        echo "<tr>
                <td>{$fila['venta_id']}</td>
                <td>{$fila['fecha_hora']}</td>
                <td>$" . number_format($fila['total'], 2) . "</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<p>No has realizado ventas hoy.</p>";
}

$stmt->close();

echo "<h2>Retiros de caja realizados por ti hoy</h2>";

$sqlRetiros = "
    SELECT retiro_id, fecha_hora, monto, descripcion
    FROM retiros_caja
    WHERE usuario_id = ? AND DATE(fecha_hora) = CURDATE()
";

$stmt = $conn->prepare($sqlRetiros);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resRetiros = $stmt->get_result();

if ($resRetiros && $resRetiros->num_rows > 0) {
    echo "<table>
            <tr>
                <th>ID Retiro</th>
                <th>Fecha y Hora</th>
                <th>Monto</th>
                <th>Descripción</th>
            </tr>";

    while ($fila = $resRetiros->fetch_assoc()) {
        echo "<tr>
                <td>{$fila['retiro_id']}</td>
                <td>{$fila['fecha_hora']}</td>
                <td>$" . number_format($fila['monto'], 2) . "</td>
                <td>{$fila['descripcion']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<p>No has registrado retiros hoy.</p>";
}

$stmt->close();
$conn->close();
?>

</main>
</body>
</html>
