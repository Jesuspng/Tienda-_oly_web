<?php
session_start();

if (isset($_SESSION["usuario_id"])) {
    switch ($_SESSION["rol"]) {
        case "Admin":
            header("Location: admin.php");
            break;
        case "Cajero":
            header("Location: caja.php");
            break;
        case "Cliente":
            header("Location: index.php");
            break;
        default:
            header("Location: index.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear Cuenta</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
  <link rel="manifest" href="favicon_io/site.webmanifest">


<style>
:root {
  --orange:#f57a3a;
  --orange-low:#ff983c;
  --orange-dark:#e8632a;
  --bg:#f3f6f8;
  --muted:#6b6b6b;
  font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
</style>

</head>

<body class="d-flex justify-content-center align-items-center vh-100" style="background: var(--bg);">

<div class="card shadow p-4" style="width: 420px !important;">
  
<div style="display:flex; justify-content:center;">
  <a href="index.php">
	<img src="logo.png" alt="Logo Tienda Olly" width="150">
  </a>
</div>
  <h2 class="text-center mb-4" style="color: var(--orange-dark);">
    Crear Cuenta
  </h2>

  <?php if(isset($_GET["err"])): ?>
    <div class="alert alert-danger py-2 text-center">
      <?= htmlspecialchars($_GET["err"]) ?>
    </div>
  <?php endif; ?>

  <?php if(isset($_GET["ok"])): ?>
    <div class="alert alert-success py-2 text-center">
      <?= htmlspecialchars($_GET["ok"]) ?>
    </div>
  <?php endif; ?>

<form action="crear_usuario_action.php" method="POST">

  <div class="mb-2">
    <label class="form-label">Alias (usuario)</label>
    <input type="text" name="alias" class="form-control"
           required minlength="4" maxlength="20"
           pattern="[A-Za-z0-9_-]{4,20}"
           title="El alias solo puede contener letras, números, guiones y guiones bajos (4-20 caracteres)">
  </div>

  <div class="mb-2">
    <label class="form-label">Contraseña</label>
    <input type="password" name="password" class="form-control"
           required minlength="6"
           title="La contraseña debe tener al menos 6 caracteres">
  </div>

  <hr>

  <div class="mb-2">
    <label class="form-label">Nombre</label>
    <input type="text" name="nombre" class="form-control"
           required
           pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]{2,40}"
           title="Solo se permiten letras y espacios (mínimo 2 caracteres)">
  </div>

  <div class="mb-2">
    <label class="form-label">Apellido</label>
    <input type="text" name="apellido" class="form-control"
           pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]{2,40}"
           title="Solo se permiten letras y espacios (mínimo 2 caracteres)">
  </div>

  <div class="mb-2">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control"
           required maxlength="80">
  </div>

  <div class="mb-2">
    <label class="form-label">Teléfono</label>
    <input type="text" name="telefono" class="form-control"
           pattern="[0-9]{8,15}"
           title="El teléfono debe contener solo números (8 a 15 dígitos)">
  </div>

  <div class="mb-2">
    <label class="form-label">Dirección</label>
    <input type="text" name="direccion" class="form-control"
           maxlength="120">
  </div>

  <?php
    $hoy = date("Y-m-d");
    $minEdad = date("Y-m-d", strtotime("-14 years"));
  ?>
  <div class="mb-3">
    <label class="form-label">Fecha de nacimiento</label>
    <input type="date" name="fecha_nacimiento" class="form-control"
           max="<?= $minEdad ?>"
           title="Debes tener al menos 14 años">
  </div>

  <button type="submit" class="btn w-100 text-white"
          style="background: var(--orange);">
    Crear Cuenta
  </button>

</form>

</div>

</body>
</html>
