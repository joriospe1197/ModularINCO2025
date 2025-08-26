
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
    .totales {
        max-width: 1000px;
        margin: 20px auto;
        font-family: Arial, sans-serif;
        font-size: 15px;
        color: #333;
    }

    .totales p {
        margin: 5px 0;
    }
    </style>
    <div class="barra">
            <a href="/create_weekly_history" class="register">Agregar registro semanal</a>
    </div>
    
    <div class="tabla_semanal">
    <form method="GET" class="folios">
        <label for="chofer">Chofer:</label>
                    <select name="chofer" id="chofer" onchange="this.form.submit()">
                        <?php foreach ($choferes as $chofer): ?>
                            <option value="<?= $chofer->idempleado ?>" <?= $chofer->idempleado == $choferSeleccionado ? 'selected' : '' ?>>
                                 <?= $chofer->nombre ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
        <label for="semana">Semana:</label>
        <select name="semana" id="semana" onchange="this.form.submit()">
            <?php foreach ($semanas as $index => $semana): ?>
                <option value="<?= $index ?>" <?= $index == $semanaSeleccionada ? 'selected' : '' ?>>
                    Folios <?= $semana->primer_folio ?> - <?= $semana->ultimo_folio ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
            // Obtener la semana seleccionada correctamente
            $semanaElegida = $semanas[$semanaSeleccionada] ?? null;
        ?>
        <?php if ($semanaElegida): ?>
            <div class="barra">
                <a href="/edit_week?chofer=<?= $choferSeleccionado ?>&semana=<?= $semanaSeleccionada ?>&primer_folio=<?= $semanaElegida->primer_folio ?>&ultimo_folio=<?= $semanaElegida->ultimo_folio ?>" class="search_user">
                    Editar registro semanal
                </a>
                <a href="/remove_week?chofer=<?= $choferSeleccionado ?>&semana=<?= $semanaSeleccionada ?>&primer_folio=<?= $semanaElegida->primer_folio ?>&ultimo_folio=<?= $semanaElegida->ultimo_folio ?>" class="remove_user">
                    Eliminar registro semanal
                </a>
            </div>
            
        <?php endif; ?>   
        
    </form>
    <br><br><br>
    <div class="tabla_inferior">
        <table>
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Servicio</th>
                    <th>Gastos</th>
                    <th>Costo</th>
                    <th>Pagados</th>
                    <th>Almacén</th>
                    <th>Depósitos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $pedido): ?>
                    <tr>
                        <td><?= $pedido->folio ?></td>
                        <td><?= $pedido->fecha?></td>
                        <td><?= $pedido->cliente ?></td>
                        <td><?= $pedido->servicio ?></td>
                        <td> $ <?= $pedido->gastos ?></td>
                        <td> $ <?= $pedido->costo ?></td>
                        <td> $ <?= $pedido->pagados ?></td> 
                        <td> $ <?= $pedido->almacen ?></td>    
                        <td> $ <?= $pedido->depositos ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            
            <tfoot>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td>Total</td>
                    <td> $ <?= $totalGastos ?> </td>
                    <td> $ <?= $totalCosto ?></td>
                    <td> $ <?= $totalPagados ?> </td>
                    <td> $ <?= $totalAlmacen ?> </td>
                    <td> $ <?= $totalDepositos ?></td>
                    
                </tr>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td>Total Gastos:</td>
                    <td>$ <?= $totalGastos ?></td>
                    
                </tr>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td>Total Pagados:</td>
                    <td>$ <?= $totalPagados ?></td>    
                </tr>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td>Total Depositos:</td>
                    <td>$ <?= $totalDepositos ?></td>    
                </tr>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td>Total Almacen:</td>
                    <td>$ <?= $totalAlmacen ?></td>    
                </tr>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td>Justificacion:</td>
                    <td>$ <?= $justificacion ?></td>    
                </tr>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td>Saldo anterior:</td>
                    <td>$ <?= $saldo_anterior_1 ?></td>    
                </tr>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td>Saldo actual:</td>
                    <td>$ <?= $saldo ?></td>    
                </tr>
            </tfoot>
        </table>

        
        
    </div>
    </div>


<?php include_once __DIR__ . '/footer-dashboard.php'; ?>