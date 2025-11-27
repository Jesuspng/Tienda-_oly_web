<?php
include 'conexion.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alias = $_POST["alias"];
    $passwordRaw = $_POST["password"];
    $rol = $_POST["rol"];
    $activo = isset($_POST["activo"]) ? 1 : 0;

    // Hashear la contraseña con bcrypt
    $passwordHash = password_hash($passwordRaw, PASSWORD_BCRYPT);

    // Insertar el nuevo usuario
    $sql = "INSERT INTO usuarios (alias, password_hash, rol, activo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $msg = "Error en prepare(): " . $conn->error;
    } else {
        $stmt->bind_param("sssi", $alias, $passwordHash, $rol, $activo);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: admin.php?msg=" . urlencode("Usuario agregado correctamente."));
            exit;
        } else {
            $msg = "Error al ejecutar consulta: " . $stmt->error;
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link rel="stylesheet" href="stylesAdmin.css">
</head>
<body>
    <!-- ===== CABECERA ===== -->
    <header>
        <div class="header-left">
            <img src="logo.png" alt="Logo">
            <h1>Panel de Administración</h1>
        </div>
        <nav>
            <a href="admin.php">Usuarios</a>
            <button class="logout" onclick="location.href='logout.php'">Cerrar sesión</button>
        </nav>
    </header>

    <!-- ===== CONTENIDO PRINCIPAL ===== -->
    <main>
        <h2>Agregar Usuario</h2>

        <?php if (!empty($msg)): ?>
            <p style="color:red; font-weight:bold;"><?php echo htmlspecialchars($msg); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="alias">Alias:</label>
            <input type="text" id="alias" name="alias" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <option value="admin">Administrador</option>
                <option value="empleado">Empleado</option>
            </select>

            <label for="activo" style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" id="activo" name="activo" checked> Activo
            </label>

            <div style="display: flex; gap: 10px; margin-top: 1rem;">
                <button type="submit" class="btn btn-agregar">Guardar Usuario</button>
                <a href="admin.php" class="btn btn-eliminar">Cancelar</a>
            </div>
        </form>
    </main>
</body>
</html>
