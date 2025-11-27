<?php
include 'conexion.php';

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de usuario inválido.");
}

$id = intval($_GET['id']);

// Obtener los datos del usuario actual
$sql = "SELECT usuario_id, alias, rol, activo FROM usuarios WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}

$stmt->close();

// Procesar actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $alias = $_POST["alias"];
    $rol = $_POST["rol"];
    $activo = isset($_POST["activo"]) ? 1 : 0;
    $password = $_POST["password"];

    if (!empty($password)) {
        // Si se proporciona una nueva contraseña, se hashea y se actualiza
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE usuarios SET alias = ?, password_hash = ?, rol = ?, activo = ? WHERE usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $alias, $passwordHash, $rol, $activo, $id);
    } else {
        // Si no se proporciona, se omite la contraseña
        $sql = "UPDATE usuarios SET alias = ?, rol = ?, activo = ? WHERE usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $alias, $rol, $activo, $id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: admin.php?msg=" . urlencode("Usuario actualizado correctamente."));
        exit;
    } else {
        $error = "Error al actualizar el usuario: " . $stmt->error;
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="stylesAdmin.css">
</head>
<body>
<header>
    <div class="header-left">
        <img src="logo.png" alt="Logo">
        <h1>Panel de Administración</h1>
    </div>
    <nav>
        <a href="admin.php">Usuarios</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<main>
    <h2>Editar Usuario</h2>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <label>Alias:</label><br>
        <input type="text" name="alias" value="<?php echo htmlspecialchars($usuario['alias']); ?>" required><br><br>

        <label>Nueva contraseña (opcional):</label><br>
        <input type="password" name="password" placeholder="Dejar en blanco para no cambiar"><br><br>

        <label>Rol:</label><br>
        <select name="rol" required>
            <option value="admin" <?php if ($usuario['rol'] === 'admin') echo 'selected'; ?>>Admin</option>
            <option value="empleado" <?php if ($usuario['rol'] === 'empleado') echo 'selected'; ?>>Empleado</option>
        </select><br><br>

        <label>Activo:</label>
        <input type="checkbox" name="activo" <?php if ($usuario['activo']) echo 'checked'; ?>><br><br>

        <button type="submit">Actualizar</button>
        <a href="admin.php">Cancelar</a>
    </form>
</main>
</body>
</html>
