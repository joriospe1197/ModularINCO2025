<a href="/pedidos/ver?id=<?= $pedido->id ?>" class="btn-regresar">⬅ Regresar al Pedido</a>

<?php if (!empty($alertas['error'])): ?>
    <div class="alerta error mb-4">
        <?php foreach ($alertas['error'] as $error): ?>
            <p><?php echo $error; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="contenedor-pedido-constructora">
    <div class="card-formulario-pedido">
        <div class="card-body">
            <form method="POST" class="formulario-pedido">
                <div class="seccion-formulario">
                    <h2><i class="fas fa-info-circle"></i> Datos del Pedido</h2>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="campo-formulario">
                                <label for="id_empleado_chofer">Chofer asignado *</label>
                                <select name="id_empleado_chofer" id="id_empleado_chofer" required>
                                    <option value="">Seleccionar chofer...</option>
                                    <?php foreach ($choferes as $chofer): ?>
                                        <option value="<?php echo $chofer->idempleado; ?>" 
                                            <?= $chofer->idempleado == $pedido->id_empleado_chofer ? 'selected' : '' ?>>
                                            <?php echo htmlspecialchars($chofer->nombre); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="campo-formulario">
                                <label for="fecha_pedido">Fecha del pedido *</label>
                                <input type="date" name="fecha_pedido" id="fecha_pedido" required
                                       value="<?php echo htmlspecialchars($pedido->fecha_pedido); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="campo-formulario">
                                <label for="nombre_cliente">Nombre del Cliente *</label>
                                <input type="text" name="nombre_cliente" id="nombre_cliente" required
                                       value="<?php echo htmlspecialchars($pedido->nombre_cliente); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="campo-formulario">
                                <label for="domicilio_cliente">Domicilio *</label>
                                <input type="text" name="domicilio_cliente" id="domicilio_cliente" required
                                       value="<?php echo htmlspecialchars($pedido->domicilio_cliente); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="campo-formulario">
                                <label for="telefono_cliente">Teléfono</label>
                                <input type="tel" name="telefono_cliente" id="telefono_cliente"
                                       value="<?php echo htmlspecialchars($pedido->telefono_cliente ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="seccion-formulario">
                    <h2><i class="fas fa-boxes"></i> Productos</h2>
                    
                    <div id="productos-container" class="lista-productos">
                        <?php if (!empty($productos_pedido)): ?>
                            <?php foreach ($productos_pedido as $index => $producto): ?>
                            <div class="producto-item">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <select name="productos[]" class="form-control" required>
                                            <option value="">Seleccionar producto...</option>
                                            <?php foreach ($productos as $prod): ?>
                                                <option value="<?php echo $prod->idproducto; ?>"
                                                    <?= $prod->idproducto == $producto['idproducto'] ? 'selected' : '' ?>>
                                                    <?php echo htmlspecialchars($prod->descripcion); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="cantidades[]" class="form-control" 
                                               min="1" value="<?php echo $producto['cantidad']; ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <?php if ($index === 0): ?>
                                            <button type="button" class="btn btn-add-producto">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-remove-producto">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Si no hay productos, mostrar un campo vacío -->
                            <div class="producto-item">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <select name="productos[]" class="form-control" required>
                                            <option value="">Seleccionar producto...</option>
                                            <?php foreach ($productos as $prod): ?>
                                                <option value="<?php echo $prod->idproducto; ?>">
                                                    <?php echo htmlspecialchars($prod->descripcion); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="cantidades[]" class="form-control" 
                                               min="1" value="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-add-producto">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="seccion-formulario">
                    <h2><i class="fas fa-comment-alt"></i> Observaciones</h2>
                    <div class="campo-formulario">
                        <textarea name="observaciones" class="form-control"><?php 
                            echo htmlspecialchars($pedido->observaciones ?? ''); 
                        ?></textarea>
                    </div>
                </div>
                
                <div class="acciones-formulario">
                    <div class="btn">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i> Actualizar Pedido
                        </button>
                        <a href="/pedidos/ver?id=<?= $pedido->id ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// El mismo script de agregar.php para manejar productos
document.addEventListener('DOMContentLoaded', function() {
    // Añadir nuevo producto
    $(document).on('click', '.btn-add-producto', function() {
        const newItem = $('.producto-item:first').clone();
        newItem.find('select').val('');
        newItem.find('input').val('1');
        newItem.find('.btn-add-producto')
            .removeClass('btn-add-producto').addClass('btn-remove-producto')
            .html('<i class="fas fa-minus"></i>');
        $('#productos-container').append(newItem);
    });

    // Eliminar producto
    $(document).on('click', '.btn-remove-producto', function() {
        if ($('.producto-item').length > 1) {
            $(this).closest('.producto-item').remove();
        }
    });
});
</script>