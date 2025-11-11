<div class="contenedor-clientes">
    <!--alertas de eliminado -->
    <?php if (!empty($_SESSION['alerta'])): ?>
        <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
            <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
        </div>
        <?php unset($_SESSION['alerta']); ?>
    <?php endif; ?>
    <div class="contenedor-pedidos">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="card-title mb-0"><i class="fas fa-user-tie"></i> Lista de Clientes</h2>
                <a href="/clientes/agregar" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Registrar Cliente
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($clientes)) : ?>
                    <div class="alert alert-info">No hay clientes registrados</div>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="tabla-pedidos">
                            <thead>
                                <tr>
                                    <th>Razon Social</th>
                                    <th>Correo Electronico</th>
                                    <th>Telefono</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <tr class="fila-clicable" data-cliente-id="<?= $cliente->id ?>">

                                        <td class="text-wrap" style="max-width: 200px;"><?= htmlspecialchars($cliente->razon_social) ?></td>
                                        <td class="text-wrap" style="max-width: 200px;"><?= htmlspecialchars($cliente->correo_electronico ?? 'Sin asignar') ?></td>
                                        <td><?= htmlspecialchars($cliente->telefono ?? 'Sin asignar') ?></td>

                                        <td>
                                            <a href="/clientes/ver?id=<?= $cliente->id ?>" class="btn btn-sm btn-primary">
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
</div>

<script>
    // 3. Funcionalidad de fila clickeable
    $(document).on('click', '.fila-clicable', function(e) {
        if ($(e.target).closest('.btn-folio, .badge, .acciones').length) {
            return;
        }

        const clienteId = $(this).data('cliente-id');
        if (clienteId) {
            window.location.href = '/clientes/ver?id=' + clienteId;
        }
    });

    $(document).on('click', '.modal-backdrop', function() {
        $('#modalCliente').modal('hide');
    });
</script>