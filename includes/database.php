<?php

$db = mysqli_connect('localhost', 'root', 'veliz4$', 'Constructora');

if (!$db) {
    echo "Error al conectar a la base de datos";
    exit;
}
