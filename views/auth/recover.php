<div class="login">
    <div class="contenedor recover">
    <?php include_once __DIR__ .'/../templates/nombre-sitio.php'; ?>
        
        <div class="contenedor-sm">
            <p class="descripcion-pagina">Coloca tu nuevo password</p>

            <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

            <?php if($mostrar) { ?> <!-- En caso de que la variable mostrar este como true, muestra el formulario -->

            <form class="formulario" method="POST">
                <div class="campo">
                    <!-- <label for="password">Contraseña</label> -->
                    <input
                        type="password"
                        id="contrasena"
                        placeholder="Ingrese su nueva contraseña."
                        name="contrasena"
                    />
                </div>

                <input type="submit" class="boton" value="Guardar Password">
            </form>

            <?php } ?>

            <div class="recover acciones a">
                <div class="acciones">
                    <a href="/register">¿No tienes cuenta? obtener una</a>
                    <a href="/forgot_my_password">¿Olvidaste tu Password?</a>
                </div>
            </div>
        </div><!--.contenedor-sm -->
    </div>
</div>