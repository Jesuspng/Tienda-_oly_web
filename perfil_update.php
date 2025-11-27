<?php
session_start();
require "conexion.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

$alias = trim($_POST["alias"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$email = trim($_POST["email"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$direccion = trim($_POST["direccion"] ?? "");
$fecha_nacimiento = $_POST["fecha_nacimiento"] ?? "";

if ($alias === "" || $email === "") {
    header("Location: perfil.php?err=Alias+y+email+son+obligatorios");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: perfil.php?err=Email+inválido");
    exit;
}

$stmt = $conn->prepare("UPDATE usuarios SET alias = ? WHERE usuario_id = ?");
$stmt->bind_param("si", $alias, $usuario_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("
    UPDATE clientes
    SET nombre=?, apellido=?, email=?, telefono=?, direccion=?, fecha_nacimiento=?
    WHERE usuario_id=?
");
$stmt->bind_param("ssssssi", $nombre, $apellido, $email, $telefono, $direccion, $fecha_nacimiento, $usuario_id);
$stmt->execute();
$stmt->close();

$_SESSION["alias"] = $alias;

header("Location: perfil.php?msg=Datos+actualizados");
exit;
