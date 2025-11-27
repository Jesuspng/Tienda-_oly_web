<?php
include 'conexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin.php?msg=" . urlencode("ID inválido."));
    exit;
}

$id = intval($_GET['id']);

$sql = "DELETE FROM usuarios WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $msg = "Usuario eliminado correctamente.";
} else {
    $msg = "Error al eliminar el usuario.";
}

$stmt->close();
$conn->close();

header("Location: admin.php?msg=" . urlencode($msg));
exit;
?>
