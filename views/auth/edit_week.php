<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>


<?php include_once __DIR__ .'/../templates/alertas.php'; ?>

<!-- Mostrar el formulario solo si no se ha encontrado un usuario -->
<a href="/historial_de_pedidos" class="search_user">Regresar</a>

<div class="tabla_inferior">

    <h2>Semana a editar</h2>
    <p>Chofer Seleccionado : <?= $choferSeleccionado ?></p>
    <p>Semana Seleccionada : <?= $semanaSeleccionada ?></p>
    <p>Folio : <?= $primer_folio ?></p>
    <p>Folio : <?= $ultimo_folio ?></p>
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
    </table>

    <form class="formulario" method="POST" action="/update_week">
        <div class="campo">
                <input
                    type="hidden"
                    id="primer_folio_actual"
                    placeholder="Ingresa el primer folio correspondiente a esa semana."
                    name="primer_folio_actual"
                    value="<?php echo $primer_folio; ?>"
                            
                />
        </div>
        <div class="campo">
                <input
                    type="hidden"
                    id="choferSeleccionado"
                    placeholder="Chofer."
                    name="choferSeleccionado"
                    value="<?php echo $choferSeleccionado; ?>"
                            
                />
        </div>
        <div class="campo">
            <label for="primer_folio">Primer folio</label>
                <input
                    type="text"
                    id="primer_folio"
                    placeholder="Ingresa el primer folio correspondiente a esa semana."
                    name="primer_folio"
                    value="<?php echo $primer_folio; ?>"
                            
                />
        </div>
        <div class="campo">
            <label for="ultimo_folio">Ultimo folio</label>
                <input
                    type="text"
                    id="ultimo_folio"
                    placeholder="Ingresa el ultimo folio correspondiente a esa semana."
                    name="ultimo_folio"
                    value="<?php echo $ultimo_folio; ?>"
                            
            />
        </div>
        <div class="campo">
            <label for="justificacion">Justificacion</label>
                <input
                    type="number"
                    id="justificacion"
                    placeholder="Otros gastos."
                    name="justificacion"
                    value="<?php echo $justificacion; ?>"
                            
            />
        </div>
        <input type="submit" class="boton" value="Actualizar semana">       
        
    </form>

    
</div>

<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>