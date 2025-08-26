<a href="/pedidos" class="btn-regresar">⬅ Regresar</a>

<div class="contenedor-pedido-constructora">
    <div class="card-formulario-pedido">
        <div class="card-body">
            <div class="seccion-formulario">
                <h2><i class="fas fa-info-circle"></i> Información del Pedido</h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Folio del Pedido</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($pedido->codigo_pedido) ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Estado</strong></label>
                            <div class="valor-campo">
                                <span class="badge estado-<?= strtolower(str_replace(' ', '_', $pedido->estado)) ?>">
                                    <?= ucfirst($pedido->estado) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Fecha del Pedido</strong></label>
                            <div class="valor-campo"><?= date('d/m/Y', strtotime($pedido->fecha_pedido)) ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Chofer Asignado</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($pedido->chofer ?? 'Sin asignar') ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Registrado por</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($pedido->empleado_registra) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="seccion-formulario">
                <h2><i class="fas fa-user"></i> Datos del Cliente</h2>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="campo-formulario">
                            <label><strong>Nombre del Cliente</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($pedido->nombre_cliente) ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="campo-formulario">
                            <label><strong>Domicilio</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($pedido->domicilio_cliente) ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="campo-formulario">
                            <label><strong>Teléfono</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($pedido->telefono_cliente ?? 'No especificado') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="seccion-formulario">
                <h2><i class="fas fa-boxes"></i> Productos del Pedido</h2>
                
                <div class="table-responsive">
                    <table class="tabla-pedido-productos">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- productos -->
                            <?php 
                            $total = 0;
                            if (!empty($productos)): 
                                foreach ($productos as $producto): 
                                    $subtotal = $producto['cantidad'] * $producto['precio'];
                                    $total += $subtotal;
                            ?>
                            <tr>
                                <td data-label="Producto"><?= htmlspecialchars($producto['descripcion']) ?></td>
                                <td data-label="Cantidad"><?= $producto['cantidad'] ?></td>
                                <td data-label="Precio">$<?= number_format($producto['precio'], 2) ?></td>
                                <td data-label="Subtotal">$<?= number_format($subtotal, 2) ?></td>
                            </tr>
                        
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-muted">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No hay productos en este pedido
                                </td>
                            </tr>
                            <?php endif; ?>
                        
                        </tbody>
                        <?php if (!empty($productos)): ?>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td><strong>$<?= number_format($total, 2) ?></strong></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <?php if (!empty($pedido->observaciones)): ?>
            <div class="seccion-formulario">
                <h2><i class="fas fa-comment-alt"></i> Observaciones</h2>
                <div class="campo-formulario">
                    <div class="valor-campo observaciones">
                        <?= nl2br(htmlspecialchars($pedido->observaciones)) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="acciones-formulario">
                <div class="btn">
                    <a href="/pedidos/editar?id=<?= $pedido->id ?>" class="btn-warning">
                        <i class="fas fa-edit me-1"></i> Editar Pedido
                    </a>
                    <a href="/pedidos" class="btn-secondary">
                        <i class="fas fa-list me-1"></i> Volver a Lista
                    </a>

                    <?php if ($_SESSION['tipo_usuario'] == 1): ?>
                    <button type="button" class="btn btn-danger" 
                            onclick="confirmarEliminacion(<?= $pedido->id ?>, '<?= $pedido->codigo_pedido ?>')">
                        <i class="fas fa-trash me-1"></i> Eliminar Pedido
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h4 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminación
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el pedido <strong id="folio-pedido"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    <strong>Esta acción no se puede deshacer.</strong> Se eliminarán todos los productos asociados.
                </p>
                
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="confirmacion-extra">
                    <label class="form-check-label" for="confirmacion-extra">
                        Confirmo que deseo eliminar este pedido permanentemente
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <form id="form-eliminar" method="POST" action="/pedidos/eliminar">
                    <input type="hidden" name="id" id="pedido-id">
                    <button type="submit" class="btn btn-danger btn-lg" id="btn-confirmar-eliminar" disabled>
                        <i class="fas fa-trash me-1"></i> Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarEliminacion(id, folio) {
    // Actualizar el modal con los datos del pedido
    document.getElementById('folio-pedido').textContent = folio;
    document.getElementById('pedido-id').value = id;
    
    // Resetear la confirmación extra
    document.getElementById('confirmacion-extra').checked = false;
    document.getElementById('btn-confirmar-eliminar').disabled = true;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminacion'));
    modal.show();
}

// Habilitar el botón solo cuando se marque la confirmación extra
document.getElementById('confirmacion-extra').addEventListener('change', function() {
    document.getElementById('btn-confirmar-eliminar').disabled = !this.checked;
});

// Prevenir envío del formulario si no está habilitado
document.getElementById('form-eliminar').addEventListener('submit', function(e) {
    if (document.getElementById('btn-confirmar-eliminar').disabled) {
        e.preventDefault();
        alert('Debes confirmar la eliminación marcando la casilla');
    }
});
</script>