<?php
namespace Controllers;

use Model\Pedidos;
use Model\Empleados;
use MVC\Router;
use Model\Usuario;
use Model\Productos;
use Model\PedidoProducto;
use Model\ActiveRecord;
use Model\Clientes;
use Exception;

class PedidosController {
    //listar o vista principal funcionando en pedidos en dashboard
    public static function listar(Router $router) {
        session_start();
        isAuth();
        
        $pedidos = Pedidos::obtenerTodos();
        $pedidosConClientes = []; //  Inicializar el array
        
        foreach ($pedidos as $pedido) {
            $clienteFrecuente = null;
            if ($pedido->id_cliente) {
                $clienteFrecuente = Clientes::findById($pedido->id_cliente);
            }
            
            $pedido->clienteFrecuente = $clienteFrecuente;
            $pedidosConClientes[] = $pedido;
        }

        $router->render('dashboard/pedidos', [
            'titulo' => 'Lista de Pedidos',
            'pedidos' => $pedidosConClientes //  Pasar el array correcto
        ]);
    }


    //agregar
    public static function agregar(Router $router) {
        session_start();
        isAuth();
                
        $alertas = [];
        $pedido = new Pedidos();
        $choferes = Usuario::allChoferes();
        $productos = Productos::all();
        $clientes = Clientes::all(); //  Todos los clientes registrados
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $pedido->sincronizar($_POST);
            

             // Validación de cliente
            $id_cliente = $_POST['id_cliente'] ?? null;
            if ($id_cliente) {
                $cliente = Clientes::findById($id_cliente);
                if ($cliente) {
                    // Guardamos snapshot
                    $pedido->id_cliente = $cliente->id;
                    $pedido->nombre_cliente = $cliente->razon_social;
                    $pedido->domicilio_cliente = $cliente->domicilio;
                    $pedido->telefono_cliente = $cliente->telefono;
                }
            }

            //validar
            $alertas = $pedido->validar();

            
            if (empty($alertas['error'])) {
                // Generar código de pedido
                $pedido->codigo_pedido = self::generarCodigoPedido($pedido->fecha_pedido);

                if (!isset($_SESSION['idempleado'])) {
                    throw new Exception('Usuario no autenticado o sesión inválida');
                }
                $pedido->id_empleado_registra = $_SESSION['idempleado'];
                $pedido->estado = 'pendiente';

                if (empty($_POST['id_cliente']) || $_POST['id_cliente'] === '0') {
                    $_POST['id_cliente'] = null;
                }

                // Guardar pedido y productos
                $resultado = $pedido->guardarConProductos($_POST['productos'] ?? [], $_POST['cantidades'] ?? []);

                if ($resultado) {

                    $_SESSION['alerta'] = [
                        'tipo' => 'exito',
                        'mensaje' => 'Pedido registrado correctamente con folio: ' . $pedido->codigo_pedido
                    ];
                    header('Location: /pedidos/agregar');
                    return;
                } else {
                    $alertas['error'][] = 'Error al guardar el pedido. Verifica los datos.';
                }
            }
        }
        
        $router->render('pedidos/agregar', [
            'titulo' => 'Nuevo Pedido',
            'pedido' => $pedido,
            'choferes' => $choferes,
            'productos' => $productos,
            'clientes' => $clientes,
            'alertas' => $alertas
        ]);
    }

    private static function generarCodigoPedido($fecha) {
        $fecha_formato = date('Ymd', strtotime($fecha));
        $fecha_sql = date('Y-m-d', strtotime($fecha));
        
        // Primero: Contar pedidos del día
        $consulta_count = "SELECT COUNT(*) as total FROM pedidos WHERE DATE(fecha_pedido) = '$fecha_sql'";
        $resultado_count = Pedidos::SQL($consulta_count);
        $total_pedidos = $resultado_count[0]->total ?? 0;
        
        // Segundo: Buscar el máximo consecutivo
        $consulta_max = "SELECT codigo_pedido FROM pedidos 
                        WHERE DATE(fecha_pedido) = '$fecha_sql' 
                        ORDER BY id DESC LIMIT 1";
        
        $resultado_max = Pedidos::SQL($consulta_max);
        
        if (!empty($resultado_max)) {
            $ultimo_codigo = $resultado_max[0]->codigo_pedido;
            // Extraer el número consecutivo del último código
            if (preg_match('/-(\d{4})$/', $ultimo_codigo, $matches)) {
                $consecutivo = (int)$matches[1] + 1;
            } else {
                $consecutivo = $total_pedidos + 1;
            }
        } else {
            $consecutivo = 1;
        }
        
        return "PED-{$fecha_formato}-" . str_pad($consecutivo, 4, '0', STR_PAD_LEFT);
    }


// En PedidosController.php, reemplaza el método cambiarEstado

    public static function cambiarEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            return;
        }

        header('Content-Type: application/json');
        
        try {
            if (!isset($_POST['pedido_id']) || !isset($_POST['nuevo_estado'])) {
                throw new \Exception('Parámetros incompletos');
            }

            $pedidoId = (int)$_POST['pedido_id'];
            $nuevoEstado = trim(strtolower($_POST['nuevo_estado']));

            // Buscar el pedido
            $pedido = Pedidos::find($pedidoId);
            if (!$pedido) {
                throw new \Exception('Pedido no encontrado');
            }

            // Validar estado permitido
            $estadosPermitidos = ['pendiente', 'en proceso', 'finalizado', 'cancelado'];
            if (!in_array($nuevoEstado, $estadosPermitidos)) {
                throw new \Exception('Estado no válido: ' . $nuevoEstado);
            }

            // Validar transiciones de estado
            if ($pedido->estado === 'finalizado' || $pedido->estado === 'cancelado') {
                throw new \Exception('No se puede modificar un pedido ' . $pedido->estado);
            }

            // Si el estado es finalizado o cancelado, capturar datos financieros
            if (($nuevoEstado === 'finalizado' || $nuevoEstado === 'cancelado') && 
                ($pedido->estado !== 'finalizado' && $pedido->estado !== 'cancelado')) {
                
                // Validar que se hayan enviado los datos financieros
                $camposFinancieros = ['gastos', 'costo', 'pagados', 'almacen', 'depositos'];
                foreach ($camposFinancieros as $campo) {
                    if (!isset($_POST[$campo])) {
                        throw new \Exception('Faltan datos financieros. Campo requerido: ' . $campo);
                    }
                }
                
                // Validar datos financieros
                $alertas = $pedido->capturarDatosFinancieros($_POST);
                if (!empty($alertas['error'])) {
                    throw new \Exception(implode(', ', $alertas['error']));
                }
            }

            // Actualizar el estado y datos financieros
            $pedido->estado = $nuevoEstado;
            $resultado = $pedido->guardar();

            if (!$resultado) {
                throw new \Exception('Error al guardar el cambio de estado');
            }

            // Respuesta exitosa
            echo json_encode([
                'success' => true,
                'nuevo_estado' => ucfirst($pedido->estado)
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


    public static function ver(Router $router) {
        session_start();
        isAuth();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /pedidos');
            exit;
        }

        $pedido = Pedidos::find($id);
        if (!$pedido) {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'El pedido no existe'
            ];
            header('Location: /pedidos');
            exit;
        }
        $clienteFrecuente = null;
        if ($pedido->id_cliente) {
            $clienteFrecuente = Clientes::findById($pedido->id_cliente);
        }

        // Obtener los productos del pedido
        $productos = PedidoProducto::obtenerProductosPorPedido($id);
        
        $router->render('pedidos/ver', [
            'titulo' => 'Detalles del Pedido',
            'pedido' => $pedido,
            'productos' => $productos,
            'clienteFrecuente' => $clienteFrecuente
            
        ]);
    }
     // API para AJAX (clientes)
    public static function apiCliente() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $cliente = Clientes::findById($id);
            if ($cliente) {
                header('Content-Type: application/json');
                echo json_encode([
                    'razon_social' => $cliente->razon_social,
                    'domicilio' => $cliente->domicilio,
                    'telefono' => $cliente->telefono
                ]);
                return;
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Cliente no encontrado']);
    }

    public static function editar(Router $router) {
        session_start();
        isAuth();
        
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: /pedidos');
            exit;
        }
        
        $id = (int)$_GET['id'];
        $pedido = Pedidos::find($id);
        
        if (!$pedido) {
            header('Location: /pedidos');
            exit;
        }
        
        // Obtener datos necesarios
        $choferes = Usuario::allChoferes();
        $productos = Productos::all();
        $productos_pedido = PedidoProducto::obtenerProductosPorPedido($id);
        $clientes = Clientes::all();
        
        $alertas = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // No permitir cambiar tipo de cliente (frecuente ↔ ocasional)
            $tipoClienteOriginal = $_POST['tipo_cliente_original'] ?? '';
            $idClienteOriginal = (int)($_POST['id_cliente_original'] ?? 0);
            
            if ($tipoClienteOriginal === 'frecuente' && empty($_POST['id_cliente'])) {
                $alertas['error'][] = 'No se puede cambiar el tipo de cliente de frecuente a ocasional';
            }
            
            if ($tipoClienteOriginal === 'ocasional' && !empty($_POST['id_cliente'])) {
                $alertas['error'][] = 'No se puede cambiar el tipo de cliente de ocasional a frecuente';
            }
            
            // Permitir cambiar entre clientes frecuentes
            // Solo validamos el tipo, no el cliente específico
            
            // Si cambia de Materiales a otro servicio, limpiar productos
            $servicioOriginal = $pedido->servicio;
            $servicioNuevo = $_POST['servicio'] ?? '';
            
            if ($servicioOriginal === 'Materiales' && $servicioNuevo !== 'Materiales') {
                // Limpiar productos al cambiar de Materiales a otro servicio
                $_POST['productos'] = [''];
                $_POST['cantidades'] = [1];
            }
            
            // Solo continuar si no hay errores de validación
            if (empty($alertas['error'])) {
                // Sincronizar con POST data
                $pedido->sincronizar($_POST);
                
                // Mantener consistencia del tipo de cliente PERO permitir cambiar cliente frecuente
                if ($tipoClienteOriginal === 'frecuente') {
                    // Permitir cambiar entre clientes frecuentes, pero asegurar que sigue siendo frecuente
                    if (empty($_POST['id_cliente'])) {
                        // Si llegó aquí es porque quitó la selección (error)
                        $alertas['error'][] = 'Debe seleccionar un cliente frecuente';
                    } else {
                        // Mantener como cliente frecuente pero con el NUEVO cliente seleccionado
                        $pedido->nombre_cliente = '';
                        $pedido->domicilio_cliente = '';
                        $pedido->telefono_cliente = '';
                    }
                } else {
                    // Forzar que mantenga el cliente ocasional
                    $pedido->id_cliente = null;
                }
                
                if (empty($alertas['error'])) {
                    $alertas = $pedido->validar();
                }
                
                if (empty($alertas['error'])) {
                    // Actualizar pedido
                    $resultado = $pedido->guardar();
                    
                    if ($resultado) {
                        // Solo actualizar productos si es servicio de Materiales
                        if ($_POST['servicio'] === 'Materiales') {
                            $pedido->actualizarProductos($_POST['productos'] ?? [], $_POST['cantidades'] ?? []);
                        } else {
                            // Eliminar productos si no es Materiales
                            PedidoProducto::eliminarPorPedido($pedido->id);
                        }
                        
                        $_SESSION['alerta'] = [
                            'tipo' => 'exito',
                            'mensaje' => 'Pedido actualizado correctamente'
                        ];
                        header('Location: /pedidos/ver?id=' . $pedido->id);
                        return;
                    } else {
                        $alertas['error'][] = 'Error al actualizar el pedido';
                    }
                }
            }
        }
        
        $router->render('pedidos/editar', [
            'titulo' => 'Editar Pedido',
            'pedido' => $pedido,
            'choferes' => $choferes,
            'productos' => $productos,
            'productos_pedido' => $productos_pedido,
            'clientes' => $clientes,
            'alertas' => $alertas
        ]);
    }

    public static function eliminar() {
        session_start();
        isAuth();
        
        // Verificar que sea método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pedidos');
            exit;
        }
        
        // Verificar permisos (tipo_usuario = 1)
        if ($_SESSION['tipo_usuario'] != 1) {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'No tienes permisos para eliminar pedidos'
            ];
            header('Location: /pedidos');
            exit;
        }
        
        // Verificar que venga el ID
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'ID de pedido no especificado'
            ];
            header('Location: /pedidos');
            exit;
        }
        
        $id = (int)$_POST['id'];
        
        try {
            // Buscar el pedido
            $pedido = Pedidos::find($id);
            
            if (!$pedido) {
                throw new Exception('Pedido no encontrado');
            }
            
            // Usar el método SQL de ActiveRecord para las consultas
            // 1. Eliminar productos asociados al pedido
            $resultado_productos = PedidoProducto::ejecutarSQL("DELETE FROM pedido_productos WHERE id_pedido = $id");
            
            if ($resultado_productos === false) {
                throw new Exception('Error al eliminar productos del pedido'. self::$db->error);
            }
            
            // 2. Eliminar el pedido usando el método eliminar() de ActiveRecord
            $resultado_pedido = PedidoProducto::ejecutarSQL("DELETE FROM pedidos WHERE id = $id");
            
            if (!$resultado_pedido) {
                throw new Exception('Error al eliminar el pedido' . self::$db->error);
            }
            
            $_SESSION['alerta'] = [
                'tipo' => 'exito',
                'mensaje' => 'Pedido con Folio | ' .$pedido->codigo_pedido . ' | eliminado correctamente'
            ];
            
        } catch (Exception $e) {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'Error al eliminar pedido con Folio ' .$pedido->codigo_pedido . ': ' . $e->getMessage()
            ];
        }
        
        header('Location: /pedidos');
        exit;
    }

}