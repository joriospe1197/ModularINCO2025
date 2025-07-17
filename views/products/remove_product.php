<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?> 

    <div class="barra">
        <a href="/productos" class="register">Regresar a la vista productos</a>
    </div>

    <!-- Mostrar alertas -->
    <?php foreach ($alertas as $tipo => $mensajes): ?>
        <?php foreach ($mensajes as $mensaje): ?>
            <div class="alerta <?php echo $tipo; ?>"><?php echo $mensaje; ?></div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <!-- Tabla con los productos -->
    <table class="tabla-empleados">
        <?php if (empty($productos)) : ?>
            <p>No hay productos registrados.</p>
        <?php else : ?>
            <thead>
                <tr>
                    <th>Id producto</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?php echo $producto->idproducto; ?></td>
                        <td><?php echo $producto->descripcion; ?></td>
                        <td><?php echo $producto->precio; ?></td>
                        <td>
                            <!-- Enlace para eliminar con confirmación -->
                            <a href="/remove_product?idproducto=<?php echo $producto->idproducto; ?>" class="boton_eliminar_empleado_tabla" 
                            onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        <?php endif; ?>    
    </table>

<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>
