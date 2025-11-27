<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: login.php?recuperacion=ok");
    exit;
}

try {

    $stmt = $conn->prepare("SELECT usuario_id FROM clientes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {

        header("Location: login.php?recuperacion=ok");
        exit;
    }

    $cliente = $res->fetch_assoc();
    $usuario_id = (int)$cliente['usuario_id'];

    $token = bin2hex(random_bytes(32)); 
    $tokenHash = password_hash($token, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        UPDATE usuarios
        SET reset_token = ?, reset_expira = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
        WHERE usuario_id = ?
    ");
    $stmt->bind_param("si", $tokenHash, $usuario_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        header("Location: login.php?recuperacion=ok");
        exit;
    }

		$url = "https://equipo6.grupoahost.com/reset.php?e=" . urlencode($email) . "&t=" . urlencode($token);


    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'soporte@equipo6.grupoahost.com';
        $mail->Password   = 'V3leZ#P@rm4!M4rlG0ld%';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('soporte@equipo6.grupoahost.com', 'Soporte Equipo 6');
        $mail->addAddress($email);

        $mail->isHTML(true);
		$mail->CharSet = 'UTF-8';
        $mail->Subject = 'Recuperación de contraseña';
        $mail->Body = "
            <p>Hola, has solicitado recuperar tu contraseña.</p>
            <p>Haz clic aquí para continuar:</p>
            <p><a href='$url'>$url</a></p>
            <br>
            <p>Si no solicitaste esto, ignora este mensaje.</p>
        ";

        $mail->send();

        header("Location: login.php?recuperacion=ok");
        exit;

    } catch (Exception $e) {
        $stmt = $conn->prepare("UPDATE usuarios SET reset_token = NULL, reset_expira = NULL WHERE usuario_id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();

        header("Location: login.php?recuperacion=error");
        exit;
    }

} catch (Throwable $e) {

    header("Location: login.php?recuperacion=error");
    exit;
}
