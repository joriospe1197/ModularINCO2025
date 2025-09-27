<a href="/unidades_de_transporte" class="btn-regresar"> ⬅ Regresar a la vista de unidades</a>

<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<h4>Detalles de la Unidad:</h4>

<form class="formulario" method="POST" action="/edit_unidad?idunidad=<?php echo $unidad->idunidad; ?>">

    <!-- Campo ID (Solo se mostrará como un campo de solo lectura) -->
    <input type="hidden" name="idunidad" value="<?php echo $unidad->idunidad; ?>" />

    <!-- Campo Modelo -->
    <div class="campo">
        <label for="modelo">Modelo:</label>
        <input type="text" id="modelo" name="modelo" value="<?php echo $unidad->modelo; ?>" />
    </div>

    <!-- Campo Placas -->
    <div class="campo">
        <label for="placas">Placas:</label>
        <input type="text" id="placas" name="placas" value="<?php echo $unidad->placas; ?>" />
    </div>

    <!-- Campo Chofer -->
    <div class="campo">
        <label for="chofer">Chofer:</label>
        <select id="chofer" name="chofer">
             <!-- <option value="" disabled selected>Sin seleccionar</option>  Esta opción será el valor por defecto -->
            <?php foreach ($choferes as $chofer): ?>
                <option value="<?php echo $chofer->idempleado; ?>" <?php echo $unidad->chofer == $chofer->idempleado ? 'selected' : ''; ?>>
                    <?php echo $chofer->nombre; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>


    <input type="submit" class="boton" value="Actualizar Unidad">
</form>

