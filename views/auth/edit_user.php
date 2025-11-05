<?php
// Verificar si el usuario existe antes de mostrar el formulario
if (!$usuario) : ?>
    <div class="alerta error">
        <p>Usuario no encontrado</p>
    </div>
    <a href="/search_user" class="btn-regresar">⬅ Regresar a búsqueda</a>
<?php else : ?>
    <a href="/empleados" class="btn-regresar">⬅ Regresar a la vista de empleados</a>
    <a href="/search_user" class="search_user">Buscar otro empleado</a>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <h4>Detalles del Usuario:</h4>

    <form class="formulario" method="POST" action="/edit_user?idempleado=<?php echo $usuario->idempleado; ?>">
        <input type="hidden" name="idempleado" value="<?php echo $usuario->idempleado; ?>" />

        <div class="campo">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario->nombre); ?>" />
        </div>

        <div class="campo">
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario->direccion); ?>" />
        </div>

        <div class="campo">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario->telefono); ?>" />
        </div>

        <div class="campo">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario->email); ?>" />
        </div>

        <div class="campo">
            <label for="confirmado">Confirmado</label>
            <select id="confirmado" name="confirmado">
                <option value="1" <?php echo $usuario->confirmado ? 'selected' : ''; ?>>Sí</option>
                <option value="0" <?php echo !$usuario->confirmado ? 'selected' : ''; ?>>No</option>
            </select>
        </div>

        <input type="submit" class="boton" value="Actualizar Usuario">
    </form>
<?php endif; ?>