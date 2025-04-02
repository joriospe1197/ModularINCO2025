<aside class="sidebar">
    <h2>Constructora</h2>

    <nav class="sidebar-nav">
        <a class="<?php echo ($titulo === 'Inicio') ? 'activo' : ''; ?>" href="/inicio">Inicio</a>
        <!-- Verificamos si el tipo de usuario es 1, para mostrar el enlace de "Empleados" -->
        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 1): ?>
            <a class="<?php echo ($titulo === 'Empleados') ? 'activo' : ''; ?>" href="/empleados">Empleados</a>
        <?php endif; ?>
        <a class="<?php echo ($titulo === 'Productos') ? 'activo' : ''; ?>" href="/productos">Productos</a>
        <a class="<?php echo ($titulo === 'Pedidos') ? 'activo' : ''; ?>" href="/pedidos">Pedidos</a>
        <a class="<?php echo ($titulo === 'Historial de pedidos') ? 'activo' : ''; ?>" href="/historial_de_pedidos">Historial de pedidos</a>
        <a class="<?php echo ($titulo === 'Choferes') ? 'activo' : ''; ?>" href="/choferes">Choferes</a>
        <a class="<?php echo ($titulo === 'Servicios de unidades') ? 'activo' : ''; ?>" href="/servicios_de_unidades">Servicios de unidades</a>
        <a class="<?php echo ($titulo === 'Rastreo de unidades') ? 'activo' : ''; ?>" href="/rastreo_de_unidades">Rastreo de unidades</a>
        <a class="<?php echo ($titulo === 'Manifiestos') ? 'activo' : ''; ?>" href="/manifiestos">Manifiestos</a>
        <a class="<?php echo ($titulo === 'Chat') ? 'activo' : ''; ?>" href="/chat">Chat</a>
    </nav>
</aside>
