<div class="barra">
    <div class="breadcrumbs">
        <a href="/inicio">Constructora</a> / <span><?= $titulo ?? 'Panel' ?></span>
    </div>
    <div class="user-menu">
        <span class="saludo-usuario">
            <p>Bienvenido, <strong>
                <?php 
                // Verificar si la sesión está iniciada y si la variable existe
                if (isset($_SESSION['nombre'])) {
                    echo htmlspecialchars($_SESSION['nombre']);
                } else {
                    echo "Usuario";
                }
                ?>
            </strong></p>
        </span>
        <a href="/logout" class="btn-outline-danger">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </div>
    <h2 class="nombre-pagina"><?php echo $titulo; ?></h2>
</div>