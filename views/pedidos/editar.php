<a href="/pedidos/ver?id=<?= $pedido->id ?>" class="btn-regresar">⬅ Regresar al Pedido</a>

<?php if (!empty($alertas['error'])): ?>
    <div class="alerta error mb-4">
        <?php foreach ($alertas['error'] as $error): ?>
            <p><?php echo $error; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$servicio_original = $pedido->servicio ?? '';
?>

<div class="contenedor-pedido-constructora">
    <div class="card-formulario-pedido">
        <div class="card-body">
            <form method="POST" class="formulario-pedido" id="form-pedido">
                <!-- Campos ocultos para mantener la integridad del tipo de cliente -->
                <input type="hidden" name="tipo_cliente_original" value="<?= $pedido->id_cliente ? 'frecuente' : 'ocasional' ?>">
                <input type="hidden" name="id_cliente_original" value="<?= $pedido->id_cliente ?>">
                
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

                    <!-- Selector de tipo de servicio -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="campo-formulario">
                                <label for="servicio">Tipo de Servicio *</label>
                                <select name="servicio" id="servicio" required>
                                    <option value="">Seleccionar servicio...</option>
                                    <option value="Materiales" <?= ($pedido->servicio ?? '') == 'Materiales' ? 'selected' : '' ?>>Entrega de Materiales</option>
                                    <option value="Escombro" <?= ($pedido->servicio ?? '') == 'Escombro' ? 'selected' : '' ?>>Recolección de Escombro</option>
                                    <option value="Madera" <?= ($pedido->servicio ?? '') == 'Madera' ? 'selected' : '' ?>>Recolección de Madera</option>
                                   <!-- <option value="Basura" <?= ($pedido->servicio ?? '') == 'Basura' ? 'selected' : '' ?>>Recolección de Basura</option> -->
                                    <option value="Otro" <?= ($pedido->servicio ?? '') == 'Otro' ? 'selected' : '' ?>>Otro tipo de servicio</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Selector de tipo de cliente para edición (BLOQUEADO) -->
                    <div class="seleccion-cliente">
                        <div class="tipo-cliente-opciones">
                            <div class="opcion-cliente <?= $pedido->id_cliente ? 'activo' : '' ?>" data-tipo="frecuente">
                                <input type="radio" name="tipo_cliente" value="frecuente" id="radio-frecuente" 
                                    <?= $pedido->id_cliente ? 'checked' : '' ?> disabled>
                                <label for="radio-frecuente">👥 Cliente Frecuente</label>
                            </div>
                            <div class="opcion-cliente <?= !$pedido->id_cliente ? 'activo' : '' ?>" data-tipo="ocasional">
                                <input type="radio" name="tipo_cliente" value="ocasional" id="radio-ocasional" 
                                    <?= !$pedido->id_cliente ? 'checked' : '' ?> disabled>
                                <label for="radio-ocasional">👤 Cliente Ocasional</label>
                            </div>
                        </div>

                        <div class="campos-cliente">
                            <!-- Cliente Frecuente -->
                            <div class="campo-cliente-frecuente <?= $pedido->id_cliente ? 'activo' : '' ?>">
                                <div class="campo-formulario select-cliente-frecuente">
                                    <label for="id_cliente">Seleccionar Cliente *</label>
                                    <select name="id_cliente" id="id_cliente" <?= $pedido->id_cliente ? '' : 'disabled' ?>>
                                        <option value="">-- Seleccionar cliente --</option>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <option value="<?php echo $cliente->id; ?>" 
                                                <?= $cliente->id == $pedido->id_cliente ? 'selected' : '' ?>>
                                                <?php echo htmlspecialchars($cliente->razon_social); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Cliente Ocasional -->
                            <div class="campo-cliente-ocasional <?= !$pedido->id_cliente ? 'activo' : '' ?>">
                                <div class="campos-cliente-ocasional">
                                    <div class="campo-formulario">
                                        <label for="nombre_cliente">Nombre del Cliente *</label>
                                        <input type="text" name="nombre_cliente" id="nombre_cliente"
                                               value="<?php echo htmlspecialchars($pedido->nombre_cliente); ?>" 
                                               <?= !$pedido->id_cliente ? '' : 'disabled' ?>>
                                    </div>
                                    
                                    <div class="campo-formulario">
                                        <label for="domicilio_cliente">Domicilio *</label>
                                        <input type="text" name="domicilio_cliente" id="domicilio_cliente"
                                               value="<?php echo htmlspecialchars($pedido->domicilio_cliente); ?>" 
                                               <?= !$pedido->id_cliente ? '' : 'disabled' ?>>
                                    </div>
                                    
                                    <div class="campo-formulario">
                                        <label for="telefono_cliente">Teléfono</label>
                                        <input type="tel" name="telefono_cliente" id="telefono_cliente"
                                               value="<?php echo htmlspecialchars($pedido->telefono_cliente ?? ''); ?>" 
                                               <?= !$pedido->id_cliente ? '' : 'disabled' ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--  Sección de Productos -->
                <div class="seccion-formulario" id="seccion-productos" style="<?= ($pedido->servicio ?? '') != 'Materiales' ? 'display: none;' : '' ?>">
                    <h2><i class="fas fa-boxes"></i> Productos</h2>
                    
                    <div id="productos-container" class="lista-productos">
                        <?php if (!empty($productos_pedido)): ?>
                            <?php foreach ($productos_pedido as $index => $producto): ?>
                            <div class="producto-item">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <select name="productos[]" class="form-control" <?= ($pedido->servicio ?? '') == 'Materiales' ? 'required' : '' ?>>
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
                                               min="1" value="<?php echo $producto['cantidad']; ?>" <?= ($pedido->servicio ?? '') == 'Materiales' ? 'required' : '' ?>>
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
                                        <select name="productos[]" class="form-control">
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
                                            min="1" value="1">
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
document.addEventListener('DOMContentLoaded', function() {
    //  Función para mostrar/ocultar sección de productos 
    function toggleSeccionProductos() {
        const servicio = document.getElementById('servicio').value;
        const seccionProductos = document.getElementById('seccion-productos');
        
        if (servicio === 'Materiales') {
            seccionProductos.style.display = 'block';
            
            // Solo cambiar el required, NUNCA deshabilitar
            document.querySelectorAll('select[name="productos[]"], input[name="cantidades[]"]').forEach(campo => {
                campo.required = true;
            });
        } else {
            seccionProductos.style.display = 'none';
            
            // Solo quitar required, NUNCA deshabilitar
            document.querySelectorAll('select[name="productos[]"], input[name="cantidades[]"]').forEach(campo => {
                campo.required = false;
            });
        }
    }

    //  Mostrar advertencia al cambiar tipo de servicio 
    document.getElementById('servicio').addEventListener('change', function() {
        const servicioOriginal = '<?= $servicio_original ?>';
        const servicioNuevo = this.value;
        
        // Si cambia A Materiales DESDE otro servicio
        if (servicioOriginal !== 'Materiales' && servicioNuevo === 'Materiales') {
            Swal.fire({
                icon: 'info',
                title: 'Productos requeridos',
                html: `📦 <strong>Ahora necesitas agregar productos</strong><br><br>
                    Por favor agrega al menos un producto para el servicio de Materiales.`,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                toggleSeccionProductos();
            });
        } else {
            toggleSeccionProductos();
        }
    });

    //  Validación del formulario
    document.getElementById('form-pedido').addEventListener('submit', function(e) {
        const servicioNuevo = document.getElementById('servicio').value;
        
        let isValid = true;
        let mensajeError = '';

        // Validar productos para servicios de Materiales
        if (servicioNuevo === 'Materiales') {
            const productosSeleccionados = Array.from(document.querySelectorAll('select[name="productos[]"]'))
                .some(select => select.value !== '');
            
            if (!productosSeleccionados) {
                isValid = false;
                mensajeError = 'Para servicios de Materiales, debes agregar al menos un producto';
            }
        }

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: mensajeError,
                confirmButtonColor: '#3085d6'
            });
        }
    });


    // Inicializacion
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar visibilidad de productos
        toggleSeccionProductos();
    });


    //  codigo para añadir y eliminar productos
    $(document).on('click', '.btn-add-producto', function() {
        const newItem = $('.producto-item:first').clone();
        newItem.find('select').val('');
        newItem.find('input').val('1');
        newItem.find('.btn-add-producto')
            .removeClass('btn-add-producto').addClass('btn-remove-producto')
            .html('<i class="fas fa-minus"></i>');
        $('#productos-container').append(newItem);
    });

    $(document).on('click', '.btn-remove-producto', function() {
        if ($('.producto-item').length > 1) {
            $(this).closest('.producto-item').remove();
        }
    });
});
</script>