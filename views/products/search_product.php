<a href="/productos" class="btn-regresar">⬅ Regresar</a>

<?php include_once __DIR__ .'/../templates/alertas.php'; ?>

<!-- Mostrar el formulario solo si no se ha encontrado un usuario -->
<?php if (!isset($producto)): ?>
    <form class="formulario" method="POST" action="/search_product">
        <div class="campo">
            <label for="idempleado">Id del producto:</label>
            <input
                type="number"
                id="idproducto"
                placeholder="Ingrese el id del producto que desea editar."
                name="idproducto"
                value="<?php echo isset($producto) ? $producto->idproducto : ''; ?>"
            />
        </div>
        <input type="submit" class="boton" value="Buscar producto">
    </form>
<?php else: ?>
    <!-- Si se encontró al usuario, redirige a la página de edición -->
    <?php header('Location: /edit_product?idproducto=' . $producto->idproducto); exit; ?>
<?php endif; ?>


