<?php
session_start();
require "conexion.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

$stmt = $conn->prepare("UPDATE usuarios SET activo = 0 WHERE usuario_id=?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->close();

session_destroy();

header("Location: index.php?bienvenido=Cuenta+eliminada");
exit;
