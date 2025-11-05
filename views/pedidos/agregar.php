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

            <form method="POST" class="formulario-pedido" id="form-pedido">
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

                    <!-- Selector de tipo de servicio -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="campo-formulario">
                                <label for="servicio">Tipo de Servicio *</label>
                                <select name="servicio" id="servicio" required>
                                    <option value="">Seleccionar servicio...</option>
                                    <option value="Materiales">Entrega de Materiales</option>
                                    <option value="Escombro">Recolección de Escombro</option>
                                    <option value="Madera">Recolección de Madera</option>
                                    <!--<option value="Basura">Recolección de Basura</option> -->
                                    <option value="Otro">Otro tipo de servicio</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Selección de tipo de cliente -->
                    <div class="seleccion-cliente">
                        <div class="tipo-cliente-opciones">
                            <div class="opcion-cliente activo" data-tipo="frecuente">
                                <input type="radio" name="tipo_cliente" value="frecuente" id="radio-frecuente" checked>
                                <label for="radio-frecuente">👥 Cliente Frecuente</label>
                            </div>
                            <div class="opcion-cliente" data-tipo="ocasional">
                                <input type="radio" name="tipo_cliente" value="ocasional" id="radio-ocasional">
                                <label for="radio-ocasional">👤 Cliente Ocasional</label>
                            </div>
                        </div>

                        <div class="campos-cliente">
                            <!-- Cliente Frecuente -->
                            <div class="campo-cliente-frecuente activo">
                                <div class="campo-formulario select-cliente-frecuente">
                                    <label for="id_cliente">Seleccionar Cliente *</label>
                                    <select name="id_cliente" id="id_cliente">
                                        <option value="">-- Seleccionar cliente --</option>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <option value="<?php echo $cliente->id; ?>">
                                                <?php echo htmlspecialchars($cliente->razon_social); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Cliente Ocasional -->
                            <div class="campo-cliente-ocasional">
                                <div class="campos-cliente-ocasional">
                                    <div class="campo-formulario">
                                        <label for="nombre_cliente">Nombre del Cliente *</label>
                                        <input type="text" name="nombre_cliente" id="nombre_cliente"
                                               value="<?php echo htmlspecialchars($pedido->nombre_cliente ?? ''); ?>">
                                    </div>
                                    
                                    <div class="campo-formulario">
                                        <label for="domicilio_cliente">Domicilio *</label>
                                        <input type="text" name="domicilio_cliente" id="domicilio_cliente"
                                               value="<?php echo htmlspecialchars($pedido->domicilio_cliente ?? ''); ?>">
                                    </div>
                                    
                                    <div class="campo-formulario">
                                        <label for="telefono_cliente">Teléfono</label>
                                        <input type="tel" name="telefono_cliente" id="telefono_cliente"
                                               value="<?php echo htmlspecialchars($pedido->telefono_cliente ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Productos (oculta inicialmente) -->
                <div class="seccion-formulario" id="seccion-productos" style="display: none;">
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
                                    <select name="productos[]" class="form-control">
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
                                           min="1" value="<?php echo $cantidades_post[$index] ?? 1; ?>">
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
                
                <div class="acciones-formulario">
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
    //  Alternar bloques según tipo de cliente
    function toggleClienteFields() {
        const tipoFrecuente = document.getElementById('radio-frecuente').checked;
        const campoFrecuente = document.querySelector('.campo-cliente-frecuente');
        const campoOcasional = document.querySelector('.campo-cliente-ocasional');
        const opciones = document.querySelectorAll('.opcion-cliente');
        
        // Actualizar clases activas
        opciones.forEach(opcion => {
            if (opcion.getAttribute('data-tipo') === 'frecuente') {
                opcion.classList.toggle('activo', tipoFrecuente);
            } else {
                opcion.classList.toggle('activo', !tipoFrecuente);
            }
        });
        
        // Mostrar/ocultar campos
        campoFrecuente.classList.toggle('activo', tipoFrecuente);
        campoOcasional.classList.toggle('activo', !tipoFrecuente);
        
        // Actualizar required attributes
        document.getElementById('id_cliente').required = tipoFrecuente;
        document.getElementById('nombre_cliente').required = !tipoFrecuente;
        document.getElementById('domicilio_cliente').required = !tipoFrecuente;
    }

    //  Funcion para mostrar/ocultar seccion de productos
    function toggleSeccionProductos() {
        const servicio = document.getElementById('servicio').value;
        const seccionProductos = document.getElementById('seccion-productos');
        
        if (servicio === 'Materiales') {
            seccionProductos.style.display = 'block';
            
            // Hacer required los campos de productos
            document.querySelectorAll('select[name="productos[]"], input[name="cantidades[]"]').forEach(campo => {
                campo.required = true;
            });
        } else {
            seccionProductos.style.display = 'none';
            
            // Quitar required de los campos de productos
            document.querySelectorAll('select[name="productos[]"], input[name="cantidades[]"]').forEach(campo => {
                campo.required = false;
            });
        }
    }

    // Inicializar
    toggleClienteFields();
    toggleSeccionProductos();
    
    // Event listeners para los radio buttons
    document.getElementById('radio-frecuente').addEventListener('change', toggleClienteFields);
    document.getElementById('radio-ocasional').addEventListener('change', toggleClienteFields);
    
    // Event listener para el servicio
    document.getElementById('servicio').addEventListener('change', toggleSeccionProductos);
    
    // Event listeners para las opciones clickeables
    document.querySelectorAll('.opcion-cliente').forEach(opcion => {
        opcion.addEventListener('click', function() {
            const tipo = this.getAttribute('data-tipo');
            document.getElementById('radio-' + tipo).checked = true;
            toggleClienteFields();
        });
    });

    //  Si se elige cliente frecuente, traer datos con AJAX
    document.getElementById('id_cliente').addEventListener('change', function() {
        const clienteId = this.value;
        if (clienteId) {
            fetch('/pedidos/api-cliente?id=' + clienteId)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('nombre_cliente').value = data.razon_social || '';
                        document.getElementById('domicilio_cliente').value = data.domicilio || '';
                        document.getElementById('telefono_cliente').value = data.telefono || '';
                    }
                })
                .catch(error => {
                    console.error('Error fetching client data:', error);
                });
        } else {
            document.getElementById('nombre_cliente').value = '';
            document.getElementById('domicilio_cliente').value = '';
            document.getElementById('telefono_cliente').value = '';
        }
    });

    //  Validación del formulario
    document.getElementById('form-pedido').addEventListener('submit', function(e) {
        const tipoFrecuente = document.getElementById('radio-frecuente').checked;
        const servicio = document.getElementById('servicio').value;
        let isValid = true;

        // Validar tipo de cliente
        if (tipoFrecuente) {
            if (!document.getElementById('id_cliente').value) {
                isValid = false;
                alert('Por favor selecciona un cliente frecuente');
            }
        } else {
            if (!document.getElementById('nombre_cliente').value.trim() || 
                !document.getElementById('domicilio_cliente').value.trim()) {
                isValid = false;
                alert('Por favor completa los campos obligatorios del cliente ocasional');
            }
        }

        // Validar productos para servicios de Materiales
        if (servicio === 'Materiales') {
            const productosSeleccionados = Array.from(document.querySelectorAll('select[name="productos[]"]'))
                .some(select => select.value !== '');
            
            if (!productosSeleccionados) {
                isValid = false;
                alert('Para servicios de Materiales, debes agregar al menos un producto');
            }
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    // añadir y eliminar productos
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
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Debe haber al menos un producto',
                confirmButtonColor: '#3085d6'
            });
        }
    });
});
</script>