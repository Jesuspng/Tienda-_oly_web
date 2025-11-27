<?php
session_start();

if (isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recuperar contraseña</title>

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

body {
  background: var(--bg);
}
.btn-orange {
  background: var(--orange);
  color: #fff;
  font-weight: 600;
}
.btn-orange:hover {
  background: var(--orange-dark);
  color: #fff;
}
</style>
</head>

<body>

  <main class="container d-flex justify-content-center" style="margin-top: 3rem;">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
      
      <h1 class="text-center mb-4" style="color: var(--orange-dark); font-size: 1.6rem;">
        Recuperar Contraseña
      </h1>

      <form action="send_recovery.php" method="post">

        <div class="mb-3">
          <label class="form-label">Email asociado a tu cuenta:</label>
          <input type="email" name="email" class="form-control" required placeholder="Ingresa tu correo">
        </div>

        <button type="submit" class="btn btn-orange w-100 mb-3">
          Enviar enlace de recuperación
        </button>

        <p class="text-center">
          <a href="login.php" class="text-decoration-none" style="color: var(--orange-dark);">
            Volver al inicio de sesión
          </a>
        </p>

      </form>
    </div>
  </main>

</body>
</html>
