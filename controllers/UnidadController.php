<?php

namespace Controllers;

use Model\Unidad_de_transporte;
use Model\Usuario;
use MVC\Router;

class UnidadController {

    public static function asignar_unidades_a_choferes(Router $router) {
        session_start();
        //isAuth();
        $alertas = [];
    
        // Obtener los choferes desde la base de datos
        $choferes = Usuario::allChoferes();  // Usamos el método 'allChoferes()' del modelo Usuario
    
        // Validar que el formulario fue enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Crear una nueva instancia de Unidad_de_transporte
            $Unidad_de_transporte = new Unidad_de_transporte($_POST);  // Pasamos los datos del formulario al objeto
    
            // Realizar la validación
            $alertas = $Unidad_de_transporte->validarUnidad();
    
            // Comprobar si se seleccionó un chofer
            if (empty($_POST['chofer'])) {
                $alertas['error'][] = 'Debes de seleccionar un chofer';
            } else {
                // Validar los campos de la unidad
                if (empty($alertas)) {
                    // Verificar si las placas ya están asignadas a otro chofer
                    $placas = $_POST['placas'];
                    $unidadExistente = Unidad_de_transporte::where('placas', $placas);
    
                    if ($unidadExistente && $unidadExistente->chofer != null) {
                        // Si existe una unidad con esas placas y ya está asignada a un chofer
                        $alertas['error'][] = 'Esta unidad ya está asignada a otro chofer';
                    } else {
                        // Si no hay conflicto de placas, continuar con la asignación
                        $Unidad_de_transporte->chofer = $_POST['chofer'];
                        $Unidad_de_transporte->modelo = $_POST['modelo'];
                        $Unidad_de_transporte->placas = $_POST['placas'];
                        $Unidad_de_transporte->url = uniqid(); // Generar una URL única
    
                        // Guardar la unidad de transporte (esto se hace con el método 'guardar' de ActiveRecord)
                        $resultado = $Unidad_de_transporte->guardar();
    
                        if ($resultado['resultado']) {
                            // Si la unidad fue guardada correctamente
                            header('Location: /message_exit_unidad'); // Redirigir a donde desees después de guardar
                        } else {
                            // Si ocurrió un error al guardar
                            $alertas['error'][] = 'Hubo un problema al guardar la unidad';
                        }
                    }
                }
            }
        }
    
        // Renderizar la vista
        $router->render('unidades/asignar_unidades_a_choferes', [
            'alertas' => $alertas,
            'titulo' => 'Asignar unidades a choferes',
            'choferes' => $choferes  // Pasamos la lista de choferes a la vista
        ]);
    }
    
    public static function message_exit_unidad(Router $router) {
        session_start();
        //isAuth();
        $router->render('Unidades/message_exit_unidad', [
            'titulo' => 'Unidad registrada exitosamente'
        ]);
    }
    
    public static function search_unidad(Router $router){
        session_start();
        //isAuth(); // Asegúrate de que el usuario esté autenticado
        $alertas = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Crear un objeto de la unidad de transporte con los datos del formulario
            $Unidad_de_transporte = new Unidad_de_transporte($_POST);
    
            // Validar que el idunidad haya sido ingresado correctamente
            $alertas = $Unidad_de_transporte->validarIdUnidad();  // Asegúrate de tener un método de validación para 'idunidad'
    
            if (empty($alertas)) {
                // Buscar la unidad de transporte en la base de datos usando el ID ingresado
                $unidad = Unidad_de_transporte::where('idunidad', $Unidad_de_transporte->idunidad);  // Buscar unidad por idunidad
    
                if (!$unidad) { // Si no se encuentra la unidad
                    Unidad_de_transporte::setAlerta('error', 'La unidad con este ID no existe');
                } else {
                    // Si la unidad existe, redirige al formulario de edición pasando el idunidad
                    header('Location: /edit_unidad?idunidad=' . $unidad->idunidad);
                    exit;  // Asegúrate de que no se ejecute más código después de la redirección
                }
            }
        }
    
        // Si no es un POST, simplemente renderizamos la vista con las alertas
        $alertas = Unidad_de_transporte::getAlertas();
    
        // Renderizar la vista
        $router->render('Unidades/search_unidad', [
            'titulo' => 'Buscando unidad',
            'alertas' => $alertas,
        ]);
    }

    public static function edit_unidad(Router $router) {
        session_start();
        //isAuth();
        $alertas = [];
    
        // Obtener la unidad a editar
        $idunidad = $_GET['idunidad'] ?? '';
        $unidad = Unidad_de_transporte::where('idunidad', $idunidad);
    
        if (!$unidad) {
            // Si no se encuentra la unidad
            header('Location: /search_unidad');
            exit;
        }
    
        // Obtener choferes
        $choferes = Usuario::allChoferes();
    
        // Validar si el formulario fue enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             // Verificar qué llega en el POST
            var_dump($_POST);  // Aquí verás lo que está llegando en el POST

            // Sincronizamos los datos
            $unidad->modelo = $_POST['modelo'] ?? $unidad->modelo;
            $unidad->placas = $_POST['placas'] ?? $unidad->placas;

            // Validar que se haya seleccionado un chofer válido
            if (!empty($_POST['chofer']) && is_numeric($_POST['chofer']) && (int)$_POST['chofer'] > 0) {
                $unidad->chofer = (int)$_POST['chofer'];
            } else {
                $alertas['error'][] = 'Debes seleccionar un chofer válido';
            }

            // Validar los datos del modelo y placas
            $alertas = array_merge($alertas, $unidad->validarUnidad());

            // Guardar si no hay errores
            if (empty($alertas)) {
                $resultado = $unidad->guardar();

                if ($resultado['resultado']) {
                    var_dump($_POST);  // Aquí verás lo que está llegando en el POST
                    header('Location: /message_update_unidad');
                    exit;
                } else {
                    $alertas['error'][] = 'Hubo un problema al actualizar la unidad';
                }
            }
        }

        // Renderizar la vista de edición
        $router->render('Unidades/edit_unidad', [
            'titulo' => 'Editar Unidad',
            'unidad' => $unidad,
            'choferes' => $choferes,
            'alertas' => $alertas
        ]);
    }
    
    public static function remove_unidad(Router $router) {
        session_start();
        //isAuth(); 
        $alertas = [];
    
        // Si se recibe el idunidad por POST, proceder a eliminar la unidad
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idunidad'])) {
            $idunidad = $_POST['idunidad'];
    
            // Buscar la unidad por idunidad
            $unidad = Unidad_de_transporte::where('idunidad', $idunidad);
    
            if ($unidad) {
                // Eliminar la unidad
                $unidad->eliminar();
    
                // Mensaje de éxito
                Unidad_de_transporte::setAlerta('exito', 'Unidad eliminada correctamente');
                header('Location: /remove_unidad');
                exit;
            } else {
                Unidad_de_transporte::setAlerta('error', 'La unidad no existe');
            }
        }
    
        // Obtener todas las unidades de transporte
        $unidades = Unidad_de_transporte::all();
        foreach ($unidades as $unidad) {
            $chofer = Usuario::find($unidad->chofer);
            $unidad->chofer_nombre = $chofer ? $chofer->nombre : 'Sin chofer asignado';
        }
    
        // Obtener alertas
        $alertas = Unidad_de_transporte::getAlertas();
    
        // Renderizar la vista
        $router->render('Unidades/remove_unidad', [
            'titulo' => 'Eliminar Unidad',
            'unidades' => $unidades,
            'alertas' => $alertas
        ]);
    }    
      
    public static function message_update_unidad(Router $router){
        session_start();
        //isAuth();
        $router->render('Unidades/message_update_unidad', [
            'titulo' => 'Unidad actualizada exitosamente'
        ]);
    }
}
