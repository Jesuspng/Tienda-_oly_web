<?php
session_start();
$seccion = $_GET['seccion'] ?? 'inicio';
require "conexion.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Cliente') {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

$stmt = $conn->prepare("
    SELECT u.alias, u.rol, c.nombre, c.apellido, c.email, 
           c.telefono, c.direccion, c.fecha_nacimiento, c.fecha_registro
    FROM usuarios u
    INNER JOIN clientes c ON u.usuario_id = c.usuario_id
    WHERE u.usuario_id = ?
");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$datos = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Perfil</title>

<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="style_perfil.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
<link rel="manifest" href="favicon_io/site.webmanifest">

</head>
<body>

<!--Opciones de accesibilidad-->
<div class="no-leer" id="accesibilidad-bar">
  <button onclick="cambiarTamano(1)">A+</button>
  <button onclick="cambiarTamano(-1)">A-</button>
  <button onclick="toggleContraste()">Cont</button>
  <button onclick="leerPagina()">🔊</button>
</div>

<!--La cabecera global-->
<nav class="navbar navbar-expand-lg nav-olly px-3 no-leer" >
  <div class="d-flex align-items-center gap-3">
    <a href="index.php" class="navbar-brand m-0">
      <img src="logo.png" alt="Logo Tienda Olly" width="90">
    </a>
    <span class="nombre-tienda text-white fw-bold">Tienda Oly</span>
  </div>
  <form class="mx-auto d-none d-md-flex" 
      style="width: 100%; max-width: 1200px;" 
      method="GET" 
      action="buscar.php">
    <div class="buscador-wrapper">
        <input type="text" placeholder="Buscar productos..." 
               class="buscador-input"
               name="q"
               required>
        <button class="btn-buscar">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>

  <div class="user-box position-relative">
    <?php if(isset($_SESSION['alias'])): ?>
        <div class="user-trigger text-white fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
            Hola, <strong><?php echo htmlspecialchars($_SESSION['alias']); ?></strong>
            <i class="bi bi-caret-down-fill" style="font-size: .9rem;"></i>
        </div>

        <div class="user-menu">
            <a href="logout.php">Cerrar sesión</a>
            <a href="perfil.php">Mi perfil</a>  
        </div>
    <?php else: ?>
        <a href="login.php" class="text-white text-decoration-none fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
            <span>Iniciar sesión <br><strong>Cuenta</strong></span>
        </a>
    <?php endif; ?>
</div>

</nav>

<div class="subnav no-leer" style="max-width:100% !important; display:flex; flex-wrap:wrap; align-items:center; gap:10px;">

  <div class="dropdown">
    <button class="btn-categorias" id="btnCat">
      <i class="bi bi-list-ul"></i> Todas las categorías 
      <span id="caret"><i class="bi bi-caret-down"></i></span>
    </button>

    <div class="dropdown-menu no-leer" id="menuCat">
      <ul>
        <li><a href="catalogo.php?cat=1">Bebidas</a></li>
        <li><a href="catalogo.php?cat=2">Snacks</a></li>
        <li><a href="catalogo.php?cat=3">Lácteos</a></li>
        <li><a href="catalogo.php?cat=4">Panadería</a></li>
        <li><a href="catalogo.php?cat=5">Abarrotes</a></li>
        <li><a href="catalogo.php?cat=6">Limpieza</a></li>
        <li><a href="catalogo.php?cat=7">Carnes frías</a></li>
        <li><a href="catalogo.php?cat=8">Frutas y verduras</a></li>
        <li><a href="catalogo.php?cat=9">Congelados</a></li>
        <li><a href="catalogo.php?cat=10">Cuidado personal</a></li>
        <li><a href="catalogo.php?cat=11">Electrónica</a></li>
        <li><a href="catalogo.php?cat=12">Mascotas</a></li>
        <li><a href="catalogo.php?cat=13">Bebés</a></li>
        <li><a href="catalogo.php?cat=14">Alcohol</a></li>
        <li><a href="catalogo.php?cat=15">Ferretería</a></li>
      </ul>
    </div>
  </div>

  <script>
    const btn = document.getElementById("btnCat");
    const menu = document.getElementById("menuCat");

    btn.addEventListener("click", () => {
      menu.classList.toggle("show");
      btn.classList.toggle("menu-open");
    });
  </script>

  <span class="no-leer" style="margin:0 8px;">|</span>

  <a href="index.php">Inicio</a>
  <a href="catalogo.php">Catálogo</a> 
  <a href="contacto.php">Contacto</a>
  <a href="sobre.php">Sobre Nosotros</a>

</div>


<div class="nav-perfil">
<a href="?seccion=inicio">Inicio</a>
<a href="?seccion=info-personal">Información personal</a>
<a href="?seccion=cambiar-password">Cambiar contraseña</a>
<a href="?seccion=zona-peligrosa">Configuración avanzada</a>
</div>

<div class="perfil-container">

    <?php if(isset($_GET["msg"])): ?>
        <div class="alert">✔ <?= htmlspecialchars($_GET["msg"]) ?></div>
    <?php endif; ?>

    <?php if(isset($_GET["err"])): ?>
        <div class="error">⚠ <?= htmlspecialchars($_GET["err"]) ?></div>
    <?php endif; ?>

    <?php if ($seccion === 'inicio'): ?>
        <section>
            <h2 class="titulo">Bienvenido, <?= htmlspecialchars($datos['alias']) ?></h2>

            <p>Desde aquí puedes revisar tu información personal, cambiar tu contraseña o eliminar tu cuenta.</p>

            <p><strong>Registrado desde:</strong> <?= $datos['fecha_registro'] ?></p>

            <div class="d-flex flex-column align-items-center">
                <h2 class="titulo">Tus compras</h2>
                <i class="bi bi-cart-x" style="font-size: 8rem; margin-top:-2rem;"></i>
                <p style="color:var(--muted);">Parece que no has realizado ninguna compra recientemente</p>
            </div>
        </section>
    <?php endif; ?>


    <?php if ($seccion === 'info-personal'): ?>
        <section>
            <h2 class="titulo">Información Personal</h2>

            <form action="perfil_update.php" method="POST">

                <label>Alias</label>
                <input type="text" name="alias" value="<?= $datos['alias'] ?>">

                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= $datos['nombre'] ?>">

                <label>Apellido</label>
                <input type="text" name="apellido" value="<?= $datos['apellido'] ?>">

                <label>Email</label>
                <input type="email" name="email" value="<?= $datos['email'] ?>">

                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?= $datos['telefono'] ?>">

                <label>Dirección</label>
                <textarea name="direccion"><?= $datos['direccion'] ?></textarea>

                <label>Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="<?= $datos['fecha_nacimiento'] ?>">

                <button class="btn-perfil" type="submit" style="margin-top:1rem;">Guardar Cambios</button>

            </form>
        </section>
    <?php endif; ?>

    <?php if ($seccion === 'cambiar-password'): ?>
        <section>
            <h2 class="titulo">Cambiar Contraseña</h2>

            <form action="perfil_password.php" method="POST">

                <label>Contraseña Actual</label>
                <input type="password" name="actual" required>

                <label>Nueva Contraseña</label>
                <input type="password" name="nueva" required>

                <label>Confirmar Nueva Contraseña</label>
                <input type="password" name="confirmar" required>

                <button class="btn-perfil" type="submit" style="margin-top:1rem;">Cambiar Contraseña</button>

            </form>
        </section>
    <?php endif; ?>


    <?php if ($seccion === 'zona-peligrosa'): ?>
<section>

    <div style="display:flex; justify-content:center;">
        <h2 class="titulo" style="color:#c0392b;">Control de cuenta</h2>
    </div>

    <p style="max-width:600px; margin:10px auto; text-align:center; color:#7f8c8d;">
        Aquí puedes realizar acciones sensibles relacionadas con tu cuenta.
        Estas operaciones son irreversibles, procede con precaución.
    </p>

    <div style="
        max-width:600px;
        margin:20px auto;
        padding:15px;
        background:#fcebea;
        border:1px solid #e74c3c;
        border-radius:8px;
        color:#c0392b;
        text-align:center;">
        <strong>Advertencia:</strong> Si eliminas tu cuenta perderás tu perfil, historial y cualquier dato asociado.
        Esta acción no se puede deshacer.
    </div>

    <form action="perfil_delete.php" method="POST"
          onsubmit="return confirm('¿Seguro que quieres eliminar tu cuenta? Esta acción es irreversible.');"
          style="display:flex; justify-content:center; margin-top:20px;">
        <button class="btn" style="background:#c0392b; color:white; padding:10px 20px;">
            Eliminar mi cuenta
        </button>
    </form>

</section>
<?php endif; ?>

</div>



<footer class="footer-olly text-white mt-5 no-leer" style="padding:40px 0;">
  <div class="container">

    <div class="row text-start">

      <div class="col-md-6 mb-4" >
        <h5 class="fw-bold mb-3">Contacto</h5>
        <p class="m-0">📍 Dirección: Calle Mexico-Tampico, col Cantores</p>
        <p class="m-0">📞 Teléfono: 7713403691</p>
        <p class="m-0">✉ Correo: contacto@tiendaoly.com</p>
        <p class="mt-2">🕒 Horario: 8:00 AM – 6:00 PM</p>
      </div>

      <div class="col-md-6 mb-4" >
        <h5 class="fw-bold mb-3">Enlaces útiles</h5>
        <ul class="list-unstyled m-0">
          <li><a href="index.php" class="footer-link">Inicio</a></li>
          <li><a href="catalogo.php" class="footer-link">Catálogo</a></li>
          <li><a href="legal.php" class="footer-link">Legal</a></li>
          <li><a href="contacto.php" class="footer-link">Contacto</a></li>
          <li><a href="sobre.php" class="footer-link">Sobre nosotros</a></li>
        </ul>
      </div>

    </div>

    <hr class="border-secondary">

    <div class="text-center mt-3">
      <div class="d-flex justify-content-center gap-3 mb-3">

        <a href="https://www.instagram.com/leomessi/" target="_blank">
          <img src="insta.png" class="icono-redes" alt="Instagram" style="width:32px;">
        </a>

        <a href="https://www.facebook.com/leomessi" target="_blank">
          <img src="face.png" class="icono-redes" alt="Facebook" style="width:32px;">
        </a>

        <a href="https://github.com/Rendel05" target="_blank">
          <img src="git.png" class="icono-redes" alt="GitHub" style="width:32px;">
        </a>

      </div>

      <small>© 2025 Tienda Oly – Todos los derechos reservados.</small>
    </div>

  </div>
</footer>
</body>
<script src="accesibilidad.js"></script>
</html>
