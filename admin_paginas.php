<?php
require "conexion.php";

$result = $conn->query("SELECT id, slug, titulo, ultima_actualizacion FROM paginas_estaticas ORDER BY id ASC");
?>

<h2>Administrar páginas estáticas</h2>

<table border="1" cellpadding="10" style="border-collapse: collapse;">
    <tr>
        <th>ID</th>
        <th>Slug</th>
        <th>Título</th>
        <th>Última actualización</th>
        <th>Acción</th>
    </tr>

    <?php while($p = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['slug']) ?></td>
            <td><?= htmlspecialchars($p['titulo']) ?></td>
            <td><?= $p['ultima_actualizacion'] ?></td>
            <td><a href="editar_pagina.php?id=<?= $p['id'] ?>">Editar</a></td>
        </tr>
    <?php endwhile; ?>
</table>
