<?php
require_once "conexion.php"; 

$q = $_GET['q'] ?? '';
$q = trim($q);

if ($q === '') {
    header("Location: index.php");
    exit;
}

$sql = "SELECT producto_id, nombre, codigo, descripcion, precio_venta, stock, categoria_id, proveedor_id, fecha_creacion, activo, ImagenText
        FROM productos
        WHERE activo = 1
          AND (nombre LIKE ? OR descripcion LIKE ? OR codigo LIKE ?)
        ORDER BY nombre
        LIMIT 50";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . htmlspecialchars($conn->error));
}

$like = "%$q%";
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$resultados = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Resultados de búsqueda: <?= htmlspecialchars($q) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="styles.css">
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


    <h2 style="text-align:center; margin-top:18px;">
        Resultados para: <span style="color:#3498db;"><?= htmlspecialchars($q) ?></span>
    </h2>

    <?php if ($resultados->num_rows === 0): ?>
    <p class="text-center text-muted mt-4">No se encontró ningún producto con ese término.</p>
<?php else: ?>
    <div class="container my-4" >
        <div class="row g-3" >
            <?php while ($p = $resultados->fetch_assoc()): ?>
                <?php
                    $imagen = $p['ImagenText'] ?? '';
                    if (!empty($imagen)) {
                        if (strpos($imagen, 'data:') === 0) {
                            $imgSrc = $imagen;
                        } else {
                            $imgSrc = 'img/' . $imagen;
                        }
                    } else {
                        $imgSrc = 'placeholder.png';
                    }

                    $link = 'producto.php?id=' . urlencode($p['producto_id']);
                ?>

                <div class="col-12 col-sm-6 col-md-4 col-lg-3" >
                    <a href="<?= htmlspecialchars($link) ?>" class="text-decoration-none text-dark">
                        <div class="card h-100 shadow-sm" style="width:10rem !important;">
                            <img src="<?= htmlspecialchars($imgSrc) ?>" 
                                 class="card-img-top" 
                                 style="object-fit: cover; height: 180px;"
                                 alt="<?= htmlspecialchars($p['nombre']) ?>">

                            <div class="card-body">
                                <h5 class="card-title" style="font-size: 1rem;">
                                    <?= htmlspecialchars($p['nombre']) ?>
                                </h5>

                                <p class="card-text text-muted" style="font-size: 0.9rem;">
                                    <?= htmlspecialchars(mb_strimwidth($p['descripcion'], 0, 80, '...')) ?>
                                </p>

                                <p class="fw-bold mb-1">
                                    $<?= number_format($p['precio_venta'], 2, '.', ',') ?>
                                </p>

                                <?php if ($p['stock'] <= 0): ?>
                                    <span class="badge bg-danger">Agotado</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Stock: <?= intval($p['stock']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>

            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>

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
