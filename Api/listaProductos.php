<?php
header('Content-Type: application/json');
include_once __DIR__ . '/coneccion.php';

$consulta = "SELECT
                productos.producto_id,
                productos.nombre,
                productos.codigo,
                productos.descripcion,
                productos.precio_venta,
                productos.precio_compra,
                productos.stock,
                categorias.nombre AS categoria_nombre,
                proveedores.nombre AS proveedor_nombre,
                productos.fecha_creacion,
                productos.activo,
                productos.ImagenText
            FROM
                productos
            LEFT JOIN categorias ON productos.categoria_id = categorias.categoria_id
            LEFT JOIN proveedores ON productos.proveedor_id = proveedores.proveedor_id;";

$result = $con->query($consulta);

$productos = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
    echo json_encode($productos, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'error' => 'No se encontraron productos'
    ]);
}

$con->close();
?>
