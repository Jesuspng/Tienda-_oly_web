<?php
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');

include_once __DIR__ . '/coneccion.php';

if (!isset($con) || $con->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la Base de Datos"]);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "Falta el ID del usuario"]);
    exit;
}

$usuario_id = $_GET['id'];

$sql = "
    SELECT 
        u.alias, 
        u.rol, 
        c.nombre, 
        c.apellido, 
        c.email, 
        c.telefono, 
        c.direccion, 
        c.fecha_nacimiento, 
        c.fecha_registro
    FROM usuarios u
    INNER JOIN clientes c ON u.usuario_id = c.usuario_id
    WHERE u.usuario_id = ?
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $usuario_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $datos = $result->fetch_assoc();
        
        echo json_encode([
            "success" => true,
            "perfil" => [
                "alias" => $datos['alias'],
                "rol" => $datos['rol'],
                "nombre" => $datos['nombre'],
                "apellido" => $datos['apellido'],
                "email" => $datos['email'],
                "telefono" => $datos['telefono'],
                "direccion" => $datos['direccion'],
                "fecha_nacimiento" => $datos['fecha_nacimiento'],
                "fecha_registro" => $datos['fecha_registro']
            ]
        ], JSON_UNESCAPED_UNICODE);
        
    } else {
        echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Error en la consulta SQL"]);
}

$stmt->close();
$con->close();
?>