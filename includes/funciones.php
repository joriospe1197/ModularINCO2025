<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

// Función que revisa que el usuario este autenticado
function isAuth() : void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if(!isset($_SESSION['login'])) {
        header('Location: /');
        exit;
    }
}

// Nueva función para identificar páginas de autenticación
function esPaginaAuth() : bool {
    $paginasAuth = ['login', 'register', 'forgot-password', 'recover'];
    $paginaActual = basename($_SERVER['PHP_SELF'], '.php');
    return in_array($paginaActual, $paginasAuth);
}

// Función para renderizar vistas (mejorada)
function render($vista, $datos = []) {
    extract($datos); // Más limpio que foreach
    include __DIR__ . "/../views/{$vista}.php";
}

// Función para verificar si es admin (opcional)
function isAdmin() : void {
    if(!isset($_SESSION['admin'])) {
        header('Location: /');
    }
}