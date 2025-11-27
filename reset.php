<?php
include "conexion.php";

$email = $_GET["e"] ?? null;
$token = $_GET["t"] ?? null;

if (!$email || !$token) {
    die("Datos incompletos.");
}

$stmt = $conn->prepare("
    SELECT usuario_id 
    FROM clientes 
    WHERE email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Correo no encontrado.");
}

$cliente = $result->fetch_assoc();
$usuario_id = $cliente["usuario_id"];

$stmt = $conn->prepare("
    SELECT reset_token, reset_expira 
    FROM usuarios 
    WHERE usuario_id = ?
");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Usuario no encontrado.");
}

$usuario = $result->fetch_assoc();

if (!password_verify($token, $usuario["reset_token"])) {
    die("Token inválido.");
}

if (strtotime($usuario["reset_expira"]) < time()) {
    die("El enlace ha expirado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer contraseña</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --orange:#f57a3a;
            --orange-low:#ff983c;
            --orange-dark:#e8632a;
            --bg:#f3f6f8;
            --muted:#6b6b6b;
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg);
            font-family: var(--font-family);
        }

        .btn-orange {
            background-color: var(--orange);
            border-color: var(--orange-dark);
            color: white;
        }

        .btn-orange:hover {
            background-color: var(--orange-dark);
            border-color: var(--orange-dark);
        }

        .titulo {
            color: var(--orange-dark);
        }

        .card {
            border: none;
            border-radius: 12px;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <h3 class="text-center mb-3 titulo">Restablecer contraseña</h3>
        <p class="text-muted text-center">Ingresa tu nueva contraseña</p>

        <form action="procesar_cambio.php" method="POST">
            <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="mb-3">
                <label class="form-label">Nueva contraseña</label>
                <input type="password" class="form-control" name="password" required minlength="6"
                       title="La contraseña debe tener al menos 6 caracteres">
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmar contraseña</label>
                <input type="password" class="form-control" name="password2" required minlength="6"
                       title="La contraseña debe tener al menos 6 caracteres">
            </div>

            <button class="btn btn-orange w-100" type="submit">Cambiar contraseña</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
