<!-- Alertas de operaciones exitosas/errores -->
<?php if (!empty($_SESSION['alerta'])): ?>
    <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
        <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
    </div>
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>


<div class="contenedor-servicios">
    <!-- Sección de Unidades (botones) -->
    <div class="acciones-servicios mb-4">
        <a href="/servicios_de_unidades/crear" class="register">
            <i class="fas fa-plus"></i> Registrar Servicio
        </a>
        <a href="/servicios_de_unidades/historial_de_servicios" class="search_user">
            <i class="fas fa-history"></i> Ver Historial General
        </a>
    </div>
    <div class="seccion-unidades-servicios mb-5">
        <!--tabla de unidades -->
        <div class="unidades-list">
            <h3><i class="fas fa-truck"></i> Unidades Registradas</h3>
            <?php if (empty($unidades)): ?>
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-truck fa-3x mb-3"></i>
                    <h4>No hay unidades registradas</h4>
                </div>
            <?php else: ?>
                <table class="tabla-unidades">
                    <thead>
                        <tr>
                            <th>ID Unidad</th>
                            <th>Modelo</th>
                            <th>Placas</th>
                            <th>Chofer</th>
                            <th>Acciones</th>
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
                                    <a href="/servicios_de_unidades/historial_de_servicios?id=<?php echo $unidad->idunidad; ?>" 
                                    class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-history"></i> <span class="fs-4">Historial</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>


    <!-- ===== CONTENEDOR PRINCIPAL PARA AMBAS ALERTAS ===== -->
    <div class="contenedor-alertas-servicios">                        
        <!-- Alertas de servicios vencidos -->
        <?php if (!empty($serviciosPendientes)): ?>
            <div class="contenedor_alertas_servicios_vencidos mb-4">
                <div class="card-alerta card-alerta-vencido">
                    <div class="card-alerta-header card-alerta-header-vencido">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Servicios que Requieren Atención Inmediata</h4>
                        <span class="badge-cantidad"><?php echo count($serviciosPendientes); ?></span>
                    </div>
                    <div class="card-alerta-body">
                        <?php foreach ($serviciosPendientes as $servicio): ?>
                            <div class="item-servicio-alerta item-servicio-vencido">
                                <div class="info-servicio-alerta">
                                    <div class="servicio-alerta-header">
                                        <span class="badge-estado-servicio badge-estado-<?php echo $servicio->estado; ?>">
                                            <?php echo ucfirst($servicio->estado); ?>
                                        </span>
                                        <span class="servicio-fecha-vencimiento">
                                            <i class="fas fa-calendar-times"></i>
                                            Vencido: <?php echo date('d/m/Y', strtotime($servicio->siguiente_servicio)); ?>
                                        </span>
                                    </div>
                                    <div class="servicio-alerta-details">
                                        <div class="servicio-vehiculo">
                                            <i class="fas fa-truck"></i>
                                            <strong><?php echo htmlspecialchars($servicio->modelo); ?></strong>
                                            <span class="servicio-placas">(<?php echo htmlspecialchars($servicio->placas); ?>)</span>
                                        </div>
                                        <div class="servicio-tipo">
                                            <i class="fas fa-tools"></i>
                                            <?php echo htmlspecialchars($servicio->nombre_servicio); ?>
                                        </div>
                                        <div class="servicio-chofer">
                                            <i class="fas fa-user"></i>
                                            Chofer: <?php echo htmlspecialchars($servicio->chofer_nombre); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="acciones-servicio-alerta">
                                    <a href="/servicios_de_unidades/historial_de_servicios?id=<?php echo $servicio->idunidad; ?>" 
                                    class="btn btn-gestionar-servicio">
                                        <i class="fas fa-edit"></i> Gestionar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>


        <!-- Próximos servicios -->
        <?php if (!empty($proximosServicios)): ?>
            <div class="contenedor_alertas_servicios_proximos mb-4">
                <div class="card-alerta card-alerta-proximo">
                    <div class="card-alerta-header card-alerta-header-proximo">
                        <i class="fas fa-clock"></i>
                        <h4>Próximos Servicios Programados</h4>
                        <span class="badge-cantidad"><?php echo count($proximosServicios); ?></span>
                    </div>
                    <div class="card-alerta-body">
                        <?php foreach ($proximosServicios as $servicio): ?>
                            <div class="item-servicio-alerta item-servicio-proximo">
                                <div class="info-servicio-alerta">
                                    <div class="servicio-alerta-header">
                                        <span class="badge-tiempo-restante badge-tiempo-<?php echo $servicio->dias_restantes <= 7 ? 'urgente' : 'normal'; ?>">
                                            <i class="fas fa-hourglass-half"></i>
                                            <?php echo $servicio->dias_restantes; ?> días restantes
                                        </span>
                                        <span class="servicio-fecha-proximo">
                                            <i class="fas fa-calendar-check"></i>
                                            Próximo: <?php echo date('d/m/Y', strtotime($servicio->siguiente_servicio)); ?>
                                        </span>
                                    </div>
                                    <div class="servicio-alerta-details">
                                        <div class="servicio-vehiculo">
                                            <i class="fas fa-truck"></i>
                                            <strong><?php echo htmlspecialchars($servicio->modelo); ?></strong>
                                            <span class="servicio-placas">(<?php echo htmlspecialchars($servicio->placas); ?>)</span>
                                        </div>
                                        <div class="servicio-tipo">
                                            <i class="fas fa-tools"></i>
                                            <?php echo htmlspecialchars($servicio->nombre_servicio); ?>
                                        </div>
                                        <div class="servicio-chofer">
                                            <i class="fas fa-user"></i>
                                            Chofer: <?php echo htmlspecialchars($servicio->chofer_nombre); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="acciones-servicio-alerta">
                                    <a href="/servicios_de_unidades/historial_de_servicios?id=<?php echo $servicio->idunidad; ?>" 
                                    class="btn btn-ver-servicio">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>