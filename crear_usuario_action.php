<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}


session_start();

if (isset($_SESSION["usuario_id"])) {
    switch ($_SESSION["rol"]) {
        case "Admin":
            header("Location: admin.php");
            break;
        case "Cajero":
            header("Location: caja.php");
            break;
        case "Cliente":
            header("Location: index.php");
            break;
        default:
            header("Location: index.php");
    }
    exit;
}

require "conexion.php";

$alias            = trim($_POST["alias"] ?? "");
$password         = $_POST["password"] ?? "";
$nombre           = trim($_POST["nombre"] ?? "");
$apellido         = trim($_POST["apellido"] ?? "");
$email            = trim($_POST["email"] ?? "");
$telefono         = trim($_POST["telefono"] ?? "");
$direccion        = trim($_POST["direccion"] ?? "");
$fecha_nacimiento = $_POST["fecha_nacimiento"] ?? "";

if ($alias === "" || $password === "" || $nombre === "" || $email === "") {
    header("Location: create_user.php?err=Faltan+campos+obligatorios");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: create_user.php?err=Email+inválido");
    exit;
}

if ($telefono !== "" && !preg_match("/^[0-9]+$/", $telefono)) {
    header("Location: create_user.php?err=Teléfono+inválido");
    exit;
}

if (isset($_POST["rol"])) {
    header("Location: create_user.php?err=No+puedes+definir+el+rol");
    exit;
}

$stmt = $conn->prepare("SELECT usuario_id FROM usuarios WHERE alias = ?");
$stmt->bind_param("s", $alias);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: create_user.php?err=El+alias+ya+existe");
    exit;
}
$stmt->close();

$stmt = $conn->prepare("SELECT cliente_id FROM clientes WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: create_user.php?err=El+email+ya+está+registrado");
    exit;
}
$stmt->close();

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("
    INSERT INTO usuarios (alias, password_hash, rol, activo)
    VALUES (?, ?, 'Cliente', 1)
");
$stmt->bind_param("ss", $alias, $hash);

if (!$stmt->execute()) {
    header("Location: create_user.php?err=Error+al+crear+usuario");
    exit;
}

$usuario_id = $conn->insert_id;
$stmt->close();

$stmt = $conn->prepare("
    INSERT INTO clientes (
        usuario_id, nombre, apellido,
        telefono, email, direccion,
        fecha_nacimiento, fecha_registro
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    "issssss",
    $usuario_id,
    $nombre,
    $apellido,
    $telefono,
    $email,
    $direccion,
    $fecha_nacimiento
);

if (!$stmt->execute()) {
    header("Location: create_user.php?err=Error+al+crear+datos+del+cliente");
    exit;
}

$stmt->close();


$_SESSION["usuario_id"] = $usuario_id;
$_SESSION["alias"] = $alias;
$_SESSION["rol"] = "Cliente";

header("Location: index.php?bienvenido=1");
exit;

