<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>
    
    <div class="barra">
        <a href="/productos" class="register">Regresar a la vista productos</a>
    </div>

    <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

    <form class="formulario" method="POST" action="/register_product">
        <div class="campo">
            <label for="descripcion">Descripcion</label>
            <input type="text" id="descripcion" placeholder="Ingrese la descripción del producto." name="descripcion" value="<?php echo htmlspecialchars($productos->descripcion ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>

        <div class="campo">
            <label for="precio">Precio</label>
            <input 
                type="number" 
                id="precio" 
                step="0.01" 
                min="0" 
                placeholder="Ingrese el precio del producto." 
                name="precio" 
                value="<?php echo htmlspecialchars($productos->precio ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
            />
        </div>


        <input type="submit" class="boton" value="Guardar registro">
</form>

<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>
