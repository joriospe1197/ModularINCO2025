<?php

namespace Controllers;

use Model\Unidad_de_transporte;
use Model\Productos;
use Model\Usuario;
use MVC\Router;
use Model\Pedidos;
use Model\ServicioUnidad;


class DashboardController{

    public static function mi_perfil(Router $router){
        session_start();
        $router->render('dashboard/mi_perfil', [
            'titulo' => 'Mi perfil'
        ]);
    }

    public static function inicio(Router $router){

        session_start();
        isAuth();

        $router->render('dashboard/inicio', [
            'titulo' => 'Inicio'
        ]);
    }

    public static function empleados(Router $router) {
        session_start();
        isAuth();
         // Validar que el usuario sea tipo 1 (admin)
        if ($_SESSION['tipo_usuario'] != 1) {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'No tienes permisos para acceder a esta sección'
            ];
            header('Location: /inicio');
            exit;
        }
    
        // Obtener todos los empleados
        $empleados = Usuario::all();  // Usamos el método all() de ActiveRecord para obtener todos los empleados
    
        // Renderizar la vista pasando los empleados
        $router->render('dashboard/empleados', [
            'titulo' => 'Empleados',
            'empleados' => $empleados
        ]);
    }


    public static function productos(Router $router) {
        session_start();
        isAuth();
    
        // Obtener todos los productos
        $productos = Productos::all();  // Usamos el método all() de ActiveRecord para obtener todos los productos
    
        // Renderizamos la vista pasando los productos
        $router->render('Dashboard/productos', [
            'titulo' => 'Productos',
            'productos' => $productos
        ]);
    }    

    public static function pedidos(Router $router) {
        session_start();
        isAuth();

        // Obtener los pedidos
        $pedidos = Pedidos::obtenerTodos();
        
        $router->render('dashboard/pedidos', [
            'titulo' => 'Pedidos',
            'pedidos' => $pedidos
        ]);
    }

    public static function historial_de_pedidos(Router $router){
        session_start();

        isAuth();

        $router->render('dashboard/historial_de_pedidos', [
            'titulo' => 'Historial de pedidos'
        ]);
    }

    public static function unidades_de_transporte(Router $router) {
        session_start();
        isAuth();
    
        // Obtener las unidades de transporte y los choferes asociados
        $unidades = Unidad_de_transporte::all();  // Obtiene todas las unidades
        foreach ($unidades as $unidad) {
            // Obtener el chofer por su idempleado (usamos el id del chofer para obtener el nombre)
            $unidad->chofer_nombre = Usuario::find($unidad->chofer)->nombre;
        }
    
        $router->render('dashboard/unidades_de_transporte', [
            'titulo' => 'Unidades de transporte',
            'unidades' => $unidades  // Pasamos las unidades junto con los nombres de chofer
        ]);
    }
    
   

    public static function servicios_de_unidades(Router $router) {
        session_start();
        isAuth();
        
        // Obtener todas las unidades con sus choferes
        $unidades = Unidad_de_transporte::all();
        foreach ($unidades as $unidad) {
            $chofer = Usuario::find($unidad->chofer);
            $unidad->chofer_nombre = $chofer ? $chofer->nombre : 'Sin chofer';
        }

        // Obtener servicios pendientes (vencidos)
        $serviciosPendientes = ServicioUnidad::obtenerServiciosPendientes();
        
        // Obtener próximos servicios (próximos a vencer)
        $proximosServicios = ServicioUnidad::obtenerProximosServicios();

        $router->render('dashboard/servicios_de_unidades', [
            'titulo' => 'Servicios de Unidades',
            'unidades' => $unidades,
            'serviciosPendientes' => $serviciosPendientes,
            'proximosServicios' => $proximosServicios
        ]);
    }

    
    public static function manifiestos(Router $router){
        session_start();

        isAuth();

        $router->render('dashboard/manifiestos', [
            'titulo' => 'Manifiestos'
        ]);
    }

    public static function chat(Router $router){
        session_start();

        isAuth();

        $router->render('dashboard/chat', [
            'titulo' => 'Chat'
        ]);
    }
    

    
}