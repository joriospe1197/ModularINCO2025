<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>

<div class="barra">
    <a href="/search_user" class="search_user">Buscar otro empleado</a>
</div>

<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<h4>Detalles del Usuario:</h4>

<form class="formulario" method="POST" action="/edit_user?idempleado=<?php echo $usuario->idempleado; ?>">

    <!-- Campo ID (Solo se mostrará como un campo de solo lectura) -->
    <input type="hidden" name="idempleado" value="<?php echo $usuario->idempleado; ?>" />

    <!-- Campo Nombre -->
    <div class="campo">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo $usuario->nombre; ?>" />
    </div>

    <!-- Campo Dirección -->
    <div class="campo">
        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion" value="<?php echo $usuario->direccion; ?>" />
    </div>

    <!-- Campo Teléfono -->
    <div class="campo">
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo $usuario->telefono; ?>" />
    </div>

    <!-- Campo Email 
    <div class="campo">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $usuario->email; ?>" />
    </div> -->

    <!-- Campo Confirmado 
    <div class="campo">
        <label for="confirmado">Confirmado</label>
        <select id="confirmado" name="confirmado">
            <option value="1" <?php echo $usuario->confirmado ? 'selected' : ''; ?>>Sí</option>
            <option value="0" <?php echo !$usuario->confirmado ? 'selected' : ''; ?>>No</option>
        </select>
    </div> -->

    <input type="submit" class="boton" value="Actualizar Usuario">
</form>


<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>
