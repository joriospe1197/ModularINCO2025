<a href="/pedidos" class="btn-regresar">⬅ Regresar</a>
<?php if (!empty($_SESSION['alerta'])): ?>
    <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
        <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
    </div>
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>

<div class="contenedor-pedido-constructora">
    <div class="card-formulario-pedido">
        <div class="card-body">

            <?php if (!empty($alertas['error'])): ?>
                <div class="alerta error mb-4">
                    <?php foreach ($alertas['error'] as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

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
                                        <option value="<?php echo $chofer->idempleado; ?>">
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
                                       value="<?php echo htmlspecialchars($pedido->fecha_pedido ?? date('Y-m-d')); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="campo-formulario">
                                <label for="nombre_cliente">Nombre del Cliente *</label>
                                <input type="text" name="nombre_cliente" id="nombre_cliente" required
                                       value="<?php echo htmlspecialchars($pedido->nombre_cliente ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="campo-formulario">
                                <label for="domicilio_cliente">Domicilio *</label>
                                <input type="text" name="domicilio_cliente" id="domicilio_cliente" required
                                       value="<?php echo htmlspecialchars($pedido->domicilio_cliente ?? ''); ?>">
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
                        <?php 
                        $productos_post = $_POST['productos'] ?? [''];
                        $cantidades_post = $_POST['cantidades'] ?? [1];
                        foreach ($productos_post as $index => $producto_id): 
                        ?>
                        <div class="producto-item">
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <select name="productos[]" class="form-control" required>
                                        <option value="">Seleccionar producto...</option>
                                        <?php foreach ($productos as $producto): ?>
                                            <option value="<?php echo $producto->idproducto; ?>">
                                                <?php echo htmlspecialchars($producto->descripcion); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="cantidades[]" class="form-control" 
                                           min="1" value="<?php echo $cantidades_post[$index] ?? 1; ?>" required>
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
                
                
                <div class="acciones-formulario ">
                    <div class="btn">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Guardar Pedido
                        </button>
                        <button type="reset" class="btn btn-secondary ms-2">
                            <i class="fas fa-undo mr-1"></i> Limpiar
                        </button>     
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Añadir nuevo producto
    $(document).on('click', '.btn-add-producto', function() {
        const newItem = $('.producto-item:first').clone();
        newItem.find('select').val('');
        newItem.find('input').val('1');
        newItem.find('.btn-add-producto')
            .removeClass('btn-success').addClass('btn-danger')
            .html('<i class="fas fa-minus"></i>')
            .removeClass('btn-add-producto').addClass('btn-remove-producto');
        $('#productos-container').append(newItem);
    });

    // Eliminar producto
    $(document).on('click', '.btn-remove-producto', function() {
        if ($('.producto-item').length > 1) {
            $(this).closest('.producto-item').remove();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Debe haber al menos un producto',
                confirmButtonColor: '#3085d6'
            });
        }
    });

    // Validación del formulario
    $('#form-pedido').on('submit', function(e) {
        let isValid = true;
        
        // Validar campos requeridos
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Complete todos los campos obligatorios',
                confirmButtonColor: '#3085d6'
            });
        }
    });
});
</script>