<a href="/manifiestos" class="btn-regresar">⬅ Regresar</a>
<?php if (!empty($_SESSION['alerta'])): ?>
    <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
        <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
    </div>
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>

<div class="contenedor-pedido-constructora">
    <div class="card-formulario-pedido">
        <div class="card-body">

            <?php if (!empty($alertas['error'])): ?>
                <div class="alerta error mb-4">
                    <?php foreach ($alertas['error'] as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="formulario-cliente" id="form-cliente">
                <div class="seccion-formulario">
                    <h2><i class="fas fa-info-circle"></i> Datos del Cliente</h2>
                
                        <div class="campos-cliente">
                            <div class="campo-cliente-ocasional">
                                <div class="campos-cliente-ocasional">
                                    <div class="campo-formulario">
                                        <label for="nombre_cliente">Nombre del Cliente o Razon Social *</label>
                                        <input type="text" name="nombre_cliente" id="nombre_cliente"
                                               value="<?php echo htmlspecialchars($cliente->razon_social ?? ''); ?>">
                                    </div>
                                    
                                    <div class="campo-formulario">
                                        <label for="domicilio_cliente">Domicilio *</label>
                                        <input type="text" name="domicilio_cliente" id="domicilio_cliente"
                                               value="<?php echo htmlspecialchars($pedido->domicilio ?? ''); ?>">
                                    </div>
                                    <div class="campo-formulario">
                                        <label for="estado">Estado *</label>
                                        <input type="text" name="estado" id="estado"
                                               value="<?php echo htmlspecialchars($pedido->estado ?? ''); ?>">
                                    </div>
                                    <div class="campo-formulario">
                                        <label for="municipio">Municipio *</label>
                                        <input type="text" name="municipio" id="municipio"
                                               value="<?php echo htmlspecialchars($pedido->municipio ?? ''); ?>">
                                    </div>
                                    <div class="campo-formulario">
                                        <label for="codigo_postal">Codigo Postal *</label>
                                        <input type="text" name="codigo_postal" id="codigo_postal"
                                               value="<?php echo htmlspecialchars($pedido->codigo_postal ?? ''); ?>">
                                    </div>
                                    
                                    <div class="campo-formulario">
                                        <label for="telefono_cliente">Teléfono *</label>
                                        <input type="tel" name="telefono_cliente" id="telefono_cliente"
                                               value="<?php echo htmlspecialchars($pedido->telefono ?? ''); ?>">
                                    </div>
                                    <div class="campo-formulario">
                                        <label for="correo_electronico">Correo Electronico *</label>
                                        <input type="email" name="correo_electronico" id="correo_electronico"
                                               value="<?php echo htmlspecialchars($cliente->correo_electronico ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                </div>
                
                <div class="acciones-formulario">
                    <div class="btn">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Registrar Cliente
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
document.addEventListener('DOMContentLoaded', function() {
    

});
</script>