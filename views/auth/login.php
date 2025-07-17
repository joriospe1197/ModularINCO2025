<div class="login">
    <div class="contenedor">
    <?php include_once __DIR__ .'/../templates/nombre-sitio.php'; ?>

        <div class="contenedor-sm">
            <!-- <p class="descripcion-pagina">Iniciar Sesión</p> -->

            <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

            <form class="formulario" method="POST" action="/" novalidate>
                <div class="campo">
                    <!-- <label for="email">Correo electronico</label> -->
                    <input
                        type="email"
                        id="email"
                        placeholder="Ingresa tu correo electronico."
                        name="email"
                    />
                </div>

                <div class="campo">
                <!--<label for="contrasena">Contraseña</label> -->
                    <input
                        type="password"
                        id="contrasena"
                        placeholder="Ingresa tu contraseña."
                        name="contrasena"
                    />
                </div>

                <input type="submit" class="boton" value="Iniciar Sesión">
            </form>

            <div class="acciones">
                <!--.<a href="/register">¿No tienes cuenta? obtener una</a>-->
                <a href="/forgot_my_password">¿Olvidaste tu Password?</a>
            </div>
        </div><!--.contenedor-sm -->
    </div>
</div>