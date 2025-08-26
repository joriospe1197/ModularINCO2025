<?php 

require_once __DIR__ . '/../includes/app.php';


use Controllers\WeeklyController;
use Controllers\LoginController;
use Controllers\UserController;
use MVC\Router;
use Controllers\DashboardController;
use Controllers\ManifiestosController;
use Model\ManifiestosRecord;
use Controllers\UnidadController;
use Controllers\ProductsController;
use Controllers\PedidosController;

$router = new Router();
//Generar pdf manifiesto
$router->get('/generar_PDF',[ManifiestosController::class, 'generar_PDF']);
$router->post('/generar_PDF',[ManifiestosController::class, 'generar_PDF']);

//Generar manifiesto
$router->get('/generar_manifiesto',[ManifiestosController::class,'generar_manifiesto']);
$router->post('/generar_manifiesto',[ManifiestosController::class,'generar_manifiesto']);
//Manifiestos
$router->get('/crear_manifiesto',[ManifiestosController::class, 'crear_manifiesto']);
$router->post('/crear_manifiesto',[ManifiestosController::class, 'crear_manifiesto']);
// Crear Registro Semanal
$router->get('/create_weekly_history',[WeeklyController::class, 'create_weekly_history']);
$router->post('/create_weekly_history',[WeeklyController::class, 'create_weekly_history']);

$router->get('/vista_manifiesto',[ManifiestosController::class, 'vista_manifiesto']);
$router->post('/vista_manifiesto',[ManifiestosController::class, 'vista_manifiesto']);

//Editar registro semanal
$router->get('/edit_week', [WeeklyController::class,'edit_week_pr']);
$router->post('/edit_week', [WeeklyController::class,'edit_week_pr']);
//Eliminar semana
$router->get('/remove_week',[WeeklyController::class,'remove_week']);
$router->post('/remove_week',[WeeklyController::class,'remove_week']);
$router->get('/delete_week',[WeeklyController::class,'delete_week']);
$router->post('/delete_week',[WeeklyController::class,'delete_week']);
//Actualizar semana
$router->get('/update_week',[WeeklyController::class,'update_week']);
$router->post('/update_week',[WeeklyController::class,'update_week']);

//Vista historial semanal
$router->get('/historial_de_pedidos', [WeeklyController::class, 'historial_semanal']);
$router->post('/historial_de_pedidos', [WeeklyController::class, 'historial_semanal']);
//Buscar por folio inicial en las semanas
$router->get('/search_for_week',[WeeklyController::class, 'search_for_week']);
$router->post('/search_for_week',[WeeklyController::class, 'search_for_week']);
//Editar por folio inicial en las semanas
$router->get('/edit_folio',[WeeklyController::class, 'edit_folio']);
$router->post('/edit_folio',[WeeklyController::class, 'edit_folio']);



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
//$router->get('/historial_de_pedidos',[DashboardController::class, 'historial_de_pedidos']);
$router->get('/unidades_de_transporte',[DashboardController::class, 'unidades_de_transporte']);
$router->get('/servicios_de_unidades',[DashboardController::class, 'servicios_de_unidades']);
$router->get('/rastreo_de_unidades',[DashboardController::class, 'rastreo_de_unidades']);
$router->get('/manifiestos',[ManifiestosController::class, 'manifiestos']);
$router->get('/chat',[DashboardController::class, 'chat']);

// Asignar unidad a chofer
$router->get('/asignar_unidades_a_choferes',[UnidadController::class, 'asignar_unidades_a_choferes']);
$router->post('/asignar_unidades_a_choferes',[UnidadController::class, 'asignar_unidades_a_choferes']);

// Mensaje de exito se asigno la unidad
$router->get('/message_exit_unidad',[UnidadController::class, 'message_exit_unidad']);

// Editar unidad
$router->get('/search_unidad',[UnidadController::class, 'search_unidad']);
$router->post('/search_unidad',[UnidadController::class, 'search_unidad']);
$router->get('/edit_unidad',[UnidadController::class, 'edit_unidad']);
$router->post('/edit_unidad',[UnidadController::class, 'edit_unidad']);
$router->get('/message_update_unidad',[UnidadController::class, 'message_update_unidad']);
$router->post('/message_update_unidad',[UnidadController::class, 'message_update_unidad']);
$router->get('/remove_unidad',[UnidadController::class, 'remove_unidad']);
$router->post('/remove_unidad',[UnidadController::class, 'remove_unidad']);

// Agregar producto
$router->get('/register_product',[ProductsController::class, 'register_product']);
$router->post('/register_product',[ProductsController::class, 'register_product']);

// Mensaje de exito se registro el producto
$router->get('/product_success_message',[ProductsController::class, 'product_success_message']);

// Editar producto
$router->get('/search_product',[ProductsController::class, 'search_product']);
$router->post('/search_product',[ProductsController::class, 'search_product']);
$router->get('/edit_product',[ProductsController::class, 'edit_product']);
$router->post('/edit_product',[ProductsController::class, 'edit_product']);
$router->get('/message_update_product',[ProductsController::class, 'message_update_product']);
$router->post('/message_update_product',[ProductsController::class, 'message_update_product']);

//Eliminar producto
$router->get('/remove_product',[ProductsController::class, 'remove_product']);
$router->post('/remove_product',[ProductsController::class, 'remove_product']);

//se llama listar pero es la vista principal en pedidos
$router->get('/pedidos', [PedidosController::class, 'listar']);

//agregar pedido
$router->get('/pedidos/agregar', [PedidosController::class, 'agregar']);
$router->post('/pedidos/agregar', [PedidosController::class, 'agregar']);

//actualizar estado
$router->post('/pedidos/cambiar-estado', [PedidosController::class, 'cambiarEstado']);

//ver pedido detallado
$router->get('/pedidos/ver', [PedidosController::class, 'ver']);

//editar pedido
$router->get('/pedidos/editar', [PedidosController::class, 'editar']);
$router->post('/pedidos/editar', [PedidosController::class, 'editar']);

//eliminar pedido solo para usuarios tipo 1
$router->post('/pedidos/eliminar', [PedidosController::class, 'eliminar']);

//productos
$router->get('/productos', [Controllers\ProductsController::class, 'index']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();