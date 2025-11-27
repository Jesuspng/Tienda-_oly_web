<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sobre Nosotros - Tienda Oly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


  <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles_map.css">
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


    
    <div class="oly-map-container">
    <div class="oly-map-header">
        <h1>Mapa de Navegación</h1>
        <br><p>Explora todas las secciones de Tienda Oly</p></br>
    </div>

    <div class="oly-map-section">
        <h2 class="oly-section-public" style="margin-top:-2rem;">Páginas Públicas</h2>

        <div class="oly-map-grid">

            <a href="index.php" class="oly-map-card">
                <div class="oly-map-icon oly-icon-orange">
                    <span class="oly-icon-simple">🏠</span>
                </div>
                <h3>Inicio</h3>
                <p>Página de bienvenida</p>
            </a>

            <a href="catalogo.php" class="oly-map-card">
                <div class="oly-map-icon oly-icon-orange">
                    <span class="oly-icon-simple">🛒</span>
                </div>
                <h3>Catálogo</h3>
                <p>Productos disponibles</p>
            </a>

            <a href="sobre.php" class="oly-map-card">
                <div class="oly-map-icon oly-icon-orange">
                    <span class="oly-icon-simple">ℹ️</span>
                </div>
                <h3>Sobre Nosotros</h3>
                <p>Información de la tienda</p>
            </a>

            <a href="contacto.php" class="oly-map-card">
                <div class="oly-map-icon oly-icon-orange">
                    <span class="oly-icon-simple">📩</span>
                </div>
                <h3>Contacto</h3>
                <p>Medios para comunicarte</p>
            </a>

            <a href="legal.php" class="oly-map-card">
                <div class="oly-map-icon oly-icon-orange">
                    <span class="oly-icon-simple">📘</span>
                </div>
                <h3>Aviso Legal</h3>
                <p>Información y normativas</p>
            </a>

            <a href="login.php" class="oly-map-card">
                <div class="oly-map-icon oly-icon-orange">
                    <span class="oly-icon-simple">🔑</span>
                </div>
                <h3>Login</h3>
                <p>Inicio de sesión</p>
            </a>

        </div>
    </div>
</div>

  <footer class="footer-olly text-white mt-5 no-leer" style="padding:40px 0;">
  <div class="container">

    <div class="row text-start">

      <div class="col-md-6 mb-4" >
        <h5 class="fw-bold mb-3">Contacto</h5>
        <p class="m-0">📌 Dirección:Calle Mexico-Tampico, col Cantores</p>
        <p class="m-0">📞 Teléfono: 7713403691</p>
        <p class="m-0">✉ Correo: contacto@tiendaoly.com</p>
        <p class="mt-2">🕒 Horario: 8:00 AM – 6:00 PM</p>
      </div>

      <div class="col-md-6 mb-4" >
        <h5 class="fw-bold mb-3">Enlaces útiles</h5>
        <ul class="list-unstyled m-0">
          <li><a href="index.php" class="footer-link">Inicio</a></li>
          <li><a href="productos.php" class="footer-link">Catálogo</a></li>
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
