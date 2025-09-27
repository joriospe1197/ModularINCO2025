
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 10px;
        font-family: Arial, sans-serif;
    }

    th, td {
        border: 1px solid #ccc;
        padding: 8px 12px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #fafafa;
    }

</style>
    

    
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


<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="/crear_manifiesto" class="register"> Crear nuevo manifiesto</a>
</div>



<div class="cajaManifiestos">
    <table>
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
                <tr>
                    <td><?= htmlspecialchars($manifiesto->cliente) ?></td>
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
                    <td><?= htmlspecialchars($manifiesto->obra) ?></td>
                    <td>
                        <a href="/vista_manifiesto?cliente=<?= urlencode($manifiesto->cliente) ?>&mes=<?= $manifiesto->mes ?>&anio=<?= $manifiesto->anio ?>&dirObra=<?= urlencode($manifiesto->obra) ?>&tipo_residuo=<?= urlencode($manifiesto->tipo_residuo) ?>" 
                           class="btn btn-sm btn-primary">
                            Ver manifiesto
                        </a>
                        <button onclick="eliminarManifiesto(<?= $manifiesto->id ?>)" 
                                class="btn btn-sm btn-danger">
                            Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
</script>