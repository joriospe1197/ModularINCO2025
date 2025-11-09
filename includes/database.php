<?php

$db = mysqli_connect(
    'localhost',     // Host
    'root',                        // Usuario
    'veliz4$', // Contraseña
    'constructora_1.0'                         // Puerto personalizadogh
);

if (!$db) {
    echo "Error: No se pudo conectar a MySQL.<br>";
    echo "errno de depuración: " . mysqli_connect_errno() . "<br>";
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}
