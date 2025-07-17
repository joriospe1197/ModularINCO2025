<?php include_once __DIR__ . '/header-dashboard.php'; ?>

    <div class="barra">
        <a href="/register_product" class="register">Registrar producto</a>
        <a href="/search_product" class="search_user">Editar producto</a>
        <a href="/remove_product" class="remove_user">Eliminar producto</a>
    </div>

    <!-- Mostrar los productos -->
    <table class="tabla-empleados">
        <?php if (empty($productos)) : ?>
            <p>No hay productos registrados.</p>
        <?php else : ?>
                <thead>
                    <tr>
                        <th>ID Producto</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto) : ?>
                        <tr>
                            <td><?php echo $producto->idproducto; ?></td>
                            <td><?php echo $producto->descripcion; ?></td>
                            <td><?php echo $producto->precio; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
        <?php endif; ?>
    </table>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>
