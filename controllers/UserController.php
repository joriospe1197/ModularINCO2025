<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use Model\Chofer;
use MVC\Router;

class UserController {
    
    public static function register(Router $router) {
        session_start();
        isAuth();
        $alertas = [];
        $usuario = new Usuario;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sincronizar los datos del formulario con el objeto Usuario
            $usuario->sincronizar($_POST);

            // Validar que no falte el puesto
            $alertas = $usuario->validarNuevaCuenta();

            // Si no hay alertas, proceder con el registro
            if (empty($alertas)) {
                // Verificar si el email ya existe
                $existeUsuario = Usuario::where('email', $usuario->email);
                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'El email ya está registrado');
                    $alertas = Usuario::getAlertas();
                }

                // Verificar si el nombre ya está registrado
                $existeNombre = Usuario::where('nombre', $usuario->nombre);
                if ($existeNombre) {
                    Usuario::setAlerta('error', 'El nombre ya está registrado');
                    $alertas = Usuario::getAlertas();
                }

                // Verificar si la dirección ya está registrada
                $existeDireccion = Usuario::where('direccion', $usuario->direccion);
                if ($existeDireccion) {
                    Usuario::setAlerta('error', 'La dirección ya está registrada');
                    $alertas = Usuario::getAlertas();
                }

                // Verificar si el teléfono ya está registrado
                $existeTelefono = Usuario::where('telefono', $usuario->telefono);
                if ($existeTelefono) {
                    Usuario::setAlerta('error', 'El teléfono ya está registrado');
                    $alertas = Usuario::getAlertas();
                }

                // Si no hay alertas, procesar el registro
                if (empty($alertas)) {
                    // Encriptar contraseña
                    $usuario->hashPassword();

                    // Eliminar el elemento 'contrasena2' del objeto
                    unset($usuario->contrasena2);

                    // Generar token
                    $usuario->crearToken();

                    // Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    // Enviar email de confirmación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    if ($resultado) {
                        header('Location: /message');
                    }
                }
            }
        }

        // Renderizar la vista
        $router->render('auth/register', [
            'titulo' => 'Agregar empleado',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    
    public static function message(Router $router){
        session_start();
        isAuth();
        $router->render('auth/message', [
            'titulo' => 'Cuenta creada exitosamente'
        ]);
    }

    public static function confirm(Router $router){
        $token = s($_GET['token']);

        if(!$token) header('Location: /');

        // Encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            // No se encontró un usuario con ese token.
            Usuario::setAlerta('error', 'Token no válido'); 
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->tipo_usuario = 0;
            $usuario->token = null;
            unset($usuario->contrasena2);

            // Depuración: Verificar valores antes de guardar
            //var_dump($usuario); 

            //Guardar en la DB (esto debería actualizar el registro existente)
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Comprobó su cuenta de manera correcta'); 
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirm', [
            'titulo' => 'Confirma tu cuenta',
            'alertas' =>  $alertas
        ]);
    }

    public static function search_user(Router $router){
        session_start();
        isAuth();
        $alertas = [];
        $usuarioEncontrado = null; // Cambiar nombre para evitar conflicto

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idempleado = $_POST['idempleado'] ?? '';
            
            // Validar que no esté vacío
            if (empty($idempleado)) {
                Usuario::setAlerta('error', 'El ID de empleado es requerido');
            } else {
                // Buscar directamente por el ID
                $usuarioEncontrado = Usuario::where('idempleado', $idempleado);
                
                if (!$usuarioEncontrado) {
                    Usuario::setAlerta('error', 'El empleado con ID ' . $idempleado . ' no existe');
                } elseif (!$usuarioEncontrado->confirmado) {
                    Usuario::setAlerta('error', 'El empleado con ID ' . $idempleado . ' no está confirmado');
                } else {
                    // Redirigir a edición
                    header('Location: /edit_user?idempleado=' . $usuarioEncontrado->idempleado);
                    exit;
                }
            }
        }

        $alertas = Usuario::getAlertas();
        
        $router->render('auth/search_user', [
            'titulo' => 'Buscar empleado',
            'alertas' => $alertas,
            'usuario' => $usuarioEncontrado // Pasar el usuario encontrado
        ]);
    }
    
    public static function edit_user(Router $router){
        session_start();
        isAuth(); 
        $alertas = [];
        $usuario = null;
    
        // Verificamos si se ha enviado el idempleado por GET (cuando el usuario es redirigido desde search_user)
        if (isset($_GET['idempleado']) && $_GET['idempleado']) {
            $idempleado = $_GET['idempleado'];
    
            // Buscamos al usuario en la base de datos por idempleado
            $usuario = Usuario::where('idempleado', $idempleado);
    
            if (!$usuario) {
                // Si no se encuentra al usuario, mostramos la alerta
                Usuario::setAlerta('error', 'El usuario no existe');
                header('Location: /search_user');
                exit;
            }
            

        } else {
            // Si no hay idempleado, redirigir a búsqueda
            header('Location: /search_user');
            exit;
        }
    
        // Si recibimos los datos del formulario (método POST), actualizamos el usuario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           
            // Sincronizamos los datos del formulario con el objeto $usuario
            $usuario->sincronizar($_POST);
    
            // Validamos los campos (esto puede cambiar según tus reglas de validación)
            if (empty($usuario->nombre)) {
                Usuario::setAlerta('error', 'El nombre no puede estar vacío');
            }
    
            if (empty($usuario->direccion)) {
                Usuario::setAlerta('error', 'La dirección no puede estar vacía');
            }
    
            if (empty($usuario->telefono)) {
                Usuario::setAlerta('error', 'El teléfono no puede estar vacío');
            }
    
            if (empty($usuario->email)) {
                Usuario::setAlerta('error', 'El email no puede estar vacío');
            }
    
            // Si no hay alertas, procesamos la actualización
            $alertas = Usuario::getAlertas();
            if (empty($alertas)) {
                // Guardamos los cambios en la base de datos
                $resultado = $usuario->guardar();  // Aquí se guarda la actualización
    
                if ($resultado) {
                    // Si la actualización fue exitosa, redirigimos a una página de éxito
                    Usuario::setAlerta('exito', 'Usuario actualizado correctamente');
                    header('Location: /message_update_user'); // Redirigir a la página de éxito
                    exit;
                } else {
                    Usuario::setAlerta('error', 'Error al actualizar el usuario');
                }
            }
        }
    
        // Obtener las alertas para mostrarlas en la vista
        $alertas = Usuario::getAlertas();
    
        // Renderizar la vista para editar el usuario
        $router->render('auth/edit_user', [
            'titulo' => 'Editar datos del empleado',
            'usuario' => $usuario,  // Pasamos el usuario a la vista para mostrar los datos actuales
            'alertas' => $alertas
        ]);
    }
    

    public static function message_update_user(Router $router){
        session_start();
        isAuth();
        $router->render('auth/message_update_user', [
            'titulo' => 'Usuario actualizado exitosamente'
        ]);
    }

    public static function remove_user(Router $router) {
        session_start();
        isAuth();  
        $alertas = [];
    
        // Obtener todos los empleados (usuarios)
        $empleados = Usuario::all();
    
        if (isset($_GET['idempleado']) && $_GET['idempleado']) {
            $idempleado = $_GET['idempleado'];
            
            // Buscar al usuario por idempleado
            $usuario = Usuario::where('idempleado', $idempleado);
    
            if ($usuario) {
                // Conexión a la base de datos
                $db = \Model\ActiveRecord::getConnection();
    
                // Verificar si tiene pedidos activos
                $query = "
                    SELECT COUNT(*) AS total 
                    FROM pedidos 
                    WHERE 
                        (id_empleado_chofer = {$idempleado} OR id_empleado_registra = {$idempleado})
                        AND estado IN ('pendiente', 'en proceso')
                ";
                $res = $db->query($query);
                $row = $res->fetch_assoc();
                $tienePedidosActivos = $row['total'] > 0;
    
                if ($tienePedidosActivos) {
                    // No eliminar si tiene pedidos activos
                    Usuario::setAlerta('error', 'No se puede eliminar al empleado "' . $usuario->nombre . '" porque tiene pedidos activos.');
                } else {
                    // Eliminar usuario si no tiene pedidos activos
                    $usuario->eliminar();
                    Usuario::setAlerta('exito', 'Empleado "' . $usuario->nombre . '" eliminado correctamente.');
                    
                    // Volvemos a cargar la lista de empleados después de eliminar
                    $empleados = Usuario::all();
                }
            } else {
                Usuario::setAlerta('error', 'El usuario no existe');
            }
        }
    
        // Obtener alertas
        $alertas = Usuario::getAlertas();
    
        // Renderizar la vista y pasar los empleados
        $router->render('auth/remove_user', [
            'titulo' => 'Eliminar empleado',
            'alertas' => $alertas,
            'empleados' => $empleados,
        ]);
    }
    
    
    
    
}
