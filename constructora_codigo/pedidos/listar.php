<?php
include('../includes/conexion.php');
include('../includes/auth.php');
verificarSesion();

$titulo = "Lista de Pedidos";

// Consulta para obtener pedidos
$sql = "
    SELECT p.id, p.codigo_pedido, 
           e_reg.nombre AS empleado_registra,
           e_chofer.nombre AS chofer,
           p.fecha_pedido, p.estado
    FROM pedidos p
    JOIN empleados e_reg ON p.id_empleado_registra = e_reg.idempleado
    LEFT JOIN empleados e_chofer ON p.id_empleado_chofer = e_chofer.idempleado
    ORDER BY p.fecha_pedido DESC
";

$pedidos = $conexion->query($sql);

// Procesar cambio de estado si se envió por AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado'])) {
    header('Content-Type: application/json');
    
    try {
        $pedido_id = (int)$_POST['pedido_id'];
        $nuevo_estado = $conexion->real_escape_string($_POST['nuevo_estado']); // Corregí el typo aquí (de 'nuevo_estado' a 'nuevo_estado')
        
        // Validar estado
        $estados_permitidos = ['pendiente', 'en proceso', 'finalizado', 'cancelado'];
        if (!in_array($nuevo_estado, $estados_permitidos)) {
            throw new Exception("Estado no válido");
        }
        
        $stmt = $conexion->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_estado, $pedido_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar estado");
        }
        
        echo json_encode([
            'success' => true,
            'nuevo_estado' => $nuevo_estado,
            'clase_badge' => obtenerClaseBadge($nuevo_estado)
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// Función auxiliar para clases de badges
function obtenerClaseBadge($estado) {
    switch (strtolower($estado)) {
        case 'pendiente': return 'bg-warning';
        case 'en proceso': return 'bg-info';
        case 'finalizado': return 'bg-success';
        case 'cancelado': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

// Función auxiliar para descripciones de estado
function obtenerDescripcionEstado($estado) {
    $descripciones = [
        'pendiente' => 'El pedido está esperando ser procesado',
        'en proceso' => 'El pedido está siendo atendido',
        'finalizado' => 'El pedido ha sido completado satisfactoriamente',
        'cancelado' => 'El pedido ha sido cancelado'
    ];
    return $descripciones[strtolower($estado)] ?? '';
}

ob_start();
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title mb-0"><i class="fas fa-list"></i> Lista de Pedidos</h2>
        <a href="agregar.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Pedido
        </a>
    </div>
    <div class="card-body">
        <?php if ($pedidos->num_rows === 0): ?>
            <div class="alert alert-info">No hay pedidos registrados</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Registrado por</th>
                            <th>Chofer</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($pedido = $pedidos->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($pedido['codigo_pedido']) ?></td>
                            <td><?= htmlspecialchars($pedido['empleado_registra']) ?></td>
                            <td><?= htmlspecialchars($pedido['chofer'] ?? 'Sin asignar') ?></td>
                            <td><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></td>
                            <td>
                                <span class="badge <?= obtenerClaseBadge($pedido['estado']) ?> estado-pedido" 
                                      style="cursor: pointer;"
                                      data-pedido-id="<?= $pedido['id'] ?>">
                                    <?= ucfirst($pedido['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="ver.php?id=<?= $pedido['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Variable global para almacenar el estado seleccionado
let estadoSeleccionado = '';

// Función para obtener la clase del badge según el estado
function obtenerClaseBadge(estado) {
    switch(estado) {
        case 'pendiente': return 'bg-warning';
        case 'en proceso': return 'bg-info';
        case 'finalizado': return 'bg-success';
        case 'cancelado': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

// Función para obtener las opciones de estado disponibles
function obtenerOpcionesEstado(estadoActual) {
    const opciones = {
        'pendiente': [
            { value: 'en proceso', text: 'En proceso' },
            { value: 'cancelado', text: 'Cancelado' }
        ],
        'en proceso': [
            { value: 'finalizado', text: 'Finalizado' },
            { value: 'cancelado', text: 'Cancelado' }
        ],
        'finalizado': [],
        'cancelado': []
    };
    return opciones[estadoActual] || [];
}

$(document).ready(function() {
    // Manejar clic en el badge de estado
    $(document).on('click', '.estado-pedido', function() {
        const $badge = $(this);
        const pedidoId = $badge.data('pedido-id');
        const estadoActual = $badge.text().trim().toLowerCase();
        const opciones = obtenerOpcionesEstado(estadoActual);
        
        if (opciones.length === 0) {
            Swal.fire('Información', 'Este pedido no puede cambiar de estado', 'info');
            return;
        }
        
        // Crear el HTML para las opciones
        let opcionesHTML = opciones.map(opcion => `
            <div class="status-option" 
                 data-value="${opcion.value}"
                 onclick="estadoSeleccionado = '${opcion.value}'">
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
        
        Swal.fire({
            title: '<strong>Cambiar estado del pedido</strong>',
            html: `
                <div class="text-center mb-3">
                    <p>Estado actual: <span class="badge ${obtenerClaseBadge(estadoActual)}">${estadoActual.charAt(0).toUpperCase() + estadoActual.slice(1)}</span></p>
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
                estadoSeleccionado = opciones[0].value;
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
                    });
                });
            },
            preConfirm: () => {
                if (!estadoSeleccionado) {
                    Swal.showValidationMessage('Por favor selecciona un estado');
                    return false;
                }
                return true;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                const originalText = $badge.text();
                $badge.html('<i class="fas fa-spinner fa-spin"></i>');
                
                // Enviar petición AJAX
                $.ajax({
                    url: 'listar.php',
                    method: 'POST',
                    data: {
                        cambiar_estado: true,
                        pedido_id: pedidoId,
                        nuevo_estado: estadoSeleccionado
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $badge.text(response.nuevo_estado.charAt(0).toUpperCase() + 
                                       response.nuevo_estado.slice(1));
                            $badge.removeClass('bg-warning bg-info bg-success bg-danger')
                                   .addClass(response.clase_badge);
                            Swal.fire('Éxito', 'Estado actualizado correctamente', 'success');
                        } else {
                            Swal.fire('Error', response.error, 'error');
                            $badge.text(originalText);
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error de conexión', 'error');
                        $badge.text(originalText);
                    }
                });
            }
        });
    });
});
</script>

<style>
.status-change-popup {
    max-width: 500px;
    border-radius: 10px;
}

.status-options-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 15px;
}

.status-option {
    padding: 15px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    border: 1px solid #eee;
}

.status-option:hover {
    background: #f9f9f9;
}

.status-option.selected {
    border: 2px solid #4e73df;
    background-color: #f8f9fc;
}

.status-badge {
    padding: 8px 12px;
    border-radius: 20px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 8px;
    font-size: 14px;
}

.status-description {
    font-size: 12px;
    color: #666;
    text-align: center;
}

.bg-warning { background-color: #ffc107; color: #000; }
.bg-info { background-color: #17a2b8; color: #fff; }
.bg-success { background-color: #28a745; color: #fff; }
.bg-danger { background-color: #dc3545; color: #fff; }
.bg-secondary { background-color: #6c757d; color: #fff; }
</style>

<?php
$contenido = ob_get_clean();
include('../includes/layout.php');
?>