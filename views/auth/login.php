<div class="login">
    <div class="contenedor-login">
        <div class="logo-constructora">
            <?php include_once __DIR__ .'/../templates/nombre-sitio.php'; ?>
            
        </div>

        <div class="card-login">
            <h2 class="titulo-formulario">Inicio de sesión</h2>
            <p class="instrucciones">Ingresa tus credenciales para acceder al sistema</p>

            <?php include_once __DIR__ .'/../templates/alertas.php'; ?>

            <form class="formulario" method="POST" action="/" novalidate>
                <div class="grupo-input">
                    <label for="email" class="etiqueta">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        placeholder="usuario@---.com"
                        name="email"
                        class="input-login"
                    />
                    <i class="icono-input fas fa-envelope"></i>
                </div>

                <div class="grupo-input">
                    <label for="contrasena" class="etiqueta">Contraseña</label>
                    <input
                        type="password"
                        id="contrasena"
                        placeholder="••••••••"
                        name="contrasena"
                        class="input-login"
                    />
                    <i class="icono-input fas fa-lock"></i>
                </div>

                <div class="opciones-login">
                    <div class="recordarme">
                        <input type="checkbox" id="recordar">
                        <label for="recordar">Recordar sesión</label>
                    </div>
                    <a href="/forgot_my_password" class="enlace-olvido">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="boton-login">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>
        </div>

        <div class="pie-login">
            <p>© <?php echo date('Y'); ?> JIMARSOFT.</p>
        </div>
    </div>
</div>