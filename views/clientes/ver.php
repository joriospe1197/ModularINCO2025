<a href="/clientes" class="btn-regresar">⬅ Regresar</a>

<div class="contenedor-pedido-constructora">
    <div class="card-formulario-pedido">
        <div class="card-body">
            <div class="seccion-formulario">
                <h2><i class="fas fa-info-circle"></i> Información del Cliente</h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Razon Social</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($cliente->razon_social) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Domicilio</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($cliente->domicilio ?? 'Sin asignar') ?></div>
                        </div>
                    </div>
                </div>
               <div class="row">
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Correo Electronico</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($cliente->correo_electronico) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Telefono</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($cliente->telefono) ?></div>
                        </div>
                    </div>
               </div>     
                    
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Municipio</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($cliente->municipio) ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Estado</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($cliente->estado) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="campo-formulario">
                            <label><strong>Codigo Postal</strong></label>
                            <div class="valor-campo"><?= htmlspecialchars($cliente->codigo_postal) ?></div>
                        </div>
                    </div>
                    
                </div>
            </div>

            
            <div class="acciones-formulario">
                <div class="btn">
                    <a href="/clientes/editar?id=<?= $cliente->id ?>" class="btn-warning">
                        <i class="fas fa-edit me-1"></i> Editar Cliente
                    </a>


                    <?php if ($_SESSION['tipo_usuario'] == 1): ?>
                    <button type="button" class="btn btn-danger" 
                            onclick="eliminarCliente(<?= $cliente->id ?>)">
                        <i class="fas fa-trash me-1"></i> Eliminar Cliente
                    </button>
                    <?php endif; ?>
                </div>
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


function eliminarCliente(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            //  CAMBIAR LA URL Y USAR FormData
            const formData = new FormData();
            formData.append('id', id);
            
            fetch('/clientes/eliminar', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Eliminado!', 'El cliente ha sido eliminado.', 'success')
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Error de conexión: ' + error, 'error');
            });
        }
    });
}

</script>