<?php

$db = mysqli_connect(
    'gondola.proxy.rlwy.net',     // Host
    'root',                        // Usuario
    'hgVvISLFXzOyIALjHttvbipzQmSCPMAl', // Contraseña
    'constructora',                     // Nombre de base de datos correcto
    59369                          // Puerto personalizadogh
);

if (!$db) {
    echo "Error: No se pudo conectar a MySQL.<br>";
    echo "errno de depuración: " . mysqli_connect_errno() . "<br>";
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}
