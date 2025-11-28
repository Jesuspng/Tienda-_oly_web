<?php
ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');


include_once __DIR__ . '/coneccion.php';


if (!isset($con) || $con->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la BD"]);
    exit;
}


$json = file_get_contents('php://input');
$data = json_decode($json, true);


if (!isset($data['id']) || empty($data['alias']) || empty($data['email'])) {
    echo json_encode(["success" => false, "message" => "Faltan datos obligatorios (ID, Alias o Email)"]);
    exit;
}


if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Formato de email inválido"]);
    exit;
}

$usuario_id = $data['id'];
$alias = trim($data['alias']);
$nombre = trim($data['nombre'] ?? "");
$apellido = trim($data['apellido'] ?? "");
$email = trim($data['email']);
$telefono = trim($data['telefono'] ?? "");
$direccion = trim($data['direccion'] ?? "");
$fecha_nacimiento = $data['fecha_nacimiento'] ?? NULL;

if (empty($fecha_nacimiento)) {
    $fecha_nacimiento = NULL;
}

$con->begin_transaction();

try {
   
    $stmt1 = $con->prepare("UPDATE usuarios SET alias = ? WHERE usuario_id = ?");
    $stmt1->bind_param("si", $alias, $usuario_id);
    
    if (!$stmt1->execute()) {
        throw new Exception("Error al actualizar el usuario (Alias duplicado o error interno)");
    }
    $stmt1->close();

    $stmt2 = $con->prepare("
        UPDATE clientes 
        SET nombre=?, apellido=?, email=?, telefono=?, direccion=?, fecha_nacimiento=? 
        WHERE usuario_id=?
    ");
    $stmt2->bind_param("ssssssi", $nombre, $apellido, $email, $telefono, $direccion, $fecha_nacimiento, $usuario_id);
    
    if (!$stmt2->execute()) {
        throw new Exception("Error al actualizar datos del cliente");
    }
    $stmt2->close();

    $con->commit();
    echo json_encode(["success" => true, "message" => "Datos actualizados correctamente"]);

} catch (Exception $e) {
    $con->rollback();
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}

$con->close();
?>