<?php
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["token"];
    $usuario_id = $_POST["usuario_id"];
    $pass1 = $_POST["password"];
    $pass2 = $_POST["password2"];

    if ($pass1 !== $pass2) {
        die("Las contraseñas no coinciden.");
    }

    $stmt = $conn->prepare("SELECT reset_token, reset_expira FROM usuarios WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        die("Usuario no encontrado.");
    }

    $row = $res->fetch_assoc();

    if (!password_verify($token, $row["reset_token"])) {
        die("Token inválido.");
    }

    if (strtotime($row['reset_expira']) < time()) {
        die("El enlace ha expirado.");
    }

    $newHash = password_hash($pass1, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        UPDATE usuarios
        SET password_hash = ?, reset_token = NULL, reset_expira = NULL
        WHERE usuario_id = ?
    ");
    $stmt->bind_param("si", $newHash, $usuario_id);
    $stmt->execute();

    echo "Contraseña cambiada correctamente. Ya puedes iniciar sesión.";
}
?>
