<div class="login">
    <div class="contenedor forgot_my_password">
    <?php include_once __DIR__ .'/../templates/nombre-sitio.php'; ?>
        <div class="contenedor-sm">
            <p class="descripcion-pagina">Recuperar contraseña</p>

            <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

            <form class="formulario" method="POST" action="/forgot_my_password" novalidate>
                <div class="campo">
                    <!-- <label for="email">Correo electronico</label> -->
                    <input
                        type="email"
                        id="email"
                        placeholder="Ingresa tu correo electronico."
                        name="email"
                    />
                </div>
                <input type="submit" class="boton" value="Enviar instrucciones">
            </form>

            <div class="acciones">
                <a href="/">¿Ya recordaste tu contraseña? Inicia sesión</a>
                <!--. <a href="/register">¿No tienes cuenta? obtener una</a> -->
            </div>
        </div><!--.contenedor-sm -->
    </div>
</div>