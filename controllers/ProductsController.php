<?php

namespace Controllers;

use Model\Productos;
use MVC\Router;

class ProductsController {
    public static function index(Router $router) {
        session_start();
        isAuth();
        
        $alertas = [];
        $productos = Productos::all();
        
        // Si hay una búsqueda, filtrar productos
        if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
            $busqueda = $_GET['busqueda'];
            $productos = array_filter($productos, function($producto) use ($busqueda) {
                return stripos($producto->descripcion, $busqueda) !== false;
            });
        }
        
        $router->render('dashboard/productos', [
            'titulo' => 'Productos',
            'productos' => $productos,
            'alertas' => $alertas
        ]);
    }

    public static function register_product(Router $router) {
        session_start();
        isAuth(); // Verificar que el usuario esté autenticado

        $alertas = [];
        $producto = new Productos(); // Instanciamos el modelo de Producto

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sincronizamos los datos del formulario con el objeto producto
            $producto->sincronizar($_POST);
        
            // Realizar la validación
            $alertas = $producto->validarProducto();

             // Si no hay alertas, proceder con el registro
             if (empty($alertas)) {
                // Verificar si la descripción ya existe
                $existeProducto = Productos::where('descripcion', $producto->descripcion);
                if ($existeProducto) {
                    Productos::setAlerta('error', 'Ya hay un producto con esa descripción');
                    $alertas = Productos::getAlertas();
                }

                // Si no hay alertas, procedemos a guardar el producto
                if (empty($alertas)) {
                    $resultado = $producto->guardar();
            
                    if ($resultado) {
                        header('Location: /product_success_message'); // Redirige a la lista de productos
                        exit;
                    } else {
                        Productos::setAlerta('error', 'Hubo un error al guardar el producto');
                        $alertas = Productos::getAlertas();
                    }
                }
            }
        
            
        }
        
        $router->render('products/register_product', [
            'titulo' => 'Registrar Producto',
            'productos' => $producto,
            'alertas' => $alertas 
        ]);
        
    }

    public static function product_success_message(Router $router) {
        session_start();
        isAuth();
        $router->render('products/product_success_message', [
            'titulo' => 'Registrar Producto'
        ]);
    }

    public static function search_product(Router $router){
        session_start();
        isAuth(); 
        $alertas = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Crear un objeto de la unidad de transporte con los datos del formulario
            $Productos = new Productos($_POST);
    
            // Validar que el idunidad haya sido ingresado correctamente
            $alertas = $Productos->validarIdProducto();  
    
            if (empty($alertas)) {
                // Buscar la unidad de transporte en la base de datos usando el ID ingresado
                $producto = Productos::where('idproducto', $Productos->idproducto);  // Buscar unidad por idunidad
    
                if (!$producto) { // Si no se encuentra la unidad
                    Productos::setAlerta('error', 'El producto con este ID no existe');
                } else {
                    // Si la unidad existe, redirige al formulario de edición pasando el idunidad
                    header('Location: /edit_product?idproducto=' . $producto->idproducto);
                    exit;  
                }
            }
        }
         // Si no es un POST, simplemente renderizamos la vista con las alertas
        $alertas = Productos::getAlertas();

        // Renderizar la vista
        $router->render('products/search_product', [
            'titulo' => 'Buscando producto',
            'alertas' => $alertas,
        ]);
    }

    public static function edit_product(Router $router) {
        session_start();
        isAuth();
        $alertas = [];
    
        // Obtener producto a editar
        $idproducto = $_GET['idproducto'] ?? '';
        $producto = Productos::where('idproducto', $idproducto);
    
        if (!$producto) {
            // Si no se encuentra el producto
            header('Location: /search_product');
            exit;
        }
    
        // Validar si el formulario fue enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar qué llega en el POST
             // var_dump($_POST);  Aquí verás lo que está llegando en el POST
    
            // Sincronizamos los datos (actualizar solo los campos modificados)
            $producto->descripcion = $_POST['descripcion'] ?? $producto->descripcion;
            $producto->precio = $_POST['precio'] ?? $producto->precio;
    
            // Validar que los campos no estén vacíos
            $alertas = $producto->validarProducto();
    
            // Guardar si no hay errores
            if (empty($alertas)) {
                $resultado = $producto->guardar();
    
                if ($resultado['resultado']) {
                    header('Location: /message_update_product');
                    exit;
                } else {
                    $alertas['error'][] = 'Hubo un problema al actualizar el producto';
                }
            }
        }
    
        // Renderizar la vista de edición
        $router->render('products/edit_product', [
            'titulo' => 'Editar Producto',
            'producto' => $producto,
            'alertas' => $alertas
        ]);
    }
    

    public static function message_update_product(Router $router){
        session_start();
        isAuth();
        $router->render('products/message_update_product', [
            'titulo' => 'Producto actualizado exitosamente'
        ]);
    }

    public static function remove_product(Router $router) {
        session_start();
        isAuth(); 
        $alertas = [];
    
        $productos = Productos::all();
    
        // Verificar permisos
        if ($_SESSION['tipo_usuario'] != 1) {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'No tienes permisos para eliminar productos'
            ];
            header('Location: /productos');
            exit;
        }
    
        // Si hay idproducto por GET
        if (isset($_GET['idproducto']) && $_GET['idproducto']) {
            $idproducto = intval($_GET['idproducto']);
            $producto = Productos::where('idproducto', $idproducto);
    
            if ($producto) {
                // ✅ Obtener conexión desde el modelo (válido porque hereda de ActiveRecord)
                $db = $producto->db;
    
                // Verificar si el producto está en algún pedido
                $query = "SELECT COUNT(*) AS total FROM pedido_productos WHERE idproducto = {$idproducto}";
                $resultado = $db->query($query);
                $row = $resultado->fetch_assoc();
    
                if ($row['total'] > 0) {
                    $_SESSION['alerta'] = [
                        'tipo' => 'error',
                        'mensaje' => '⚠️ No se puede eliminar el producto "' . 
                                     $producto->descripcion . 
                                     '" porque se encuentra dentro de un pedido activo. ' .
                                     'Primero elimina o actualiza el pedido asociado.'
                    ];
                } else {
                    // Eliminar si no está vinculado
                    $producto->eliminar();
                    $_SESSION['alerta'] = [
                        'tipo' => 'exito',
                        'mensaje' => '✅ Producto "' . $producto->descripcion . '" eliminado correctamente.'
                    ];
                }
    
                header('Location: /remove_product');
                exit;
            } else {
                Productos::setAlerta('error', 'El producto no existe');
            }
        }
    
        $alertas = Productos::getAlertas();
    
        $router->render('products/remove_product', [
            'titulo' => 'Eliminar producto',
            'alertas' => $alertas,
            'productos' => $productos
        ]);
    }
      
}


