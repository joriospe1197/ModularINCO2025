<!-- Alertas de operaciones exitosas/errores -->
<?php if (!empty($_SESSION['alerta'])): ?>
    <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
        <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
    </div>
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>

<div class="contenedor-historial">
    <div class="card-historial">
        <div class="card-header-historial-servicios d-flex justify-content-between align-items-center">
            <h2 class="card-title mb-0">
                <i class="fas fa-history"></i> 
                <?php echo $unidad ? 'Historial de ' . $unidad->modelo : 'Historial de Servicios'; ?>
            </h2>
            <div>
                <a href="/servicios_de_unidades" class="btn btn-secondary-historial">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="/servicios_de_unidades/crear" class="btn btn-primary-historial">
                    <i class="fas fa-plus"></i> Nuevo Servicio
                </a>
            </div>
        </div>
        
        <div class="card-body-historial">
            <!-- Información de la unidad seleccionada -->
            <?php if ($unidad): ?>
                <div class="info-unidad-historial mb-4 p-3 bg-light rounded">
                    <h5>Información de la Unidad</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Modelo:</strong> <?php echo $unidad->modelo; ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Placas:</strong> <?php echo $unidad->placas; ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Chofer:</strong> <?php echo $unidad->chofer_nombre; ?>
                        </div>
                        <div class="col-md-3">
                            <strong>ID:</strong> <?php echo $unidad->idunidad; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Selector de unidad -->
            <div class="selector-unidad mb-4">
                <form method="GET" action="/servicios_de_unidades/historial_de_servicios" class="row g-3">
                    <div class="col-md-6">
                        <select class="form-control-selector-historial" name="id" onchange="this.form.submit()">
                            <option value="">Selecciona una unidad para ver su historial</option>
                            <?php foreach ($unidades as $unidad_option): ?>
                                <option value="<?php echo $unidad_option->idunidad; ?>" 
                                    <?php echo ($unidad && $unidad->idunidad == $unidad_option->idunidad) ? 'selected' : ''; ?>>
                                    <?php echo $unidad_option->modelo . ' - ' . $unidad_option->placas . ' (' . $unidad_option->chofer_nombre . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Lista de servicios -->
            <?php if ($unidad && empty($servicios)): ?>
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-tools fa-3x mb-3"></i>
                    <h4>No hay servicios registrados</h4>
                    <p class="mb-0">Esta unidad no tiene servicios en su historial</p>
                    <a href="/servicios_de_unidades/crear" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Registrar Primer Servicio
                    </a>
                </div>
            <?php elseif ($unidad && !empty($servicios)): ?>
                <div class="tabla-responsive-historial">
                    <table class="tabla-historial-servicios">
                        <thead>
                            <tr>
                                <th>Fecha Servicio</th>
                                <th>Tipo de Servicio</th>
                                <th>Descripción</th>
                                <th>Próximo Servicio</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicios as $servicio): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($servicio->fecha_servicio)); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($servicio->nombre_servicio); ?></strong><br>
                                        <small>Cada <?php echo $servicio->intervalo_meses; ?> meses</small>
                                    </td>
                                    <td><?php echo htmlspecialchars($servicio->descripcion_servicio ?: '---'); ?></td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($servicio->siguiente_servicio)); ?>
                                    </td>
                                    <td>
                                        <span class="badge_servicio estado-<?= $servicio->estado ?>_servicio" 
                                            data-servicio-id="<?= $servicio->id_servicio ?>"
                                            style="cursor: pointer; padding: 0.6em 1em;">
                                            <?= ucfirst($servicio->estado) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Resumen simplificado -->
                <div class="resumen-servicios mt-4 p-3 bg-light rounded">
                    <h5>Resumen de Servicios</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Total Servicios:</strong> <?php echo count($servicios); ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Pendientes:</strong> 
                            <?php 
                            $pendientes = array_filter($servicios, function($s) { 
                                return $s->estado === 'pendiente'; 
                            });
                            echo count($pendientes); 
                            ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Programados:</strong> 
                            <?php 
                            $programados = array_filter($servicios, function($s) { 
                                return $s->estado === 'programado'; 
                            });
                            echo count($programados); 
                            ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Completados:</strong> 
                            <?php 
                            $completados = array_filter($servicios, function($s) { 
                                return $s->estado === 'completado'; 
                            });
                            echo count($completados); 
                            ?>
                        </div>
                    </div>
                </div>
            <?php elseif (!$unidad): ?>
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-truck fa-3x mb-3"></i>
                    <h4>Selecciona una unidad</h4>
                    <p class="mb-0">Elige una unidad del menú desplegable para ver su historial de servicios</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Función para obtener opciones permitidas según el estado actual
    function obtenerOpcionesEstado(estadoActual) {
        const opciones = [];
        
        switch(estadoActual) {
            case 'pendiente':
                opciones.push(
                    { value: 'programado', text: 'Programado', icon: '📅' }
                );
                break;
                
            case 'programado':
                opciones.push(
                    { value: 'completado', text: 'Completado', icon: '✅' }
                );
                break;
                
            case 'completado':
                // No se permiten cambios desde estado completado
                break;
        }
        
        return opciones;
    }

    // Función para obtener color del badge
    function obtenerColorBadge(estado) {
        const colores = {
            'pendiente': '#ffc107',
            'programado': '#0d6efd', 
            'completado': '#198754'
        };
        return colores[estado] || '#6c757d';
    }

    // Función para obtener descripción del estado
    function obtenerDescripcionEstado(estado) {
        const descripciones = {
            'pendiente': 'El servicio está pendiente de programación',
            'programado': 'El servicio está programado para realizarse', 
            'completado': 'El servicio ha sido completado exitosamente'
        };
        return descripciones[estado] || 'Estado del servicio';
    }

    // Cambiar Estado del Servicio
    $(document).on('click', '.badge_servicio[data-servicio-id]', function() {
        const $badge = $(this);
        const servicioId = $badge.data('servicio-id');
        const estadoActual = $badge.text().trim().toLowerCase();
        const opciones = obtenerOpcionesEstado(estadoActual);
        
        // Si no hay opciones disponibles (estado completado)
        if (opciones.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Estado completado',
                text: 'Este servicio ya ha sido completado y no puede modificarse',
                confirmButtonText: 'Entendido'
            });
            return;
        }
        
        // Crear el HTML para las opciones
        let opcionesHTML = opciones.map(opcion => `
            <div class="status-option" data-value="${opcion.value}" 
                 style="padding: 1rem; margin: 0.5rem 0; border: 2px solid #e9ecef; border-radius: 0.5rem; cursor: pointer; transition: all 0.3s ease;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-size: 1.5rem;">${opcion.icon}</span>
                    <div>
                        <div style="font-weight: 600; font-size: 1.1rem;">${opcion.text}</div>
                        <div style="color: #6c757d; font-size: 0.9rem;">${obtenerDescripcionEstado(opcion.value)}</div>
                    </div>
                </div>
            </div>
        `).join('');
        
        let estadoSeleccionado = opciones[0].value;
        
        Swal.fire({
            title: '<strong>Cambiar estado del servicio</strong>',
            html: `
                <div class="text-center mb-3">
                    <p style="font-size: 1.1rem; margin-bottom: 1rem;">Estado actual: 
                        <span style="padding: 0.5rem 1rem; border-radius: 1rem; font-size: 1rem; font-weight: 500; background-color: ${obtenerColorBadge(estadoActual)}; color: white;">
                            ${estadoActual.charAt(0).toUpperCase() + estadoActual.slice(1)}
                        </span>
                    </p>
                </div>
                <div class="status-options-container">
                    ${opcionesHTML}
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Confirmar cambio',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
            width: '600px',
            customClass: {
                popup: 'border-radius-1'
            },
            didOpen: () => {
                // Seleccionar primera opción por defecto
                document.querySelector('.status-option').style.borderColor = obtenerColorBadge(estadoSeleccionado);
                document.querySelector('.status-option').style.backgroundColor = obtenerColorBadge(estadoSeleccionado) + '15';
                
                document.querySelectorAll('.status-option').forEach(option => {
                    option.addEventListener('mouseenter', function() {
                        if (!this.classList.contains('selected')) {
                            this.style.transform = 'translateY(-2px)';
                            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                        }
                    });
                    
                    option.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('selected')) {
                            this.style.transform = 'translateY(0)';
                            this.style.boxShadow = 'none';
                        }
                    });
                    
                    option.addEventListener('click', function() {
                        document.querySelectorAll('.status-option').forEach(opt => {
                            opt.style.borderColor = '#e9ecef';
                            opt.style.backgroundColor = 'transparent';
                            opt.classList.remove('selected');
                        });
                        
                        this.style.borderColor = obtenerColorBadge(this.getAttribute('data-value'));
                        this.style.backgroundColor = obtenerColorBadge(this.getAttribute('data-value')) + '15';
                        this.classList.add('selected');
                        estadoSeleccionado = this.getAttribute('data-value');
                    });
                });
            },
            preConfirm: () => {
                if (!estadoSeleccionado) {
                    Swal.showValidationMessage('Por favor selecciona un estado');
                    return false;
                }
                return estadoSeleccionado;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const nuevoEstado = result.value;
                realizarCambioEstado(servicioId, nuevoEstado, $badge, estadoActual);
            }
        });
    });

    // Función para realizar el cambio de estado
    function realizarCambioEstado(servicioId, nuevoEstado, $badge, estadoActual) {
        // Mostrar loading
        $badge.html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Enviar petición AJAX
        $.ajax({
            url: '/servicios_de_unidades/cambiar_estado',
            method: 'POST',
            data: {
                id_servicio: servicioId,
                nuevo_estado: nuevoEstado,
                redirect_to_historial_de_servicios: 1,
                idunidad: '<?php echo $unidad->idunidad ?? ""; ?>'
            },
            success: function(response) {
                // Simular respuesta exitosa (ajustar según tu backend)
                setTimeout(() => {
                    // Actualizar badge
                    $badge.text(nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1))
                        .removeClass('estado-pendiente_servicio estado-programado_servicio estado-completado_servicio')
                        .addClass('estado-' + nuevoEstado)
                        .css({
                            'backgroundColor': obtenerColorBadge(nuevoEstado),
                            'color': 'white'
                        });
                    
                    Swal.fire('¡Éxito!', 'Estado del servicio actualizado correctamente', 'success');
                    
                    // Recargar la página para actualizar los contadores
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    
                }, 500);
            },
            error: function(xhr) {
                Swal.fire('Error', 'Error al actualizar el estado del servicio', 'error');
                // Restaurar estado original
                $badge.text(estadoActual.charAt(0).toUpperCase() + estadoActual.slice(1))
                    .removeClass('estado-pendiente_servicio estado-programado_servicio estado-completado_servicio')
                    .addClass('estado-' + estadoActual)
                    .css({
                        'backgroundColor': obtenerColorBadge(estadoActual),
                        'color': 'white'
                    });
            }
        });
    }
});

</script>