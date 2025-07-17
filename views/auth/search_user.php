<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>

<div class="barra">
    <a href="/register" class="register">Agregar empleado</a>
    <a href="/remove_user" class="remove_user">Eliminar empleado</a>
</div>


        <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

        <!-- Mostrar el formulario solo si no se ha encontrado un usuario -->
        <?php if (!isset($usuario)): ?>
            <form class="formulario" method="POST" action="/search_user">
                <div class="campo">
                    <label for="idempleado">Id del empleado:</label>
                    <input
                        type="number"
                        id="idempleado"
                        placeholder="Ingrese el id del empleado que desea editar."
                        name="idempleado"
                        value="<?php echo isset($usuario) ? $usuario->idempleado : ''; ?>"
                    />
                </div>
                <input type="submit" class="boton" value="Buscar empleado">
            </form>
        <?php else: ?>
            <!-- Si se encontró al usuario, redirige a la página de edición -->
            <?php header('Location: /edit_user?idempleado=' . $usuario->idempleado); exit; ?>
        <?php endif; ?>

  

<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>
