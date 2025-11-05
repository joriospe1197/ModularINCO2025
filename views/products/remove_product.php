<a href="/productos" class="btn-regresar">⬅ Regresar</a>

<!-- Mostrar alertas -->
<?php if (!empty($_SESSION['alerta'])): ?>
    <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
        <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
    </div>
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>

    <!-- Tabla con los productos -->
    <table class="tabla-productos">
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
