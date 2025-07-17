<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>


<?php include_once __DIR__ .'/../templates/alertas.php'; ?>

<!-- Mostrar el formulario solo si no se ha encontrado un usuario -->
<a href="/historial_de_pedidos" class="search_user">Regresar</a>

<div class="tabla_inferior">


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
    <p>Esta es la semana a eliminar,¿ esta seguro?</p>
    <form class="formulario" method="POST" action="/delete_week">
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
        <div class="barra">
            <a href="/historial_de_pedidos" class="search_user">Cancelar</a>
            <input type="submit" class="boton" value="Si, eliminar">
        </div>
         

    </form>
    
<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>