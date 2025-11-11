<?php include_once __DIR__ . '/../templates/alertas.php'; ?>
<a href="/manifiestos" class="btn-regresar">⬅ Regresar</a>

<div class="contenedor-pedido-constructora">
    <div class="card-formulario-pedido">
        <div class="card-body">


            <form method="POST" class="formulario" id="form-cliente" action="/generar_manifiesto">
                <div class="seccion-formulario">
                    <h2><i class="fas fa-info-circle"></i> Datos para la creación del manifiesto</h2>

                    <div class="campos-cliente">
                        <div class="campo-cliente-ocasional">
                            <div class="campos-cliente-ocasional">
                                <div class="campo-formulario">
                                    <label for="cliente">Cliente : </label>
                                    <select name="clientes" id="clientes" required onchange="updateHiddenField()">
                                        <option value="">Seleccionar cliente...</option>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <option value="<?= $cliente->id ?>" data-razon="<?= htmlspecialchars($cliente->razon_social) ?>">
                                                <?= $cliente->razon_social ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="razon_social" id="razon_social">
                                </div>

                                <div class="campo-formulario">
                                    <label for="mes">Mes : </label>
                                    <select name="mes" id="mes" required>
                                        <option value="">Seleccionar mes...</option>
                                        <?php foreach ($meses as $key => $mes): ?>
                                            <option value="<?= $mes ?>">
                                                <?= $mes ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="campo-formulario">
                                    <label for="anio">Año : </label>
                                    <select name="anio" id="anio" required>
                                        <option value="">Seleccionar año...</option>
                                        <?php $anioActual = date('Y'); ?>
                                        <?php for ($i = $anioActual; $i >= $anioActual - 5; $i--): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="campo-formulario">
                                    <label for="dirObra">Dirección de la obra: </label>
                                    <input type="text" id="dirObra" name="dirObra" required />
                                </div>
                                <div class="campo-formulario">
                                    <label for="tipoResiduo">Tipo de Residuo: </label>
                                    <select name="tipoResiduo" id="tipoResiduo" required>
                                        <option value="">Seleccionar tipo de residuo...</option>
                                        <option value="Materiales">Materiales</option>
                                        <option value="Escombro">Escombro</option>
                                        <option value="Madera">Madera</option>
                                        <option value="Basura">Basura</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="acciones-formulario">
                    <div class="btn">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Crear manifiesto
                        </button>
                        <button type="reset" class="btn btn-secondary ms-2">
                            <i class="fas fa-undo mr-1"></i> Limpiar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function updateHiddenField() {
        const select = document.getElementById('clientes');
        const selectedOption = select.options[select.selectedIndex];
        document.getElementById('razon_social').value = selectedOption.getAttribute('data-razon') || '';
    }
</script>