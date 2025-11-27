<?php
require_once 'conexion.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fecha actual
$hoy = date('Y-m-d');

// Consulta del producto
$sql = "SELECT p.*, c.nombre AS categoria
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
        WHERE p.producto_id = $id";
$result = mysqli_query($conn, $sql);
$producto = mysqli_fetch_assoc($result);

// Consulta oferta válida según tu tabla real
$ofertaSql = "SELECT *
              FROM ofertas
              WHERE producto_id = $id
                AND activo = 1
                AND fecha_inicio <= '$hoy'
                AND fecha_fin >= '$hoy'
              LIMIT 1";
$ofertaRes = mysqli_query($conn, $ofertaSql);
$oferta = mysqli_fetch_assoc($ofertaRes);
?>
<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($producto['nombre'] ?? 'Producto no encontrado') ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="producto.css">
	<link rel="stylesheet" href="styles.css">
	  <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
  <link rel="manifest" href="favicon_io/site.webmanifest">

</head>
<body class="bg-light d-flex flex-column min-vh-100">

<!--Opciones de accesibilidad-->
<div class="no-leer" id="accesibilidad-bar">
  <button onclick="cambiarTamano(1)">A+</button>
  <button onclick="cambiarTamano(-1)">A-</button>
  <button onclick="toggleContraste()">Cont</button>
  <button onclick="leerPagina()">🔊</button>
</div>

<!--La cabezera global-->
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

  <span class="no-leer"style="margin:0 8px;">|</span>

  <a href="index.php">Inicio</a>
  <a href="catalogo.php">Catálogo</a>	
  <a href="contacto.php">Contacto</a>
  <a href="sobre.php">Sobre Nosotros</a>

</div>


  <main style="max-width:1100px; margin:30px auto; padding:20px;">

<?php if ($producto): ?>

  <div style="
      display:flex;
      gap:30px;
      background:white;
      padding:25px;
      border-radius:15px;
      box-shadow:0 4px 15px rgba(0,0,0,0.1);
      align-items:flex-start;
      flex-wrap:wrap;
  ">

    <div style="flex:1 1 350px; text-align:center;">
      <img src="img/<?= htmlspecialchars($producto['ImagenText'] ?: 'no-image.png') ?>" 
           alt=""
           style="width:100%; max-width:380px; border-radius:12px; object-fit:cover;">
    </div>

    <div style="flex:1 1 400px;">

      <h1 style="color:var(--orange-dark); margin-bottom:10px;">
        <?= htmlspecialchars($producto['nombre']) ?>
      </h1>

      <p style="font-size:18px; margin:5px 0;">
        <strong>Categoría:</strong> <?= htmlspecialchars($producto['categoria'] ?? 'Sin categoría') ?>
      </p>

      <p style="font-size:18px; margin:5px 0;">
        <strong>Código:</strong> <?= htmlspecialchars($producto['codigo']) ?>
      </p>

      <p style="font-size:18px; margin:5px 0;">
        <strong>Stock:</strong> <?= (int)$producto['stock'] ?>
      </p>

      <div style="margin-top:20px;">

        <?php if ($oferta): ?>

          <p style="font-size:30px; font-weight:bold; color:#d32f2f; margin:0;">
            $<?= number_format($oferta['precio_oferta'], 2) ?>
          </p>

          <p style="font-size:18px; color:#555; text-decoration:line-through; margin:0;">
            $<?= number_format($producto['precio_venta'], 2) ?>
          </p>

          <span style="
            display:inline-block;
            margin-top:8px;
            padding:5px 12px;
            background:#ff5722;
            color:white;
            font-weight:bold;
            border-radius:10px;
            font-size:14px;
          ">
            Producto en oferta
          </span>

        <?php else: ?>

          <!-- Precio normal -->
          <p style="font-size:30px; font-weight:bold; color:var(--orange-dark); margin:0;">
            $<?= number_format($producto['precio_venta'], 2) ?>
          </p>

        <?php endif; ?>

      </div>

      <?php if ($oferta && !empty($oferta['fecha_fin'])): ?>
        <p style="margin-top:15px; color:#b71c1c; font-weight:bold; font-size:16px;">
          Oferta válida hasta: <?= htmlspecialchars($oferta['fecha_fin']) ?>
        </p>
      <?php endif; ?>

      <a href="catalogo.php" 
         style="
           display:inline-block;
           margin-top:25px;
           padding:10px 20px;
           background:var(--orange-dark);
           color:white;
           border-radius:10px;
           text-decoration:none;
           font-weight:bold;
         ">
         ← Volver al catálogo
      </a>

    </div>
  </div>

<?php else: ?>

  <p style="text-align:center; margin:3rem; font-size:20px;">
    Producto no encontrado.
  </p>

<?php endif; ?>

</main>
<footer class="footer-olly text-white mt-5 no-leer" style="padding:40px 0;">
  <div class="container">

    <div class="row text-start">

      <div class="col-md-6 mb-4" >
        <h5 class="fw-bold mb-3">Contacto</h5>
        <p class="m-0">📌 Dirección: Calle Mexico-Tampico, col Cantores</p>
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

      <small>© 2025 Tienda Oly — Todos los derechos reservados.</small>
    </div>

  </div>
</footer>
</body>
<script src="accesibilidad.js"></script>
</html>

<?php
mysqli_close($conn);
?>
