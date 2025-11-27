<?php
require_once 'conexion.php';

$id = $_POST['id'] ?? null;
$contenido = $_POST['contenido'] ?? null;

if (!$id || $contenido === null) {
    die("Datos incompletos.");
}


$conn->set_charset("utf8mb4");

$stmt = $conn->prepare("UPDATE paginas_estaticas SET contenido = ? WHERE id = ?");
if (!$stmt) {
    die("Error al preparar consulta: " . $conn->error);
}

$stmt->bind_param("si", $contenido, $id);

if (!$stmt->execute()) {
    die("Error al ejecutar: " . $stmt->error);
}

$stmt->close();
$conn->close();

header("Location: admin.php?seccion=paginas&msg=guardado");
exit;
