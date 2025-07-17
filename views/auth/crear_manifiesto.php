<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>

        <?php include_once __DIR__ .'/../templates/alertas.php'; ?>
        <form class="formulario" method="POST" action="/generar_manifiesto">
            <label for="cliente">Cliente : </label>

                    <select name="clientes" id="clientes">
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente->id ?>">
                                <?= $cliente->razon_social ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <script>  console.log(<?= json_encode($mensaje); ?>); </script>
            <label for="mes">Mes : </label>
                    <select name="mes" id="mes">
                        <?php foreach ($meses as $mes): ?>
                            <option value="<?= $mes ?>">
                                <?= $mes ?>
                            </option>
                        <?php endforeach; ?>        
                 </select>
            <?php $anioActual = date('Y'); ?>
            <select name="anio">
                <?php for ($i = $anioActual; $i >= $anioActual - 5; $i--): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>

            <label for="dirObra">Dirección de la obra: </label>
            <input type="text" id="dirObra" name="dirObra"/>
            <label for="tipoResiduo">Tipo de Residuo: </label>
            <input type="text" id="tipoResiduo" name="tipoResiduo" placeholder="Escombro,Leña,Madera,etc."/>
            <br><br><br>
            <input type="submit" class="boton" value="Generar manifiesto">
        

                <br><br>
        </form>
               

<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>                  