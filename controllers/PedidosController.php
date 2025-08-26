<?php
namespace Controllers;

use Model\Pedidos;
use Model\Empleados;
use MVC\Router;
use Model\Usuario;
use Model\Productos;
use Model\PedidoProducto;
use Model\ActiveRecord;
use Exception;

class PedidosController {
    //listar o vista principal funcionando en pedidos en dashboard
    public static function listar(Router $router) {
        session_start();
        isAuth();
        
        $pedidos = Pedidos::obtenerTodos();
        $router->render('dashboard/pedidos', [
            'titulo' => 'Lista de Pedidos',
            'pedidos' => $pedidos
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
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $pedido->sincronizar($_POST);
            $alertas = $pedido->validar();
            
            if (empty($alertas['error'])) {
                // Generar código de pedido
                $pedido->codigo_pedido = self::generarCodigoPedido($pedido->fecha_pedido);

                if (!isset($_SESSION['idempleado'])) {
                    throw new Exception('Usuario no autenticado o sesión inválida');
                }
                $pedido->id_empleado_registra = $_SESSION['idempleado'];
                $pedido->estado = 'pendiente';

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

            // Actualizar el estado
            $pedido->estado = $nuevoEstado;
            $resultado = $pedido->guardar();

            // guardar() devuelve un array, verificar el resultado
            if (!$resultado || (isset($resultado['resultado']) && !$resultado['resultado'])) {
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

        // Obtener los productos del pedido
        $productos = PedidoProducto::obtenerProductosPorPedido($id);
        
        $router->render('pedidos/ver', [
            'titulo' => 'Detalles del Pedido',
            'pedido' => $pedido,
            'productos' => $productos
            
        ]);
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
        
        $alertas = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sincronizar con POST data
            $pedido->sincronizar($_POST);
            $alertas = $pedido->validar();
            
            if (empty($alertas['error'])) {
                // Actualizar pedido
                $resultado = $pedido->guardar();
                
                if ($resultado) {
                    // Actualizar productos del pedido
                    $pedido->actualizarProductos($_POST['productos'] ?? [], $_POST['cantidades'] ?? []);
                    
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
        
        $router->render('pedidos/editar', [
            'titulo' => 'Editar Pedido',
            'pedido' => $pedido,
            'choferes' => $choferes,
            'productos' => $productos,
            'productos_pedido' => $productos_pedido,
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