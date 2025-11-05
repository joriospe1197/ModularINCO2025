<div class="contenedor-pedidos">
    <!--alertas de eliminado -->
    <?php if (!empty($_SESSION['alerta'])): ?>
        <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
            <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
        </div>
        <?php unset($_SESSION['alerta']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="card-title mb-0"><i class="fas fa-list"></i> Lista de Pedidos</h2>
            <a href="/pedidos/agregar" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Pedido
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($pedidos)) : ?>
                <div class="alert alert-info">No hay pedidos registrados</div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="tabla-pedidos">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Registrado por</th>
                                <th>Chofer</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido) : ?>
                            <tr class="fila-clicable" data-pedido-id="<?= $pedido->id ?>">
                                <td>
                                    <button class="btn btn-outline-primary btn-folio" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalCliente"
                                            data-pedido-id="<?= $pedido->id ?>"
                                            data-tipo-cliente="<?= $pedido->id_cliente ? 'frecuente' : 'ocasional' ?>"
                                            data-id-cliente="<?= $pedido->id_cliente ?>"
                                            data-nombre="<?= htmlspecialchars($pedido->nombre_cliente) ?>"
                                            data-domicilio="<?= htmlspecialchars($pedido->domicilio_cliente) ?>"
                                            data-telefono="<?= htmlspecialchars($pedido->telefono_cliente ?? 'No especificado') ?>">
                                        <?= htmlspecialchars($pedido->codigo_pedido) ?>
                                    </button>
                                </td>
                                <td><?= htmlspecialchars($pedido->empleado_registra) ?></td>
                                <td><?= htmlspecialchars($pedido->chofer ?? 'Sin asignar') ?></td>
                                <td><?= date('d/m/Y', strtotime($pedido->fecha_pedido)) ?></td>
                                <td>
                                    <span class="badge estado-<?= strtolower(str_replace(' ', '_', $pedido->estado)) ?>" 
                                        data-pedido-id="<?= $pedido->id ?>"
                                        style="cursor: pointer;">
                                        <?= ucfirst($pedido->estado) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/pedidos/ver?id=<?= $pedido->id ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<!-- Modal para datos del cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="margin-top: 2rem;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">Datos del Cliente</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- div para el tipo de cliente -->
                <div class="mb-2">
                    <span id="modal-tipo-cliente" class="badge"></span>
                </div>
                
                <p><strong>Nombre:</strong> <span id="cliente-nombre"></span></p>
                <p><strong>Domicilio:</strong> <span id="cliente-domicilio"></span></p>
                <p><strong>Teléfono:</strong> <span id="cliente-telefono"></span></p>
                
                <!-- div para información adicional de cliente frecuente -->
                <div id="cliente-frecuente-info" style="display: none;">
                    <hr>
                    <p class="text-muted"><small>Cliente registrado en el sistema</small></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>



<script>
$(document).ready(function() {
    // Inicializar modal
    const clienteModal = new bootstrap.Modal(document.getElementById('modalCliente'));

    // Manejar clic en botones .btn-folio
    $(document).on('click', '.btn-folio', function(e) {
        e.preventDefault();
        
        // Obtener datos del botón
        const pedidoId = $(this).data('pedido-id');
        const tipoCliente = $(this).data('tipo-cliente');
        const idCliente = $(this).data('id-cliente');
        const nombre = $(this).data('nombre');
        const domicilio = $(this).data('domicilio');
        const telefono = $(this).data('telefono');
        
        // Mostrar datos básicos inmediatamente
        $('#cliente-nombre').text(nombre);
        $('#cliente-domicilio').text(domicilio);
        $('#cliente-telefono').text(telefono);
        
        // Configurar tipo de cliente
        if (tipoCliente === 'frecuente') {
            $('#modal-tipo-cliente').text('👥 Cliente Frecuente').removeClass('bg-info bg-warning').addClass('bg-success');
            $('#cliente-frecuente-info').show();
            
            // Si es cliente frecuente, intentar obtener más información
            if (idCliente) {
                fetch('/pedidos/api-cliente?id=' + idCliente)
                    .then(response => response.json())
                    .then(data => {
                        if (data && !data.error) {
                            // Actualizar con datos del cliente frecuente
                            $('#cliente-nombre').text(data.razon_social || nombre);
                            $('#cliente-domicilio').text(data.domicilio || domicilio);
                            $('#cliente-telefono').text(data.telefono || telefono);
                        }
                    })
                    .catch(error => {
                        console.error('Error obteniendo datos del cliente:', error);
                    });
            }
        } else {
            $('#modal-tipo-cliente').text('👤 Cliente Ocasional').removeClass('bg-success bg-info').addClass('bg-info');
            $('#cliente-frecuente-info').hide();
        }
        
        // Mostrar modal
        clienteModal.show();

        setTimeout(() => {
            $('.modal-dialog').css('transform', 'none');
        }, 10);
    });

    // Limpieza al cerrar
    $('#modalCliente').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        
        // Limpiar campos para el próximo uso
        $('#modal-tipo-cliente').text('').removeClass('bg-success bg-warning bg-info');
        $('#cliente-nombre, #cliente-domicilio, #cliente-telefono').text('');
        $('#cliente-frecuente-info').hide();
    });

    // Función para obtener opciones permitidas según el estado actual
    function obtenerOpcionesEstado(estadoActual) {
        const opciones = [];
        
        switch(estadoActual) {
            case 'pendiente':
                opciones.push(
                    { value: 'en proceso', text: 'En proceso' },
                    { value: 'cancelado', text: 'Cancelado' }
                );
                break;
                
            case 'en proceso':
                opciones.push(
                    { value: 'finalizado', text: 'Finalizado' },
                    { value: 'cancelado', text: 'Cancelado' }
                );
                break;
                
            case 'finalizado':
            case 'cancelado':
                // No se permiten cambios desde estos estados
                break;
        }
        
        return opciones;
    }

    // Función para obtener clase CSS del badge
    function obtenerClaseBadge(estado) {
        const clases = {
            'pendiente': 'estado-pendiente',
            'en proceso': 'estado-en_proceso',
            'finalizado': 'estado-finalizado',
            'cancelado': 'estado-cancelado'
        };
        return clases[estado] || 'estado-pendiente';
    }

    function obtenerColorBadge(estado) {
        const colores = {
            'pendiente': '#4e73df',
            'en proceso': '#0b2764e1', 
            'finalizado': '#115d41',
            'cancelado': '#e74a3b'
        };
        return colores[estado] || '#ffc107';
    }

    // 2. Cambiar Estado del Pedido
    $(document).on('click', '.badge[data-pedido-id]', function() {
        const $badge = $(this);
        const pedidoId = $badge.data('pedido-id');
        const estadoActual = $badge.text().trim().toLowerCase();
        const opciones = obtenerOpcionesEstado(estadoActual);
        
        // Si no hay opciones disponibles (estados finalizado o cancelado)
        if (opciones.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Estado inmodificable',
                text: `No se puede cambiar el estado de un pedido ${estadoActual}`,
                confirmButtonText: 'Entendido'
            });
            return;
        }
        
        // Crear el HTML para las opciones
        let opcionesHTML = opciones.map(opcion => `
            <div class="status-option" data-value="${opcion.value}">
                <div class="status-badge ${obtenerClaseBadge(opcion.value)}">
                    ${opcion.text}
                </div>
                <div class="status-description">
                    ${opcion.value === 'en proceso' ? 'El pedido está siendo atendido' : 
                    opcion.value === 'finalizado' ? 'El pedido ha sido completado' : 
                    'El pedido ha sido cancelado'}
                </div>
            </div>
        `).join('');
        
        let estadoSeleccionado = opciones[0].value;
        
        Swal.fire({
            title: '<strong>Cambiar estado del pedido</strong>',
            html: `
                <div class="text-center mb-3">
                    <p style="font-size: 1.4rem; margin-bottom: 1rem;">Estado actual: 
                        <span style="padding: 0.75rem 1.1rem; border-radius: 1.7rem; font-size: 1.2rem; font-weight: 500; min-width: 80px; display: inline-block; text-align: center; background-color: ${obtenerColorBadge(estadoActual)}; color: white;">
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
            customClass: {
                popup: 'status-change-popup',
                htmlContainer: 'status-change-html'
            },
            didOpen: () => {
                document.querySelector('.status-option').classList.add('selected');
                
                document.querySelectorAll('.status-option').forEach(option => {
                    option.addEventListener('mouseenter', () => {
                        option.style.transform = 'scale(1.03)';
                        option.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
                    });
                    option.addEventListener('mouseleave', () => {
                        option.style.transform = 'scale(1)';
                        option.style.boxShadow = 'none';
                    });
                    option.addEventListener('click', () => {
                        document.querySelectorAll('.status-option').forEach(opt => {
                            opt.classList.remove('selected');
                        });
                        option.classList.add('selected');
                        estadoSeleccionado = option.getAttribute('data-value');
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
                
                // Si el estado es finalizado o cancelado, mostrar formulario de datos financieros
                if (nuevoEstado === 'finalizado' || nuevoEstado === 'cancelado') {
                    mostrarFormularioDatosFinancieros(pedidoId, nuevoEstado, $badge, estadoActual);
                } else {
                    realizarCambioEstado(pedidoId, nuevoEstado, $badge, estadoActual, {});
                }
            }
        });
    });

    // Función para mostrar formulario de datos financieros
    function mostrarFormularioDatosFinancieros(pedidoId, nuevoEstado, $badge, estadoActual) {
        Swal.fire({
            title: 'Datos Financieros',
            html: `
                <p>Por favor, ingresa los datos financieros para este pedido:</p>
                <div class="form-group">
                    <label for="gastos">Gastos:</label>
                    <input type="number" step="0.01" min="0" id="gastos" class="swal2-input" placeholder="Gastos" required>
                </div>
                <div class="form-group">
                    <label for="costo">Costo:</label>
                    <input type="number" step="0.01" min="0" id="costo" class="swal2-input" placeholder="Costo" required>
                </div>
                <div class="form-group">
                    <label for="pagados">Pagados:</label>
                    <input type="number" step="0.01" min="0" id="pagados" class="swal2-input" placeholder="Pagados" required>
                </div>
                <div class="form-group">
                    <label for="almacen">Almacén:</label>
                    <input type="number" step="0.01" min="0" id="almacen" class="swal2-input" placeholder="Almacén" value="0">
                </div>
                <div class="form-group">
                    <label for="depositos">Depósitos:</label>
                    <input type="number" step="0.01" min="0" id="depositos" class="swal2-input" placeholder="Depósitos" value="0">
                </div>
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Guardar y Cambiar Estado',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                return {
                    gastos: parseFloat(document.getElementById('gastos').value) || 0,
                    costo: parseFloat(document.getElementById('costo').value) || 0,
                    pagados: parseFloat(document.getElementById('pagados').value) || 0,
                    almacen: parseFloat(document.getElementById('almacen').value) || 0,
                    depositos: parseFloat(document.getElementById('depositos').value) || 0
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const datosFinancieros = result.value;
                realizarCambioEstado(pedidoId, nuevoEstado, $badge, estadoActual, datosFinancieros);
            }
        });
    }

    // Función para realizar el cambio de estado
    function realizarCambioEstado(pedidoId, nuevoEstado, $badge, estadoActual, datosFinancieros) {
        // Mostrar loading
        $badge.html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Preparar datos para enviar
        let datos = {
            pedido_id: pedidoId,
            nuevo_estado: nuevoEstado
        };
        
        // Agregar datos financieros si existen
        if (Object.keys(datosFinancieros).length > 0) {
            datos = {...datos, ...datosFinancieros};
        }
        
        // Enviar petición AJAX
        $.ajax({
            url: '/pedidos/cambiar-estado',
            method: 'POST',
            data: datos,
            success: function(response) {
                if (response.success) {
                    // Mapeo de estados a clases CSS
                    const estadoClasses = {
                        'pendiente': 'estado-pendiente',
                        'en proceso': 'estado-en_proceso', 
                        'finalizado': 'estado-finalizado',
                        'cancelado': 'estado-cancelado'
                    };
                    
                    // Actualizar badge
                    $badge.text(response.nuevo_estado)
                        .removeClass('estado-pendiente estado-en_proceso estado-finalizado estado-cancelado')
                        .addClass('badge ' + estadoClasses[response.nuevo_estado.toLowerCase()]);
                    
                    Swal.fire('¡Éxito!', 'Estado actualizado correctamente', 'success');
                } else {
                    throw new Error(response.error || 'Error desconocido');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.error || 'Error en la conexión', 'error');
                // Restaurar estado original
                $badge.text(estadoActual.charAt(0).toUpperCase() + estadoActual.slice(1))
                    .removeClass('estado-pendiente estado-en_proceso estado-finalizado estado-cancelado')
                    .addClass('badge ' + obtenerClaseBadge(estadoActual));
            }
        });
    }

    // 3. Funcionalidad de fila clickeable
    $(document).on('click', '.fila-clicable', function(e) {
        if ($(e.target).closest('.btn-folio, .badge, .acciones').length) {
            return;
        }
        
        const pedidoId = $(this).data('pedido-id');
        if (pedidoId) {
            window.location.href = '/pedidos/ver?id=' + pedidoId;
        }
    });

    $(document).on('click', '.modal-backdrop', function() {
        $('#modalCliente').modal('hide');
    });
});
</script>