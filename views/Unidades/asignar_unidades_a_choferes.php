<?php include_once __DIR__ . '/../dashboard/header-dashboard.php'; ?>

    <div class="barra">
        <a href="/unidades_de_transporte" class="register">Regresar a la vista de unidades</a>
    </div>

    <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

    <form class="formulario" method="POST" action="/asignar_unidades_a_choferes">
        <div class="campo">
            <label for="idempleado">Chofer</label>
            <select id="idempleado" name="chofer">
                <option value="">Sin seleccionar</option>
                <?php foreach ($choferes as $chofer) : ?>
                    <option value="<?php echo $chofer->idempleado; ?>" 
                        <?php echo (isset($usuario) && $usuario->chofer == $chofer->idempleado) ? 'selected' : ''; ?>>
                        <?php echo $chofer->nombre; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label for="modelo">Modelo</label>
            <input type="text" id="modelo" placeholder="Ingrese el modelo de la unidad." name="modelo" />
        </div>
        <div class="campo">
            <label for="placas">Placas</label>
            <input type="text" id="placas" placeholder="Ingrese las placas de la unidad." name="placas" />
        </div>

        <input type="submit" class="boton" value="Asignar unidad">
    </form>


<?php include_once __DIR__ . '/../dashboard/footer-dashboard.php'; ?>
