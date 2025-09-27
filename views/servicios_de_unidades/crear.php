<!-- Alertas de operaciones exitosas/errores -->
<?php if (!empty($_SESSION['alerta'])): ?>
    <div class="alerta-crear-servicio <?php echo $_SESSION['alerta']['tipo'] === 'exito' ? 'exito' : 'error'; ?> mb-4">
        <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
    </div>
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>

<div class="contenedor-crear-servicio">
    <div class="card-crear-servicio">
        <div class="card-header-crear-servicio">
            <h2 class="card-title mb-0"><i class="fas fa-plus-circle"></i> Registrar Nuevo Servicio</h2>
            <a href="/servicios_de_unidades" class="btn-crear-servicio btn-secondary-crear btn-volver-crear">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        
        <div class="card-body-crear-servicio">
            <!-- Alertas de validación -->
            <?php if (!empty($alertas)): ?>
                <?php foreach ($alertas as $tipo => $mensajes): ?>
                    <?php foreach ($mensajes as $mensaje): ?>
                        <div class="alerta-crear-servicio <?php echo $tipo; ?> mb-4">
                            <p><?php echo $mensaje; ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <form method="POST" class="formulario-servicio">
                <div class="row">
                    <!-- Unidad -->
                    <div class="col-md-6 mb-3">
                        <label for="idunidad" class="form-label">Unidad *</label>
                        <select class="form-control" id="idunidad" name="idunidad" required>
                            <option value="">Selecciona una unidad</option>
                            <?php foreach ($unidades as $unidad): ?>
                                <option value="<?php echo $unidad->idunidad; ?>" 
                                    <?php echo (isset($servicio->idunidad) && $servicio->idunidad == $unidad->idunidad) ? 'selected' : ''; ?>>
                                    <?php echo $unidad->modelo . ' - ' . $unidad->placas . ' (' . $unidad->chofer_nombre . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tipo de Servicio -->
                    <div class="col-md-6 mb-3">
                        <label for="id_tipo_servicio" class="form-label">Tipo de Servicio *</label>
                        <select class="form-control" id="id_tipo_servicio" name="id_tipo_servicio" required>
                            <option value="">Selecciona un tipo de servicio</option>
                            <?php foreach ($tipos_servicio as $tipo): ?>
                                <option value="<?php echo $tipo->id_tipo_servicio; ?>" 
                                    data-intervalo="<?php echo $tipo->intervalo_meses; ?>"
                                    data-descripcion="<?php echo htmlspecialchars($tipo->descripcion); ?>"
                                    <?php echo (isset($servicio->id_tipo_servicio) && $servicio->id_tipo_servicio == $tipo->id_tipo_servicio) ? 'selected' : ''; ?>>
                                    <?php echo $tipo->nombre_servicio . ' (Cada ' . $tipo->intervalo_meses . ' meses)'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="descripcion-servicio" id="descripcion-servicio"></small>
                    </div>
                </div>

                <div class="row">
                    <!-- Fecha del Servicio -->
                    <div class="col-md-6 mb-3">
                        <label for="fecha_servicio" class="form-label">Fecha del Servicio *</label>
                        <input type="date" class="form-control" id="fecha_servicio" name="fecha_servicio" 
                               value="<?php echo isset($servicio->fecha_servicio) ? $servicio->fecha_servicio : date('Y-m-d'); ?>" required>
                    </div>

                    <!-- Descripción -->
                    <div class="col-md-6 mb-3">
                        <label for="descripcion_servicio" class="form-label">Descripción del Servicio (Opcional)</label>
                        <input type="text" class="form-control" id="descripcion_servicio" name="descripcion_servicio" 
                               value="<?php echo $servicio->descripcion_servicio ?? ''; ?>" placeholder="Detalles del servicio...">
                    </div>
                </div>

                <!-- Información de próximo servicio -->
                <div class="info-proximo-servicio">
                    <h6><i class="fas fa-info-circle"></i> Información del Próximo Servicio</h6>
                    <p id="info-proximo-servicio">Se calculará automáticamente al guardar</p>
                </div>

                <!-- Botones -->
                <div class="botones-formulario-servicio">
                    <a href="/servicios_de_unidades" class="btn-crear-servicio btn-secondary-crear">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn-crear-servicio btn-primary-crear">
                        <i class="fas fa-save"></i> Registrar Servicio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoServicioSelect = document.getElementById('id_tipo_servicio');
    const fechaServicioInput = document.getElementById('fecha_servicio');
    const infoProximoServicio = document.getElementById('info-proximo-servicio');
    const descripcionServicio = document.getElementById('descripcion-servicio');

    // Mostrar descripción del servicio seleccionado
    tipoServicioSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const descripcion = selectedOption.getAttribute('data-descripcion');
        descripcionServicio.textContent = descripcion || '';
        
        calcularProximoServicio();
    });

    // Calcular próximo servicio cuando cambia la fecha
    fechaServicioInput.addEventListener('change', calcularProximoServicio);

    function calcularProximoServicio() {
        const tipoServicio = tipoServicioSelect.value;
        const fechaServicio = fechaServicioInput.value;
        
        if (tipoServicio && fechaServicio) {
            const selectedOption = tipoServicioSelect.options[tipoServicioSelect.selectedIndex];
            const intervalo = parseInt(selectedOption.getAttribute('data-intervalo'));
            
            const fecha = new Date(fechaServicio);
            fecha.setMonth(fecha.getMonth() + intervalo);
            
            const proximaFecha = fecha.toISOString().split('T')[0];
            const fechaFormateada = new Date(proximaFecha).toLocaleDateString('es-MX');
            
            infoProximoServicio.innerHTML = 
                `<strong>Próximo servicio:</strong> ${fechaFormateada} <br>
                 <strong>Intervalo:</strong> Cada ${intervalo} meses`;
        } else {
            infoProximoServicio.textContent = 'Se calculará automáticamente al guardar';
        }
    }

    // Calcular al cargar la página si hay valores
    if (tipoServicioSelect.value && fechaServicioInput.value) {
        calcularProximoServicio();
    }
});
</script>