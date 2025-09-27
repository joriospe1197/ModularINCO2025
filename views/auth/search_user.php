<a href="/empleados" class="btn-regresar">⬅ Regresar a la vista de empleados</a>

<?php include_once __DIR__ .'/../templates/alertas.php'; ?>
<!-- Mostrar info si se encontró usuario (solo para debug) -->
<?php if (isset($usuario) && $usuario): ?>
    <div class="alerta exito">
        <p>Empleado encontrado:  <?php echo $usuario->nombre; ?></p>
        <p>  Redirigiendo a edición...</p>
    </div>
    <script>
        // Redirección con JavaScript en caso de que el header no funcione
        setTimeout(function() {
            window.location.href = '/edit_user?idempleado=<?php echo $usuario->idempleado; ?>';
        }, 2000);
    </script>
<?php endif; ?>

<!-- Mostrar el formulario siempre -->
<form class="formulario" method="POST" action="/search_user">
    <div class="campo">
        <label for="idempleado">Id del empleado:</label>
        <input
            type="number"
            id="idempleado"
            placeholder="Ingrese el id del empleado que desea editar."
            name="idempleado"
            value="<?php echo $_POST['idempleado'] ?? ''; ?>"
            required
        />
    </div>
    <input type="submit" class="boton" value="Buscar empleado">
</form>

