<?php

$db = mysqli_connect('localhost', 'root', 'veliz4$', 'Constructora');

if (!$db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_error();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}
