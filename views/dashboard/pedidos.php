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
                <p><strong>Nombre:</strong> <span id="cliente-nombre"></span></p>
                <p><strong>Domicilio:</strong> <span id="cliente-domicilio"></span></p>
                <p><strong>Teléfono:</strong> <span id="cliente-telefono"></span></p>
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
        const nombre = $(this).data('nombre');
        const domicilio = $(this).data('domicilio');
        const telefono = $(this).data('telefono');
        
        // Actualizar contenido del modal
        $('#cliente-nombre').text(nombre);
        $('#cliente-domicilio').text(domicilio);
        $('#cliente-telefono').text(telefono);
        
        // Mostrar modal
        
        clienteModal.show();

        // Ajustar posición después de mostrarse
        setTimeout(() => {
            $('.modal-dialog').css('transform', 'none');
        }, 10);
        
    });

     // Limpieza al cerrar
    $('#modalCliente').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
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
                // Seleccionar la primera opción por defecto
                document.querySelector('.status-option').classList.add('selected');
                
                // Agregar efecto hover y selección a las opciones
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
                
                // Mostrar loading
                $badge.html('<i class="fas fa-spinner fa-spin"></i>');
                
                // Mapeo de estados a clases CSS
                const estadoClasses = {
                    'pendiente': 'estado-pendiente',
                    'en proceso': 'estado-en_proceso', 
                    'finalizado': 'estado-finalizado',
                    'cancelado': 'estado-cancelado'
                };
                
                // Enviar petición AJAX
                $.ajax({
                    url: '/pedidos/cambiar-estado',
                    method: 'POST',
                    data: { 
                        pedido_id: pedidoId,
                        nuevo_estado: nuevoEstado 
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remover todas las clases de estado y agregar la nueva
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
                            .addClass('badge ' + estadoClasses[estadoActual]);
                    }
                });
            }
        });
    });

    // 3. Funcionalidad de fila clickeable
    $(document).on('click', '.fila-clicable', function(e) {
        // Prevenir que se dispare si el clic fue en:
        // - El botón de folio (que abre el modal)
        // - El badge de estado (que cambia estado)
        // - Cualquier elemento dentro de la celda de acciones
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