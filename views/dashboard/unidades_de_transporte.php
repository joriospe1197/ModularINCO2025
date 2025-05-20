<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?> 

    <div class="barra">
        <a href="/asignar_unidades_a_choferes" class="register">Asignar unidad a empleado</a>
        <a href="/search_unidad" class="search_user">Editar unidad</a>
        <a href="/remove_unidad" class="remove_user">Eliminar unidad</a>
    </div>

    <table class="tabla-empleados">
        <thead>
            <tr>
                <th>Id Unidad</th>
                <th>Modelo</th>
                <th>Placas</th>
                <th>Chofer</th> <!-- Agregamos la columna para el nombre del chofer -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($unidades as $unidad): ?>
                <tr>
                    <td><?php echo $unidad->idunidad; ?></td>
                    <td><?php echo $unidad->modelo; ?></td>
                    <td><?php echo $unidad->placas; ?></td>
                    <td><?php echo $unidad->chofer_nombre; ?></td> <!-- Mostramos el nombre del chofer -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>




<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>
