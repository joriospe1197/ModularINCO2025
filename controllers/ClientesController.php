<?php

namespace Controllers;

use Model\Pedidos;
use MVC\Router;
use Model\Usuario;
use Model\Productos;
use Model\PedidoProducto;
use Model\ActiveRecord;
use Model\Clientes;
use Exception;

class ClientesController
{
    //listar o vista principal funcionando en pedidos en dashboard
    public static function listar(Router $router)
    {
        session_start();
        isAuth();
        $clientes = Clientes::all();

        $router->render('dashboard/clientes', [
            'titulo' => 'Lista de Clientes',
            'clientes' => $clientes //  Pasar el array correcto
        ]);
    }
    public static function agregar(Router $router)
    {
        session_start();
        isAuth();


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validación de cliente
            $nombre_cliente = $_POST['nombre_cliente'] ?? null;
            $domicilio_cliente = $_POST['domicilio_cliente'] ?? null;
            $estado = $_POST['estado'] ?? null;
            $municipio = $_POST['municipio'] ?? null;
            $codigo_postal = $_POST['codigo_postal'] ?? null;
            $telefono_cliente = $_POST['telefono_cliente'] ?? null;
            $correo_electronico = $_POST['correo_electronico'] ?? null;

            if (empty($nombre_cliente) || empty($domicilio_cliente) || empty($estado) || empty($municipio) || empty($codigo_postal) || empty($telefono_cliente) || empty($correo_electronico)) {
                $alertas['error'][] = 'Error al guardar el cliente. No pueden existir campos vacíos.';
            }
            $resultado = Clientes::findByName($nombre_cliente);
            if (!empty($resultado)) {
                $alertas['error'][] = 'El cliente ya esta registrado en la base de datos.';
            } else {
                $resultado = Clientes::registerClient($nombre_cliente, $domicilio_cliente, $estado, $municipio, $codigo_postal, $telefono_cliente, $correo_electronico);
                if ($resultado) {

                    $_SESSION['alerta'] = [
                        'tipo' => 'exito',
                        'mensaje' => 'Cliente registrado correctamente'
                    ];
                    header('Location: /clientes/agregar');
                    return;
                } else {
                    $alertas['error'][] = 'Error al guardar el cliente. Verifica los datos.';
                }
            }
        }

        $router->render('clientes/agregar', [
            'titulo' => 'Nuevo Cliente',
            'alertas' => $alertas
        ]);
    }
    public static function ver(Router $router)
    {
        session_start();
        isAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /pedidos');
            exit;
        }
        $cliente = Clientes::findById($id);
        if (!$cliente) {
            
            header('Location: /clientes');
            exit;
        }



        $router->render('clientes/ver', [
            'titulo' => 'Detalles del Cliente',
            'cliente' => $cliente,

        ]);
    }
    public static function editar(Router $router)
    {
        session_start();
        isAuth();

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: /clientes');
            exit;
        }

        $id = (int)$_GET['id'];
        $cliente = Clientes::findById($id);
        $alertas = [];

        if (!$cliente) {
            $alertas['error'][] = 'El cliente no existe';
            header('Location: /clientes');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $nombre_cliente = $_POST['nombre_cliente'] ?? null;
            $domicilio_cliente = $_POST['domicilio_cliente'] ?? null;
            $estado = $_POST['estado'] ?? null;
            $municipio = $_POST['municipio'] ?? null;
            $codigo_postal = $_POST['codigo_postal'] ?? null;
            $telefono_cliente = $_POST['telefono_cliente'] ?? null;
            $correo_electronico = $_POST['correo_electronico'] ?? null;
            $resultado = Clientes::updateData($id,$nombre_cliente, $domicilio_cliente, $estado, $municipio, $codigo_postal, $telefono_cliente, $correo_electronico);
            if ($resultado) {

                $_SESSION['alerta'] = [
                    'tipo' => 'exito',
                    'mensaje' => 'Información del cliente actualizada correctamente'
                ];
                header('Location: /clientes/editar');
                return;
            } else {
                $alertas['error'][] = 'Error al guardar el cliente. Verifica los datos.';
            }
        }

        $router->render('clientes/editar', [
            'titulo' => 'Editar Cliente',
            'cliente' => $cliente,
            'alertas' => $alertas
        ]);
    }
    public static function eliminar() {
        session_start();
        isAuth();
        
        // VERIFICAR PERMISOS (solo admin puede eliminar)
        if ($_SESSION['tipo_usuario'] != 1) {
            echo json_encode([
                'success' => false, 
                'error' => 'No tienes permisos para eliminar manifiestos'
            ]);
            return;
        }
        
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }
        
        try {
            $resultado = Clientes::eliminarCliente($id);
            
            if ($resultado) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al eliminar el manifiesto o el manifiesto no existe']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
