<a href="/productos" class="btn-regresar">⬅ Regresar</a>

<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<h4>Detalles del producto:</h4>

<form class="formulario" method="POST" action="/edit_product?idproducto=<?php echo $producto->idproducto; ?>">

    <!-- Campo ID (Solo se mostrará como un campo de solo lectura) -->
    <input type="hidden" name="idproducto" value="<?php echo $producto->idproducto; ?>" />

    <!-- Campo Descripcion -->
    <div class="campo">
        <label for="descripcion">Descripción:</label>
        <input type="text" id="descripcion" name="descripcion" value="<?php echo $producto->descripcion; ?>" />
    </div>

    <!-- Campo Precio -->
    <div class="campo">
        <label for="precio">Precio:</label>
        <input 
            type="number" 
            id="precio" 
            name="precio" 
            step="0.01" 
            min="0" 
            value="<?php echo htmlspecialchars($producto->precio, ENT_QUOTES, 'UTF-8'); ?>" 
        />
    </div>

    <input type="submit" class="boton" value="Actualizar producto">
</form>

