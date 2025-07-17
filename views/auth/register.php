<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>
    
<div class="barra">
    <a href="/empleados" class="register">Regresar a la vista empleados</a>
</div>

    <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/register">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" placeholder="Ingrese el nombre completo del empleado" name="nombre" value="<?php echo $usuario->nombre; ?>" />
            </div>
            <div class="campo">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" placeholder="Ingrese el correo electrónico del empleado" name="email" value="<?php echo $usuario->email; ?>" />
            </div>
            <div class="campo">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" placeholder="Ingrese la dirección del empleado" name="direccion" value="<?php echo $usuario->direccion; ?>" />
            </div>
            <div class="campo">
                <label for="telefono">Teléfono</label>
                <input type="number" id="telefono" placeholder="Ingrese el teléfono del empleado" name="telefono" value="<?php echo $usuario->telefono; ?>" />
            </div>
            <div class="campo">
                <label for="puesto">Puesto</label>
                <select id="tipo_puesto" name="tipo_puesto">
                    <option value="" <?php echo empty($usuario->tipo_puesto) ? 'selected' : ''; ?>>Sin seleccionar</option>
                    <option value="Chofer" <?php echo $usuario->tipo_puesto === 'Chofer' ? 'selected' : ''; ?>>Chofer</option>
                    <option value="RH" <?php echo $usuario->tipo_puesto === 'RH' ? 'selected' : ''; ?>>RH</option>
                    <option value="CEO" <?php echo $usuario->tipo_puesto === 'CEO' ? 'selected' : ''; ?>>CEO</option>
                    <option value="DEV" <?php echo $usuario->tipo_puesto === 'DEV' ? 'selected' : ''; ?>>DEV</option>
                </select>
            </div>

            <div class="campo">
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" placeholder="Ingrese la contraseña" name="contrasena" />
            </div>
            <div class="campo">
                <label for="contrasena2">Repetir contraseña</label>
                <input type="password" id="contrasena2" placeholder="Repite la contraseña" name="contrasena2" />
            </div>

            <input type="submit" class="boton" value="Guardar registro">
        </form>

<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>
