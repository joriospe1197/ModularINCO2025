<?php
namespace Controllers;

use Model\ServicioUnidad;
use Model\TipoServicioUnidad;
use Model\Unidad_de_transporte;
use Model\Usuario;
use MVC\Router;

class ServicioUnidadController {

    public static function crear(Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        try {
            $tipos_servicio = TipoServicioUnidad::todos();
            $unidades = Unidad_de_transporte::all();

            foreach ($unidades as $unidad) {
                $chofer = Usuario::find($unidad->chofer);
                $unidad->chofer_nombre = $chofer ? $chofer->nombre : 'Sin chofer';
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $servicio = new ServicioUnidad($_POST);
                $alertas = $servicio->validarServicio();

                if (empty($alertas)) {
                    $tipo_servicio = TipoServicioUnidad::find($servicio->id_tipo_servicio);
                    
                    if (!$tipo_servicio) {
                        throw new \Exception('Tipo de servicio no válido');
                    }

                    $servicio->calcularSiguienteServicio($tipo_servicio->intervalo_meses);
                    $servicio->estado = 'pendiente'; // Nuevo servicio empieza como pendiente

                    $resultado = $servicio->guardarManual();

                    if (!$resultado['resultado']) {
                        throw new \Exception('Error al guardar el servicio en la base de datos');
                    }

                    $_SESSION['alerta'] = [
                        'tipo' => 'exito',
                        'mensaje' => 'Servicio registrado correctamente'
                    ];
                    header('Location: /servicios_de_unidades');
                    exit;
                }
            }
        } catch (\Exception $e) {
            $alertas['error'][] = $e->getMessage();
        }

        $router->render('servicios_de_unidades/crear', [
            'titulo' => 'Registrar Servicio',
            'alertas' => $alertas,
            'tipos_servicio' => $tipos_servicio,
            'unidades' => $unidades,
            'servicio' => $servicio ?? new ServicioUnidad()
        ]);
    }
    public static function historial(Router $router) {
        session_start();
        isAuth();

        $idunidad = $_GET['id'] ?? '';
        
        if ($idunidad) {
            $servicios = ServicioUnidad::obtenerServiciosPorUnidad($idunidad);
            $unidad = Unidad_de_transporte::where('idunidad', $idunidad);
            if ($unidad) {
                $chofer = Usuario::find($unidad->chofer);
                $unidad->chofer_nombre = $chofer ? $chofer->nombre : 'Sin chofer';
            }
        } else {
            $servicios = [];
            $unidad = null;
        }

        $unidades = Unidad_de_transporte::all();
        
        // Agregar nombres de choferes a las unidades
        foreach ($unidades as $unidad_item) {
            $chofer = Usuario::find($unidad_item->chofer);
            $unidad_item->chofer_nombre = $chofer ? $chofer->nombre : 'Sin chofer';
        }

        $router->render('servicios_de_unidades/historial_de_servicios', [
            'titulo' => 'Historial de Servicios',
            'servicios' => $servicios,
            'unidad' => $unidad,
            'unidades' => $unidades
        ]);
    }

    public static function completar(Router $router) {
        session_start();
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_servicio = $_POST['id_servicio'] ?? '';
            
            if ($id_servicio) {
                $servicio = ServicioUnidad::find($id_servicio);
                
                if ($servicio) {
                    $servicio->estado = 'completado';
                    $resultado = $servicio->guardarManual();
                    
                    if ($resultado['resultado']) {
                        $_SESSION['alerta'] = [
                            'tipo' => 'exito',
                            'mensaje' => 'Servicio marcado como completado correctamente'
                        ];
                    } else {
                        $_SESSION['alerta'] = [
                            'tipo' => 'error',
                            'mensaje' => 'Error al completar el servicio'
                        ];
                    }
                } else {
                    $_SESSION['alerta'] = [
                        'tipo' => 'error',
                        'mensaje' => 'Servicio no encontrado'
                    ];
                }
            } else {
                $_SESSION['alerta'] = [
                    'tipo' => 'error',
                    'mensaje' => 'ID de servicio no válido'
                ];
            }

            header('Location: /servicios_de_unidades');
            exit;
        }
    }

    public static function cambiar_estado(Router $router) {
        session_start();
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_servicio = $_POST['id_servicio'] ?? '';
            $nuevo_estado = $_POST['nuevo_estado'] ?? '';
            
            if ($id_servicio && $nuevo_estado) {
                $servicio = ServicioUnidad::find($id_servicio);
                
                if ($servicio) {
                    // DEBUG: Verificar que estamos actualizando el registro correcto
                    error_log("Actualizando servicio ID: " . $id_servicio . " a estado: " . $nuevo_estado);
                    
                    $resultado = $servicio->cambiarEstado($nuevo_estado);
                    
                    if ($resultado['resultado']) {
                        $_SESSION['alerta'] = [
                            'tipo' => 'exito',
                            'mensaje' => 'Estado del servicio actualizado correctamente'
                        ];
                    } else {
                        $_SESSION['alerta'] = [
                            'tipo' => 'error',
                            'mensaje' => 'Error al actualizar el estado: ' . ($resultado['error'] ?? 'Error desconocido')
                        ];
                    }
                } else {
                    $_SESSION['alerta'] = [
                        'tipo' => 'error',
                        'mensaje' => 'Servicio no encontrado'
                    ];
                }
            } else {
                $_SESSION['alerta'] = [
                    'tipo' => 'error',
                    'mensaje' => 'Datos incompletos'
                ];
            }

            // Redirigir de vuelta al historial si viene de ahí
            if (isset($_POST['redirect_to_historial_de_servicios']) && $_POST['redirect_to_historial_de_servicios']) {
                $idunidad = $_POST['idunidad'] ?? '';
                header('Location: /servicios_de_unidades/historial_de_servicios' . ($idunidad ? '?id=' . $idunidad : ''));
            } else {
                header('Location: /servicios_de_unidades');
            }
            exit;
        }
    }
}