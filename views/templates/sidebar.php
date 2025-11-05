<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no hay sesión activa, no mostrar el sidebar
if (!isset($_SESSION['login'])) {
    return;
}

// Verificación de tipo de usuario
$esAdmin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 1;
?>

<aside class="sidebar">
    <div class="sidebar-header">
        
        <h2><i class="fas fa-building"></i> JIMARSOFT </h2>
    </div>
    
    <nav class="nav-menu">
        <!-- Inicio -->
        <a href="/inicio" class="nav-link <?= $titulo === 'Inicio' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Inicio
        </a>

        <!-- Sección Productos (visible para todos) -->
        <a href="/productos" class="nav-link <?= $titulo === 'Productos' ? 'active' : '' ?>">
            <i class="fas fa-boxes"></i> Productos
        </a>

        <!-- Sección Pedidos -->
        <div class="nav-group">
            <div class="nav-group-title">Gestión de Pedidos</div>
            <a href="/pedidos" class="nav-link <?= $titulo === 'Pedidos' ? 'active' : '' ?>">
                <i class="fas fa-truck"></i> Pedidos Activos
            </a>
            <a href="/pedidos/agregar" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'agregar.php' ? 'active' : '' ?>">
                <i class="fas fa-plus-circle"></i> Nuevo Pedido
            </a>
            <a href="/historial_de_pedidos" class="nav-link <?= $titulo === 'Historial de pedidos' ? 'active' : '' ?>">
                <i class="fas fa-history"></i> Historial
            </a>

            </a>
            <a href="/clientes" class="nav-link <?= $titulo === 'Lista de Clientes' ? 'active' : '' ?>">
                <i class="fas fa-user-tie"></i> Clientes
            </a>

        </div>
        

        <!-- Sección Logística -->
        <div class="nav-group">
            <div class="nav-group-title">Gestión Logística</div>
            <a href="/unidades_de_transporte" class="nav-link <?= $titulo === 'Unidades de transporte' ? 'active' : '' ?>">
                <i class="fas fa-bus"></i> Unidades
            </a>
            <a href="/servicios_de_unidades" class="nav-link <?= $titulo === 'Servicios de unidades' ? 'active' : '' ?>">
                <i class="fas fa-gas-pump"></i> Servicios de unidades
            </a>
            <a href="/manifiestos" class="nav-link <?= $titulo === 'Manifiestos' ? 'active' : '' ?>">
                <i class="fas fa-clipboard-list"></i> Manifiestos
            </a>
        </div>

        <!-- Sección Comunicación -->
        <div class="nav-group">
            <div class="nav-group-title">Comunicación</div>
            <a href="/chat" class="nav-link <?= $titulo === 'Chat' ? 'active' : '' ?>">
                <i class="fas fa-comments"></i> Chat Online
            </a>
        </div>

        <!-- Sección Administración (solo para admin) -->
        <?php if ($esAdmin): ?>
        <div class="nav-group admin-section">
            <div class="nav-group-title">Administración</div>
            <a href="/empleados" class="nav-link <?= $titulo === 'Empleados' ? 'active' : '' ?>">
                <i class="fas fa-users-cog"></i> Empleados
            </a>
        </div>
        <?php endif; ?>

        <!-- Perfil 
        <div class="sidebar-footer">
            <a href="/mi_perfil" class="nav-link <?= $titulo === 'Mi perfil' ? 'active' : '' ?>">
                <i class="fas fa-user"></i> Mi Perfil
            </a>
        </div>
        -->
    </nav>
</aside>


<style>
/* Estilos mejorados */
.admin-section {
    margin-top: 15px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 10px;
}

</style>