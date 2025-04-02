<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\LoginController;
use Controllers\UserController;
use MVC\Router;
use Controllers\DashboardController;
$router = new Router();

// Crear Cuenta
$router->get('/register',[UserController::class, 'register']);
$router->post('/register',[UserController::class, 'register']);

// Editar usuario
$router->get('/search_user',[UserController::class, 'search_user']);
$router->post('/search_user',[UserController::class, 'search_user']);
$router->get('/edit_user',[UserController::class, 'edit_user']);
$router->post('/edit_user',[UserController::class, 'edit_user']);
$router->get('/message_update_user',[UserController::class, 'message_update_user']);
$router->post('/message_update_user',[UserController::class, 'message_update_user']);

//Eliminar usuario
$router->get('/remove_user',[UserController::class, 'remove_user']);
$router->post('/remove_user',[UserController::class, 'remove_user']);


// Confirmación de cuenta
$router->get('/message',[UserController::class, 'message']);
$router->get('/confirm',[UserController::class, 'confirm']);

//Iniciar sesion
$router->get('/',[LoginController::class, 'login']);
$router->post('/',[LoginController::class, 'login']);

//Cerrar sesion
$router->get('/logout',[LoginController::class, 'logout']);

//Recuperar Password
$router->get('/forgot_my_password',[LoginController::class, 'forgot_my_password']);
$router->post('/forgot_my_password',[LoginController::class, 'forgot_my_password']);

//Colocar el nuevo password
$router->get('/recover',[LoginController::class, 'recover']);
$router->post('/recover',[LoginController::class, 'recover']);

//Dashboard
$router->get('/mi_perfil',[DashboardController::class, 'mi_perfil']);
$router->get('/inicio',[DashboardController::class, 'inicio']);
$router->get('/empleados',[DashboardController::class, 'empleados']);
$router->get('/productos',[DashboardController::class, 'productos']);
$router->get('/pedidos',[DashboardController::class, 'pedidos']);
$router->get('/historial_de_pedidos',[DashboardController::class, 'historial_de_pedidos']);
$router->get('/choferes',[DashboardController::class, 'choferes']);
$router->get('/servicios_de_unidades',[DashboardController::class, 'servicios_de_unidades']);
$router->get('/rastreo_de_unidades',[DashboardController::class, 'rastreo_de_unidades']);
$router->get('/manifiestos',[DashboardController::class, 'manifiestos']);
$router->get('/chat',[DashboardController::class, 'chat']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();