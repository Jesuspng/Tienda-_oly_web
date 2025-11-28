<?php
header('Content-Type: application/json');
include_once __DIR__ . '/coneccion.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido"
    ]);
    exit;
}

$alias      = $_POST['alias'] ?? '';
$password   = $_POST['password'] ?? '';
$nombre     = $_POST['nombre'] ?? '';
$apellido   = $_POST['apellido'] ?? '';
$email      = $_POST['email'] ?? '';
$telefono   = $_POST['telefono'] ?? '';
$direccion  = $_POST['direccion'] ?? '';
$fecha_nac  = $_POST['fecha_nacimiento'] ?? '';



if ($alias === "" || $password === "" || $nombre === "" || $email === "") {
    echo json_encode([
        "success" => false,
        "message" => "Faltan campos obligatorios"
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Email inválido"
    ]);
    exit;
}

if ($telefono !== "" && !preg_match("/^[0-9]+$/", $telefono)) {
    echo json_encode([
        "success" => false,
        "message" => "Teléfono inválido"
    ]);
    exit;
}

// Validar alias único
$stmt = $con->prepare("SELECT usuario_id FROM usuarios WHERE alias = ?");
$stmt->bind_param("s", $alias);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "El alias ya existe"
    ]);
    exit;
}
$stmt->close();

// Validar email único
$stmt = $con->prepare("SELECT cliente_id FROM clientes WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "El email ya está registrado"
    ]);
    exit;
}
$stmt->close();



$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $con->prepare("
    INSERT INTO usuarios (alias, password_hash, rol, activo)
    VALUES (?, ?, 'Cliente', 1)
");
$stmt->bind_param("ss", $alias, $hash);

if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "message" => "Error al crear usuario",
        "error" => $stmt->error
    ]);
    exit;
}

$usuario_id = $con->insert_id;
$stmt->close();



$stmt = $con->prepare("
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
    $fecha_nac
);

if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "message" => "Error al crear datos del cliente",
        "error" => $stmt->error
    ]);
    exit;
}

$stmt->close();


echo json_encode([
    "success" => true,
    "message" => "Usuario registrado correctamente",
    "usuario_id" => $usuario_id,
    "rol" => "Cliente"
]);

$con->close();
?>
