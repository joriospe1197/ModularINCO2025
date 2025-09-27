<div class="contenedor-unidades">
    <!-- Alertas de operaciones exitosas/errores -->
    <?php if (!empty($_SESSION['alerta'])): ?>
        <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
            <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
        </div>
        <?php unset($_SESSION['alerta']); ?>
    <?php endif; ?>

    <div class="card-unidades">
        <div class="card-header-unidades d-flex justify-content-between align-items-center">
            <h2 class="card-title mb-0"><i class="fas fa-truck"></i> Gestión de Unidades</h2>
        </div>
        
        <div class="card-body-unidades">
            <!-- Acciones principales -->
            <div class="acciones-unidades">
                <a href="/asignar_unidades_a_choferes" class="register">
                    <i class="fas fa-link"></i> Asignar unidad a empleado
                </a>
                <a href="/search_unidad" class="search_user">
                    <i class="fas fa-edit"></i> Editar unidad
                </a>

                <?php if ($_SESSION['tipo_usuario'] == 1): ?>
                    <a href="/remove_unidad" class="remove_user">
                        <i class="fas fa-trash"></i> Eliminar unidad
                    </a>
                <?php endif; ?>
            </div>

            <!-- Tabla de unidades -->
            <?php if (empty($unidades)) : ?>
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-truck fa-3x mb-3"></i>
                    <h4>No hay unidades registradas</h4>
                    <p class="mb-0">Comienza asignando una unidad a un chofer</p>
                </div>
            <?php else : ?>
                <table class="tabla-unidades">
                    <thead>
                        <tr>
                            <th>Id Unidad</th>
                            <th>Modelo</th>
                            <th>Placas</th>
                            <th>Chofer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($unidades as $unidad): ?>
                            <tr>
                                <td><?php echo $unidad->idunidad; ?></td>
                                <td><?php echo $unidad->modelo; ?></td>
                                <td><?php echo $unidad->placas; ?></td>
                                <td><?php echo $unidad->chofer_nombre; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
            <?php endif; ?>
        </div>
    </div>
</div>