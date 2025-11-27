<?php include 'conexion.php'; ?>
<?php
$hoy = date('Y-m-d');

$sql_ofertas = "SELECT 
                    p.producto_id,
                    p.nombre,
                    p.precio_venta,
                    o.precio_oferta,
                    p.ImagenText
                FROM productos p
                INNER JOIN ofertas o 
                    ON p.producto_id = o.producto_id
                WHERE o.fecha_inicio <= '$hoy'
                  AND o.fecha_fin >= '$hoy'";


$ofertas = $conn->query($sql_ofertas);
?>

<?php
$sql_producto_dia = "
    SELECT
        p.producto_id,
        p.nombre,
        p.precio_venta,
        p.ImagenText,
        o.precio_oferta
    FROM productos p
    LEFT JOIN ofertas o
        ON p.producto_id = o.producto_id
       AND o.fecha_inicio <= CURDATE()
       AND o.fecha_fin >= CURDATE()
       AND o.activo = 1
    WHERE p.activo = 1
    ORDER BY RAND()
    LIMIT 1
";

$res_producto_dia = $conn->query($sql_producto_dia);
if (!$res_producto_dia) {
    error_log("Error SQL producto del día: " . $conn->error);
    $productoDia = null;
} else {
    $productoDia = $res_producto_dia->fetch_assoc();
}
?>
<?php
$sql_nuevos = "
    SELECT producto_id, nombre, precio_venta, ImagenText
    FROM productos
    WHERE activo = 1
    ORDER BY fecha_creacion DESC
    LIMIT 4
";

$res_nuevos = $conn->query($sql_nuevos);
if (!$res_nuevos) {
    error_log("Error SQL productos nuevos: " . $conn->error);
    $res_nuevos = null;
}
?>
<?php if (isset($_GET["bienvenido"])): ?>
<div style="
    background:#d1ffd1;
    padding:15px;
    border:1px solid #7cc67c;
    width:80%;
    margin:20px auto;
    text-align:center;
    border-radius:6px;">
    🎉 ¡Bienvenido! Tu cuenta se creó con éxito.
</div>
<?php endif; ?>

<?php session_start(); ?>



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>POS - Inicio</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


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





<main style=" margin-left:5rem !important; margin-right:5rem !important;">
  <div class="position-relative" style="margin-top:10px">
    <p class="text-start" style="font-size:1.5rem;"><strong>Explora🗂️</strong></p>
  </div>
  
  
  
  
  
  
<div class="marquee-container">

  <div class="marquee" style="gap:20px;">

    <div class="marquee-group">

      <a href="catalogo.php?cat=1" class="card text-center">
        <img src="imgGenerales/cat_bebidas.png" class="card-img-top">
        <p>Bebidas</p>
      </a>

      <a href="catalogo.php?cat=2" class="card text-center">
        <img src="imgGenerales/cat_snacks.png" class="card-img-top">
        <p>Snacks</p>
      </a>

      <a href="catalogo.php?cat=3" class="card text-center">
        <img src="imgGenerales/cat_lacteos.png" class="card-img-top">
        <p>Lácteos</p>
      </a>

      <a href="catalogo.php?cat=4" class="card text-center">
        <img src="imgGenerales/cat_pan.png" class="card-img-top">
        <p>Panadería</p>
      </a>

      <a href="catalogo.php?cat=5" class="card text-center">
        <img src="imgGenerales/cat_abarrotes.png" class="card-img-top">
        <p>Abarrotes</p>
      </a>

      <a href="catalogo.php?cat=6" class="card text-center">
        <img src="imgGenerales/cat_limpieza.png" class="card-img-top">
        <p>Limpieza</p>
      </a>

      <a href="catalogo.php?cat=7" class="card text-center">
        <img src="imgGenerales/cat_carnes.png" class="card-img-top">
        <p>Carnes frías</p>
      </a>

      <a href="catalogo.php?cat=8" class="card text-center">
        <img src="imgGenerales/cat_frutas.png" class="card-img-top">
        <p>Frutas y verduras</p>
      </a>

      <a href="catalogo.php?cat=9" class="card text-center">
        <img src="imgGenerales/cat_congelados.png" class="card-img-top">
        <p>Congelados</p>
      </a>

      <a href="catalogo.php?cat=10" class="card text-center">
        <img src="imgGenerales/cat_personal.png" class="card-img-top">
        <p>Cuidado personal</p>
      </a>

      <a href="catalogo.php?cat=11" class="card text-center">
        <img src="imgGenerales/cat_electronica.png" class="card-img-top">
        <p>Electrónica</p>
      </a>

      <a href="catalogo.php?cat=12" class="card text-center">
        <img src="imgGenerales/cat_mascotas.png" class="card-img-top">
        <p>Mascotas</p>
      </a>

      <a href="catalogo.php?cat=13" class="card text-center">
        <img src="imgGenerales/cat_bebes.png" class="card-img-top">
        <p>Bebés</p>
      </a>

      <a href="catalogo.php?cat=14" class="card text-center">
        <img src="imgGenerales/cat_alcohol.png" class="card-img-top">
        <p>Alcohol</p>
      </a>

      <a href="catalogo.php?cat=15" class="card text-center">
        <img src="imgGenerales/cat_herramientas.png" class="card-img-top">
        <p>Ferretería</p>
      </a>

    </div>

    <div class="marquee-group no-leer">

      <a href="catalogo.php?cat=1" class="card text-center">
        <img src="imgGenerales/cat_bebidas.png" class="card-img-top">
        <p>Bebidas</p>
      </a>

      <a href="catalogo.php?cat=2" class="card text-center">
        <img src="imgGenerales/cat_snacks.png" class="card-img-top">
        <p>Snacks</p>
      </a>

      <a href="catalogo.php?cat=3" class="card text-center">
        <img src="imgGenerales/cat_lacteos.png" class="card-img-top">
        <p>Lácteos</p>
      </a>

      <a href="catalogo.php?cat=4" class="card text-center">
        <img src="imgGenerales/cat_pan.png" class="card-img-top">
        <p>Panadería</p>
      </a>

      <a href="catalogo.php?cat=5" class="card text-center">
        <img src="imgGenerales/cat_abarrotes.png" class="card-img-top">
        <p>Abarrotes</p>
      </a>

      <a href="catalogo.php?cat=6" class="card text-center">
        <img src="imgGenerales/cat_limpieza.png" class="card-img-top">
        <p>Limpieza</p>
      </a>

      <a href="catalogo.php?cat=7" class="card text-center">
        <img src="imgGenerales/cat_carnes.png" class="card-img-top">
        <p>Carnes frías</p>
      </a>

      <a href="catalogo.php?cat=8" class="card text-center">
        <img src="imgGenerales/cat_frutas.png" class="card-img-top">
        <p>Frutas y verduras</p>
      </a>

      <a href="catalogo.php?cat=9" class="card text-center">
        <img src="imgGenerales/cat_congelados.png" class="card-img-top">
        <p>Congelados</p>
      </a>

      <a href="catalogo.php?cat=10" class="card text-center">
        <img src="imgGenerales/cat_personal.png" class="card-img-top">
        <p>Cuidado personal</p>
      </a>

      <a href="catalogo.php?cat=11" class="card text-center">
        <img src="imgGenerales/cat_electronica.png" class="card-img-top">
        <p>Electrónica</p>
      </a>

      <a href="catalogo.php?cat=12" class="card text-center">
        <img src="imgGenerales/cat_mascotas.png" class="card-img-top">
        <p>Mascotas</p>
      </a>

      <a href="catalogo.php?cat=13" class="card text-center">
        <img src="imgGenerales/cat_bebes.png" class="card-img-top">
        <p>Bebés</p>
      </a>

      <a href="catalogo.php?cat=14" class="card text-center">
        <img src="imgGenerales/cat_alcohol.png" class="card-img-top">
        <p>Alcohol</p>
      </a>

      <a href="catalogo.php?cat=15" class="card text-center">
        <img src="imgGenerales/cat_herramientas.png" class="card-img-top">
        <p>Ferretería</p>
      </a>

    </div>

  </div>
</div>

	<div style="margin-left:89%;">
	<p style="font-weight:80; font-style:italic;">Íconos obtenidos en <a href="https://www.freepik.com/">Freepik</a></p>
	</div>
	
	
	
<section class="mt-5" style="justify-content:center;">
  <div style="margin-top:10px; display:flex; justify-content:center; align-content:center;">
    <p style="font-size:1.5rem;"><strong>🔥🔥¡Lo más popular!🔥🔥</strong></i></p>
  </div>

  <div class="d-flex flex-wrap justify-content-center gap-4">

    <div class="pop-card p-3 rounded" style="width: auto; background:#ff983c; box-shadow:0 4px 8px rgba(0,0,0,0.1); cursor:pointer; text-align:center;"
	onclick="location.href='producto.php?id=<?=1?>'">
      <img src="img/Coca.png" alt="Producto 1" class="img-fluid rounded" style="height:200px; object-fit:cover;">
      <h6 >Coca Cola 600ml</h6>
    </div>

    <div class="pop-card p-3 rounded" style="width: auto; background:#ff983c; box-shadow:0 4px 8px rgba(0,0,0,0.1); cursor:pointer; text-align:center;"
	onclick="location.href='producto.php?id=<?=3?>'">
  <img src="img/Sabritas.png" alt="Producto 2" class="img-fluid rounded" style="height:200px; object-fit:cover;">
  <h6>Papas Fritas 150g</h6>
</div>

<div class="pop-card p-3 rounded" style="width: auto; background:#ff983c; box-shadow:0 4px 8px rgba(0,0,0,0.1); cursor:pointer; text-align:center;"
onclick="location.href='producto.php?id=<?=15?>'">
  <img src="img/Heineken.png" alt="Producto 3" class="img-fluid rounded" style="height:200px; object-fit:cover;">
  <h6>Cerveza Heineken 355ml</h6>
</div>

<div class="pop-card p-3 rounded" style="width: auto; background:#ff983c; box-shadow:0 4px 8px rgba(0,0,0,0.1); cursor:pointer; text-align:center;"
onclick="location.href='producto.php?id=<?=2?>'">
  <img src="img/Pepsi.png" alt="Producto 4" class="img-fluid rounded" style="height:200px; object-fit:cover;">
  <h6>Pepsi 600ml</h6>
</div>
  </div>
</section>

<section class="mt-5" style="justify-content:center; margin-bottom:40px;">
  <div style="margin-top:10px; display:flex; justify-content:center;">
    <p style="font-size:1.5rem;"><strong>🔖🔖Productos en oferta🔖🔖</strong></p>
  </div>

  <div class="d-flex flex-wrap justify-content-center gap-4">
  <?php while ($row = $ofertas->fetch_assoc()): ?>
      <div class="pop-card p-3 rounded"
           style="width: auto; background:#ff983c; box-shadow:0 4px 8px rgba(0,0,0,0.1); cursor:pointer; text-align:center;"
           onclick="location.href='producto.php?id=<?php echo $row['producto_id']; ?>'">

          <img src="img/<?php echo $row['ImagenText']; ?>" 
               alt="<?php echo $row['nombre']; ?>" 
               class="img-fluid rounded" 
               style="height:200px; object-fit:cover;">

          <h6><?php echo $row['nombre']; ?></h6>

          <p style="text-decoration:line-through; color:#333;">
              $<?php echo $row['precio_venta']; ?>
          </p>

          <p style="font-size:1.3rem; font-weight:bold;">
              $<?php echo $row['precio_oferta']; ?>
          </p>
      </div>
  <?php endwhile; ?>
  </div>
</section>


	
<div class="container my-4">
  <div style="margin-top:10px; display:flex;  justify-content:center !important;" >
    <p style="font-size:1.5rem;"><strong>✨✨Producto del día✨✨</strong></p>
  </div>

  <?php if ($productoDia):
    $precio_oferta_dia = !empty($productoDia['precio_oferta']) ? $productoDia['precio_oferta'] : null;
    $precio_original_dia = $productoDia['precio_venta'];
    $imgPath = !empty($productoDia['ImagenText']) ? "img/".htmlspecialchars($productoDia['ImagenText']) : "img/placeholder.png";
  ?>

<div class="card shadow-lg mx-auto"
     style="width: 50% !important; height:50% !important; 
            border-radius:3rem; overflow:hidden; 
            display:flex; flex-direction:column; 
            align-items:center; justify-content:start;">

    
    <img src="<?php echo $imgPath; ?> "
         style="width:75%; height:65%; background:none; margin-top:2rem;"
         alt="<?php echo htmlspecialchars($productoDia['nombre']); ?>"
		 onclick="location.href='producto.php?id=<?php echo $productoDia['producto_id']; ?>'">


    <div class="p-4">
      <h3 class="text-center mb-3"><?php echo htmlspecialchars($productoDia['nombre']); ?></h3>

      <?php if ($precio_oferta_dia !== null): ?>
        <p class="text-center mb-3">
          <span class="fs-3 fw-bold text-danger">$<?php echo number_format($precio_oferta_dia, 2); ?></span>
          <small class="text-muted ms-2"><del>$<?php echo number_format($precio_original_dia, 2); ?></del></small>
        </p>
      <?php else: ?>
        <p class="text-center fs-3 fw-bold text-success mb-3">
          $<?php echo number_format($precio_original_dia, 2); ?>
        </p>
      <?php endif; ?>

      <div style="display:flex; align-items:center !important; justify-content: center;">
        <a href="producto.php?id=<?php echo $productoDia['producto_id']; ?>" class="btn btn-warning btn-lg w-70">
          Ver producto
        </a>
      </div>
    </div>
  </div>

  <?php else: ?>
    <div class="alert alert-secondary text-center">No hay producto del día disponible.</div>
  <?php endif; ?>
</div>



<div class="container my-5">
  <div style="margin-top:10px; display:flex; justify-content:center;">
    <p style="font-size:1.5rem;"><strong>🎉🎉Nuevos productos🎉🎉</strong></p>
  </div>
  
  <div class="row">
    <?php if ($res_nuevos && $res_nuevos->num_rows > 0): ?>
      <?php while ($p = $res_nuevos->fetch_assoc()): 
            $img = !empty($p['ImagenText']) ? "img/".htmlspecialchars($p['ImagenText']) : "img/placeholder.png";
      ?>
        <div class="col-6 col-md-3 mb-4" style="display:flex; justify-content:center; text-align:center;">
          <div class="card h-100 shadow-sm" style="width:10rem !important; height:15rem !important;"
		  onclick="location.href='producto.php?id=<?php echo $p['producto_id']; ?>'">
            <img src="<?php echo $img; ?>" 
                 class="card-img-top" 
                 style="height:160px; object-fit:cover;" 
                 alt="<?php echo htmlspecialchars($p['nombre']); ?>">

            <div class="card-body p-2 d-flex flex-column">
              <h6 class="card-title text-truncate mb-1"><?php echo htmlspecialchars($p['nombre']); ?></h6>
              <a href="producto.php?id=<?php echo $p['producto_id']; ?>" class="btn btn-sm btn-outline-primary mt-auto w-100">
                Ver más
              </a>
            </div>	
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-secondary text-center">No hay productos nuevos para mostrar.</div>
      </div>
    <?php endif; ?>
  </div>
</div>



	
	
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
			<li><a href="mapa_sitio.php" class="footer-link">Mapa del sitio</a></li>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
<script src="accesibilidad.js"></script>
</html>
