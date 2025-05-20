<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>

<div class="barra">
    <a href="/unidades_de_transporte" class="register">Regresar a la vista de unidades</a>
</div>

<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<!-- Mostrar el formulario solo si no se ha encontrado una unidad -->
<?php if (!isset($Unidad_de_transporte)): ?>
    <form class="formulario" method="POST" action="/search_unidad">
        <div class="campo">
            <label for="idunidad">ID de la unidad:</label>
            <input
                type="number"
                id="idunidad"
                placeholder="Ingrese el ID de la unidad que desea buscar."
                name="idunidad"
                value="<?php echo isset($Unidad_de_transporte) ? $Unidad_de_transporte->idunidad : ''; ?>"
            />
        </div>
        <input type="submit" class="boton" value="Buscar unidad">
    </form>
<?php else: ?>
    <!-- Si se encontró la unidad, redirige a la página de edición -->
    <?php header('Location: /edit_unidad?idunidad=' . $Unidad_de_transporte->idunidad); exit; ?>
<?php endif; ?>

<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>
