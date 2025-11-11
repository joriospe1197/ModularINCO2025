<?php

$meses = [
    '01' => 'Enero',
    '02' => 'Febrero',
    '03' => 'Marzo',
    '04' => 'Abril',
    '05' => 'Mayo',
    '06' => 'Junio',
    '07' => 'Julio',
    '08' => 'Agosto',
    '09' => 'Septiembre',
    '10' => 'Octubre',
    '11' => 'Noviembre',
    '12' => 'Diciembre'
];
?>

<div class="contenedor-pedidos">
    


    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="card-title mb-0"><i class="fas fa-clipboard-list"></i> Lista de Manifiestos</h2>
            <a href="/crear_manifiesto" class="btn btn-primary">
                <i class="fas fa-plus"></i> Crear nuevo manifiesto
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="tabla-pedidos">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Mes</th>
                            <th>Año</th>
                            <th>Tipo Residuo</th>
                            <th>Total M3</th>
                            <th>Obra</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($manifiestos as $manifiesto): ?>
                           
                            <tr class="fila-clicable" data-cliente_id = "<?= $manifiesto->id_cliente ?>"
                            data-cliente = "<?= $manifiesto->cliente ?>"
                            data-mes = "<?=$manifiesto->mes?>"
                            data-anio ="<?=$manifiesto->anio?>"
                            data-obra = "<?=urlencode($manifiesto->obra)?>"
                            data-tipo_residuo = "<?=$manifiesto->tipo_residuo?>"
                            data-totalm3 = "<?=$manifiesto->total_m3?>">
                                <td class="text-wrap" style="max-width: 200px;"><?= htmlspecialchars($manifiesto->cliente) ?></td>
                                <td>
                                    <?php
                                    if (!empty($manifiesto->mes)) {
                                        // Si el mes es un número (01, 02, etc.)
                                        if (is_numeric($manifiesto->mes)) {
                                            echo $meses[$manifiesto->mes] ?? 'Mes ' . $manifiesto->mes;
                                        } else {
                                            // Si ya es un nombre de mes
                                            echo $manifiesto->mes;
                                        }
                                    } else {
                                        echo 'No especificado';
                                    }
                                    ?>
                                </td>
                                <td><?= $manifiesto->anio ?></td>
                                <td><?= htmlspecialchars($manifiesto->tipo_residuo) ?></td>
                                <td>
                                    <?= ($manifiesto->total_m3 > 0) ? $manifiesto->total_m3 . ' m³' : '0 m³' ?>
                                </td>
                                <td class="text-wrap" style="max-width: 200px;"><?= htmlspecialchars($manifiesto->obra) ?></td>
                                <td onclick="event.stopPropagation()">
                                    <a href="/vista_manifiesto_guardado?id_cliente=<?= $manifiesto->id_cliente ?> &cliente=<?= $manifiesto->cliente ?>&mes=<?= $manifiesto->mes ?>&anio=<?= $manifiesto->anio ?>&dirObra=<?= urlencode($manifiesto->obra) ?>&tipo_residuo=<?= urlencode($manifiesto->tipo_residuo) ?>&totalm3=<?= urlencode($manifiesto->total_m3) ?>"
                                        <i class="fas fa-eye"></i>  Ver
                                    </a>
                                    <button onclick="eliminarManifiesto(<?= $manifiesto->id ?>)"
                                        class="btn btn-outline-danger" style="font-size: 1.4rem; height: 30px; padding: 0.75rem 1.5rem;">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<script>
    function eliminarManifiesto(id) {
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

                fetch('/manifiestos/eliminar', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Eliminado!', 'El manifiesto ha sido eliminado.', 'success')
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
    $(document).on('click', '.fila-clicable', function(e) {
        if ($(e.target).closest('.btn-folio, .badge, .acciones').length) {
            return;
        }
        const id_cliente = $(this).data('cliente_id');
        const cliente = $(this).data('cliente');
        const mes = $(this).data('mes');
        const anio = $(this).data('anio');
        const obra = $(this).data('obra');
        const tipo_residuo = $(this).data('tipo_residuo');
        const total_m3 = $(this).data('total_m3');
        if (cliente) {
            window.location.href = '/vista_manifiesto_guardado?id_cliente=' + id_cliente + '&cliente=' + cliente +'&mes=' + mes + '&anio=' + anio + '&dirObra=' + obra + '&tipo_residuo=' + tipo_residuo + '&totalm3=' + total_m3;
        }
    });

    $(document).on('click', '.modal-backdrop', function() {
        $('#modalCliente').modal('hide');
    });
</script>
