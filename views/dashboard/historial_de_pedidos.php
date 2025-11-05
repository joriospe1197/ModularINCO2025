

<h2>Historial y Reportes de Pedidos</h2>
<?php if (!empty($_SESSION['alerta'])): ?>
    <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
        <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
    </div>
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>


<!-- Pestañas -->
<div class="pestanas">
    <button class="pestana-btn active" onclick="mostrarTab('detalle')">📋 Vista Detallada</button>
    <button class="pestana-btn" onclick="mostrarTab('resumen')">📊 Generar Resumen</button>
    <button class="pestana-btn" onclick="mostrarTab('reportes')">📈 Reportes Consolidados</button>
</div>

<!-- Contenido de Pestaña 1: Vista Detallada -->
<div id="tab-detalle" class="tab-contenido active">
    <div class="filtros">
        <form method="GET" class="form-filtros">
            <input type="hidden" name="tab" value="detalle">
            <div class="campo">
                <label for="chofer">Chofer:</label>
                <select name="chofer" id="chofer" onchange="this.form.submit()">
                    <?php foreach ($choferes as $chofer): ?>
                        <option value="<?= $chofer->idempleado ?>" 
                                <?= $chofer->idempleado == $choferSeleccionado ? 'selected' : '' ?>>
                            <?= $chofer->nombre ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="campo">
                <label for="fecha_inicio">Desde:</label>
                <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?>" 
                       onchange="this.form.submit()">
            </div>
            
            <div class="campo">
                <label for="fecha_fin">Hasta:</label>
                <input type="date" name="fecha_fin" value="<?= $fecha_fin ?>" 
                       onchange="this.form.submit()">
            </div>
        </form>
    </div>

    <?php if (!empty($pedidos)): ?>
    <div class="tabla-contenedor">
        <h3>Pedidos de <?= $nombre_chofer ?> 
            (<?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?>)
        </h3>
        
        <table class="tabla-historial">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Servicio</th>
                    <th>Gastos</th>
                    <th>Costo</th>
                    <th>Pagado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= $pedido->codigo_pedido ?></td>
                        <td><?= date('d/m/Y', strtotime($pedido->fecha_pedido)) ?></td>
                        <td><?= htmlspecialchars($pedido->nombre_cliente) ?></td>
                        <td><?= !empty($pedido->servicio) ? $pedido->servicio : 'N/A' ?></td>
                        <td>$<?= number_format($pedido->gastos, 2) ?></td>
                        <td>$<?= number_format($pedido->costo, 2) ?></td>
                        <td>$<?= number_format($pedido->pagados, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-fila">
                    <td colspan="4"><strong>TOTALES:</strong></td>
                    <td><strong>$<?= number_format($totales['gastos'], 2) ?></strong></td>
                    <td><strong>$<?= number_format($totales['costo'], 2) ?></strong></td>
                    <td><strong>$<?= number_format($totales['pagados'], 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
        <p class="no-resultados">No hay pedidos registrados para este chofer en el período seleccionado.</p>
    <?php endif; ?>
</div>

<!-- Contenido de Pestaña 2: Generar Resumen -->
<div id="tab-resumen" class="tab-contenido">
    <div class="historial-resumen-contenedor historial-resumen-fade-in">
        <h3 class="historial-resumen-titulo">📊 Generar Resumen Semanal</h3>

        <?php if (!empty($alertas)): ?>
            <div class="historial-resumen-alerta <?php echo key($alertas) === 'error' ? 'error' : 'exito'; ?>">
                <?php foreach ($alertas as $tipo => $mensajes): ?>
                    <?php foreach ($mensajes as $mensaje): ?>
                        <p><?php echo $mensaje; ?></p>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="historial-resumen-formulario">
            <input type="hidden" name="tab" value="resumen">
            
            <div class="historial-resumen-campo">
                <label for="resumen_chofer">Chofer</label>
                <select name="chofer" id="resumen_chofer" required>
                    <option value="">-- Seleccionar Chofer --</option>
                    <?php foreach ($choferes as $chofer): ?>
                        <option value="<?= $chofer->idempleado ?>" 
                            <?= isset($resumen) && $resumen->chofer == $chofer->idempleado ? 'selected' : '' ?>>
                            <?= $chofer->nombre ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="historial-resumen-campo">
                <label for="fecha_inicio_resumen">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio_resumen" 
                       value="<?= isset($resumen) ? $resumen->fecha_inicio : '' ?>" required>
            </div>

            <div class="historial-resumen-campo">
                <label for="fecha_fin_resumen">Fecha Fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin_resumen" 
                       value="<?= isset($resumen) ? $resumen->fecha_fin : '' ?>" required>
            </div>

            <div class="historial-resumen-campo">
                <label for="justificacion">Justificación de Gastos</label>
                <textarea name="justificacion" id="justificacion" 
                          rows="4" placeholder="Explicación de gastos adicionales o situaciones especiales..."><?= isset($resumen) ? $resumen->justificacion : '' ?></textarea>
            </div>

            <input type="submit" value="Generar Resumen" class="historial-resumen-boton">
        </form>
    </div>
</div>

<!-- Contenido de Pestaña 3: Reportes Consolidados -->
<div id="tab-reportes" class="tab-contenido">
    <div class="historial-filtros">
        <form method="GET" class="historial-form-filtros">
            <input type="hidden" name="tab" value="reportes">
            <div class="historial-campo">
                <label for="reporte_chofer">Filtrar por Chofer:</label>
                <select name="chofer" id="reporte_chofer" onchange="this.form.submit()">
                    <option value="">-- Todos los choferes --</option>
                    <?php foreach ($choferes as $chofer): ?>
                        <option value="<?= $chofer->idempleado ?>" 
                            <?= $chofer->idempleado == $choferReporteSeleccionado ? 'selected' : '' ?>>
                            <?= $chofer->nombre ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if (!empty($reportes)): ?>
    <div class="historial-tabla-contenedor">
        <h3><?= $titulo_reportes ?></h3>
        
        <table class="historial-tabla-reportes">
            <thead>
                <tr>
                    <th>Período</th>
                    <?php if (!$choferReporteSeleccionado): ?>
                        <th>Chofer</th>
                    <?php endif; ?>
                    <th>Gastos</th>
                    <th>Costos</th>
                    <th>Pagado</th>
                    <th>Utilidad</th>
                    <th>Justificación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportes as $reporte): ?>
                <tr>
                    <td>
                        <?php if (empty($reporte->fecha_inicio) || $reporte->fecha_inicio === 'Fecha no definida'): ?>
                            Período no definido
                        <?php else: ?>
                            <?= date('d/m/Y', strtotime($reporte->fecha_inicio)) ?> - 
                            <?= date('d/m/Y', strtotime($reporte->fecha_fin)) ?>
                        <?php endif; ?>
                    </td>
                    <?php if (!$choferReporteSeleccionado): ?>
                        <td><?= htmlspecialchars($reporte->nombre_chofer) ?></td>
                    <?php endif; ?>
                    <td>$<?= number_format($reporte->total_gastos, 2) ?></td>
                    <td>$<?= number_format($reporte->total_costos, 2) ?></td>
                    <td>$<?= number_format($reporte->total_pagados, 2) ?></td>
                    <td class="<?= $reporte->utilidad_neta >= 0 ? 'positivo' : 'negativo' ?>">
                        $<?= number_format($reporte->utilidad_neta, 2) ?>
                    </td>
                    <td class="justificacion-cell">
                        <?= nl2br(htmlspecialchars($reporte->justificacion)) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p class="historial-no-resultados">No hay reportes consolidados para mostrar</p>
    <?php endif; ?>
</div>

<script>
// Funcionalidad de pestañas
function mostrarTab(tabId) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-contenido').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Desactivar todos los botones
    document.querySelectorAll('.pestana-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Mostrar la pestaña seleccionada
    document.getElementById('tab-' + tabId).classList.add('active');
    
    // Activar el botón seleccionado
    document.querySelector(`.pestana-btn[onclick="mostrarTab('${tabId}')"]`).classList.add('active');
    
    // Guardar en sessionStorage
    sessionStorage.setItem('tabActiva', tabId);
}

// Cargar pestaña activa al recargar
document.addEventListener('DOMContentLoaded', function() {
    const tabActiva = sessionStorage.getItem('tabActiva') || 'detalle';
    mostrarTab(tabActiva);
});
</script>

