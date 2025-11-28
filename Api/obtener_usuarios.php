<?php

ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');


include_once __DIR__ . '/coneccion.php';


if (!isset($con) || $con->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la Base de Datos"]);
    exit;
}


$sql = "SELECT usuario_id, alias, rol, activo 
        FROM usuarios 
        WHERE rol IN ('Admin', 'Cajero') 
        ORDER BY rol ASC, alias ASC";

$res = $con->query($sql);

$listaUsuarios = [];

if ($res && $res->num_rows > 0) {
    while ($fila = $res->fetch_assoc()) {
        
        $datosUsuario = [
            "id" => (string)$fila['usuario_id'], 
            "alias" => $fila['alias'],
            "rol" => $fila['rol'],
            "activo" => (bool)$fila['activo'] 
        ];
        
        $listaUsuarios[] = $datosUsuario;
    }
}

$response = [
    "success" => true,
    "usuarios" => $listaUsuarios
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
$con->close();
?>