<?php include_once __DIR__ . '/header-dashboard.php'; ?>
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
    
    <a href="/crear_manifiesto" class="register"> Crear nuevo manifiesto</a>
    
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
    <div class="cajaManifiestos">
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Mes</th>
                    <th>Año</th>
                    <th>Total M3</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($manifiestos as $manifiesto): ?>
                    
                    <tr>
                        <td><?= $manifiesto->cliente ?></td>
                        <td><?= $meses[$manifiesto->mes] ?? $manifiesto->mes ?></td>
                        <td><?= $manifiesto->anio ?></td>
                        <td><?= $manifiesto->total_m3 ?></td>
                        <td hidden><?= $manifiesto->obra ?></td>
                        <td ><?= $manifiesto->tipo_residuo ?></td>
                        <td><a href="/vista_manifiesto?cliente=<?= urlencode($manifiesto->cliente) ?>&mes=<?= $manifiesto->mes ?>&anio=<?= $manifiesto->anio ?>&dirObra=<?= urlencode($manifiesto->obra) ?>&tipo_residuo=<?= urlencode($manifiesto->tipo_residuo) ?>">Ver manifiesto</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>


<?php include_once __DIR__ . '/footer-dashboard.php'; ?>