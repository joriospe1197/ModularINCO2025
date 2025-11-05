<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma JIMARSOFT | <?= $titulo ?? '' ?></title>
    <?php include 'templates/cdn-head.php'; ?>
    <link rel="stylesheet" href="/build/css/app.css">
</head>
<body>
    <?php
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        //session_start();
    }
    
    // Determinar si debemos mostrar el sidebar
    $mostrarSidebar = true;
    
    // No mostrar sidebar en páginas con simple_layout
    if (isset($simple_layout) && $simple_layout === true) {
        $mostrarSidebar = false;
    }
    
    // No mostrar sidebar si no hay sesión activa
    if (!isset($_SESSION['login'])) {
        $mostrarSidebar = false;
    }
    ?>
    
    <?php if($mostrarSidebar): ?>
        <?php include 'templates/sidebar.php'; ?>
        <?php include 'templates/barra.php'; ?>
    <?php endif; ?>
    
    <div class="dashboard">
        <div class="main-content-area">
            <div class="content-viewport">
                <?= $contenido ?>
            </div>
        </div>
    </div>    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/build/js/app.js"></script>
</body>
</html>