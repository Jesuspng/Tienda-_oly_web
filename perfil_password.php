<?php
session_start();
require "conexion.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

$actual = $_POST["actual"] ?? "";
$nueva = $_POST["nueva"] ?? "";
$confirmar = $_POST["confirmar"] ?? "";

if ($nueva !== $confirmar) {
    header("Location: perfil.php?err=Las+contraseñas+no+coinciden");
    exit;
}

$stmt = $conn->prepare("SELECT password_hash FROM usuarios WHERE usuario_id=?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!password_verify($actual, $data["password_hash"])) {
    header("Location: perfil.php?err=La+contraseña+actual+es+incorrecta");
    exit;
}

$hashNuevo = password_hash($nueva, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE usuarios SET password_hash=? WHERE usuario_id=?");
$stmt->bind_param("si", $hashNuevo, $usuario_id);
$stmt->execute();
$stmt->close();

header("Location: perfil.php?msg=Contraseña+actualizada");
exit;
