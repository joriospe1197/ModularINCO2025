<a href="/empleados" class="btn-regresar">⬅ Regresar a la vista de empleados</a>


<!-- Mostrar alertas -->
<?php foreach ($alertas as $tipo => $mensajes): ?>
    <?php foreach ($mensajes as $mensaje): ?>
        <div class="alerta <?php echo $tipo; ?>"><?php echo $mensaje; ?></div>
    <?php endforeach; ?>
<?php endforeach; ?>


<!-- Tabla con los empleados -->
<table class="tabla-unidades">
    <thead>
        <tr>
            <th>Id Empleado</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($empleados as $empleado): ?>
            <tr>
                <td><?php echo $empleado->idempleado; ?></td>
                <td><?php echo $empleado->nombre; ?></td>
                <td><?php echo $empleado->email; ?></td>
                <td><?php echo $empleado->telefono; ?></td>
                <td>
                    <!-- Enlace para eliminar con confirmación -->
                    <a href="/remove_user?idempleado=<?php echo $empleado->idempleado; ?>" class="boton_eliminar_empleado_tabla" 
                    onclick="return confirm('¿Estás seguro de que deseas eliminar este empleado?');">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

