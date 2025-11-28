<?php
header('Content-Type: application/json');
include_once __DIR__ . '/coneccion.php';

if (!isset($_POST['alias']) || !isset($_POST['contrasena'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Parámetros incompletos'
    ]);
    exit;
}

$alias = $_POST['alias'];
$contrasena = $_POST['contrasena'];

$sql = "SELECT usuario_id, alias, password_hash, rol, activo 
        FROM usuarios 
        WHERE alias = ? AND activo = 1";

$stmt = $con->prepare($sql);
$stmt->bind_param("s", $alias);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();

    if (password_verify($contrasena, $row['password_hash'])) {

        echo json_encode([
            'success' => true,
            'usuario_id' => $row['usuario_id'],
            'alias' => $row['alias'],
            'rol' => $row['rol'],
            'activo' => $row['activo']
        ]);

    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Contraseña incorrecta'
        ]);
    }

} else {

    echo json_encode([
        'success' => false,
        'message' => 'Usuario no encontrado o desactivado'
    ]);
}

$stmt->close();
$con->close();
?>
