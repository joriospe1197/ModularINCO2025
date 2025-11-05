<!-- Alertas de operaciones exitosas/errores -->
<?php if (!empty($_SESSION['alerta'])): ?>
    <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
        <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
    </div>
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>


<a href="/register" class="register">Agregar empleado</a>
<a href="/search_user" class="search_user">Editar empleado</a>
<a href="/remove_user" class="remove_user">Eliminar empleado</a>

<!-- Mostrar los empleados -->
<table class="tabla-unidades">

    <?php if (empty($empleados)) : ?>
        <p>No hay empleados registrados.</p>
    <?php else : ?>
            <thead>
                <tr>
                    <th>ID Empleado</th>
                    <th>Nombre</th>
                    <th>Direccion</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empleados as $empleado) : ?>
                    <tr>
                        <td><?php echo $empleado->idempleado; ?></td>
                        <td><?php echo $empleado->nombre; ?></td>
                        <td><?php echo $empleado->direccion; ?></td>
                        <td><?php echo $empleado->email; ?></td>
                        <td><?php echo $empleado->telefono; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
    <?php endif; ?>
</table>


