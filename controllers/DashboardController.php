<?php

namespace Controllers;

use MVC\Router;

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

    public static function empleados(Router $router){
        session_start();

        isAuth();

        $router->render('dashboard/empleados', [
            'titulo' => 'Empleados'
        ]);
    }


    public static function productos(Router $router){
        session_start();

        isAuth();

        $router->render('dashboard/productos', [
            'titulo' => 'Productos'
        ]);
    }

    public static function pedidos(Router $router){
        session_start();

        isAuth();

        $router->render('dashboard/pedidos', [
            'titulo' => 'Pedidos'
        ]);
    }

    public static function historial_de_pedidos(Router $router){
        session_start();

        isAuth();

        $router->render('dashboard/historial_de_pedidos', [
            'titulo' => 'Historial de pedidos'
        ]);
    }


    public static function choferes(Router $router){
        session_start();

        isAuth();

        $router->render('dashboard/choferes', [
            'titulo' => 'Choferes'
        ]);
    }
   
    public static function servicios_de_unidades(Router $router){
        session_start();

        isAuth();

        $router->render('dashboard/servicios_de_unidades', [
            'titulo' => 'Servicios de unidades'
        ]);
    }

    public static function rastreo_de_unidades(Router $router){
        session_start();

        isAuth();

        $router->render('dashboard/rastreo_de_unidades', [
            'titulo' => 'Rastreo de unidades'
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