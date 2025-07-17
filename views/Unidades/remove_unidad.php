<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?> 

    <div class="barra">
        <a href="/unidades_de_transporte" class="register">Regresar a la vista de unidades</a>
    </div>

    <table class="tabla-empleados">
        <thead>
            <tr>
                <th>Id Unidad</th>
                <th>Modelo</th>
                <th>Placas</th>
                <th>Chofer</th>
                <th>Acciones</th> <!-- Nueva columna -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($unidades as $unidad): ?>
                <tr>
                    <td><?php echo $unidad->idunidad; ?></td>
                    <td><?php echo $unidad->modelo; ?></td>
                    <td><?php echo $unidad->placas; ?></td>
                    <td><?php echo $unidad->chofer_nombre; ?></td>
                    <td>
                        <!-- Formulario de eliminación -->
                        <form method="POST" action="/remove_unidad" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta unidad?');">
                            <input type="hidden" name="idunidad" value="<?php echo $unidad->idunidad; ?>">
                            <button type="submit" class="boton_eliminar_empleado_tabla">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>
