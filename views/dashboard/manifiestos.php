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
    <div class="barra">
            <a href="/crear_manifiesto" class="register"> Crear nuevo manifiesto</a>
    </div>
    
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
                        <td><?= $manifiesto->mes ?></td>
                        <td><?= $manifiesto->anio ?></td>
                        <td><?= $manifiesto->total_m3 ?></td>
                        <td><a href="/ver_manifiesto">Ver manifiesto</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>


<?php include_once __DIR__ . '/footer-dashboard.php'; ?>