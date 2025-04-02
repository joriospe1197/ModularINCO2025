<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router){

        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Lógica para el inicio de sesión
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if(empty($alertas)){
                //verificar que el usuario exista.
                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || !$usuario->confirmado){ //Verificando que el usuario exista y que este confirmado.
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }else{
                    // El usuario si existe y esta confirmado.
                    if(password_verify($_POST['contrasena'], $usuario->contrasena)){
                        
                        // Inicio de sesión
                        session_start();
                        $_SESSION['idempleado'] = $usuario->idempleado;  // ID de empleado
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        $_SESSION['tipo_usuario'] = $usuario->tipo_usuario;  // Guardar el tipo de usuario en la sesión

                        //Redireccionar
                        header('Location: /inicio');
                    }                                       
                    else{
                        Usuario::setAlerta('error', 'Contraseña incorrecta');
                    }
                }
            }
            
        }

        $alertas = Usuario::getAlertas();
        // Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function forgot_my_password(Router $router){
        $alertas = [];//Arreglo vacio
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Lógica para recuperación de contraseña
            $usuario = new Usuario($_POST);//Creamos una nueva instancia
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                //Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado){
                    //Generar un token nuevo
                    $usuario->crearToken();
                    unset($usuario->contrasena2);
                    
                    //Actualizar el usuario
                    $usuario->guardar();

                    //Enviar el email
                    $email = new Email( $usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //Imprimir la alerta
                    Usuario::setAlerta('exito', 'Enviamos las instrucciones a tu email');
                }else{
                    Usuario::setAlerta('error', 'No existe el usuario o no esta confirmado');
                }
                
            }
        }
        $alertas = Usuario::getAlertas();

        // Muestra la vista
        $router->render('auth/forgot_my_password', [
            'titulo' => 'Olvide mi contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function recover(Router $router){
        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token) header('Location: /'); 

        // Identificar el usuario con este token
        $usuario = Usuario::where('token',$token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Lógica para recuperar la contraseña
            $usuario->sincronizar($_POST);

            //Validar el password
            $alertas = $usuario->validarPassword();  
            
            if(empty($alertas)){
                //Hasear el nuevo password  
                $usuario->hashPassword();

                //Eliminar el token
                $usuario->token = null;

                // Guardar el usuario en la BD
                $resultado = $usuario->guardar();

                //Redireccionar
                if($resultado){
                    header('Location: /');
                }

            }
            
        }

        $alertas = Usuario::getAlertas();
        // Muestra la vista 
        $router->render('auth/recover', [
            'titulo' => 'Restablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }
}
