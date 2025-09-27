<?php include_once __DIR__ .'/../templates/alertas.php'; ?>
<form class="formulario" method="POST" action="/generar_manifiesto">
    <label for="cliente">Cliente : </label>
    <select name="clientes" id="clientes" required>
        <option value="">Seleccionar cliente...</option>
        <?php foreach ($clientes as $cliente): ?>
            <option value="<?= $cliente->id ?>">
                <?= $cliente->razon_social ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="mes">Mes : </label>
    <select name="mes" id="mes" required>
        <option value="">Seleccionar mes...</option>
        <?php foreach ($meses as $key => $mes): ?>
            <option value="<?= $mes ?>">
                <?= $mes ?>
            </option>
        <?php endforeach; ?>        
    </select>

    <label for="anio">Año : </label>
    <select name="anio" id="anio" required>
        <option value="">Seleccionar año...</option>
        <?php $anioActual = date('Y'); ?>
        <?php for ($i = $anioActual; $i >= $anioActual - 5; $i--): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select>

    <label for="dirObra">Dirección de la obra: </label>
    <input type="text" id="dirObra" name="dirObra" required/>

    <label for="tipoResiduo">Tipo de Residuo: </label>
    <select name="tipoResiduo" id="tipoResiduo" required>
        <option value="">Seleccionar tipo de residuo...</option>
        <option value="Materiales">Materiales</option>
        <option value="Escombro">Escombro</option>
        <option value="Madera">Madera</option>
        <option value="Basura">Basura</option>
        <option value="Otro">Otro</option>
    </select>

    <br><br><br>
    <input type="submit" class="boton" value="Generar manifiesto">

    <br><br>
</form>