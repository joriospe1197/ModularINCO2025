<?php
namespace Model;

use Model\ActiveRecord;

class Pedidos extends ActiveRecord {
    protected static $tabla = 'pedidos';
    protected static $columnasDB = [
        'id',
        'codigo_pedido',
        'id_empleado_registra',
        'id_empleado_chofer',
        'fecha_pedido',
        'nombre_cliente',
        'domicilio_cliente',
        'telefono_cliente',
        'observaciones',
        'estado'
    ];

    public $id;
    public $codigo_pedido;
    public $id_empleado_registra;
    public $id_empleado_chofer;
    public $fecha_pedido;
    public $estado;
    public $nombre_cliente;
    public $domicilio_cliente;
    public $telefono_cliente;
    public $observaciones;

    // Estas dos son necesarias para reflejar los alias del JOIN
    public $empleado_registra;
    public $chofer;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->codigo_pedido = $args['codigo_pedido'] ?? '';
        $this->id_empleado_registra = $args['id_empleado_registra'] ?? null;
        $this->id_empleado_chofer = $args['id_empleado_chofer'] ?? null;
        $this->fecha_pedido = $args['fecha_pedido'] ?? '';
        $this->estado = $args['estado'] ?? '';
        $this->nombre_cliente = $args['nombre_cliente'] ?? '';
        $this->domicilio_cliente = $args['domicilio_cliente'] ?? '';
        $this->telefono_cliente = $args['telefono_cliente'] ?? '';
        $this->empleado_registra = $args['empleado_registra'] ?? '';
        $this->chofer = $args['chofer'] ?? '';
        $this->observaciones = $args['observaciones'] ?? '';
    }

    public static function obtenerTodos() {
        $query = "SELECT p.id, p.codigo_pedido, e_reg.nombre AS empleado_registra, 
                 e_chofer.nombre AS chofer, p.fecha_pedido, p.estado,
                 p.nombre_cliente, p.domicilio_cliente, p.telefono_cliente,
                 p.observaciones
                 FROM pedidos p
                 JOIN empleados e_reg ON p.id_empleado_registra = e_reg.idempleado
                 LEFT JOIN empleados e_chofer ON p.id_empleado_chofer = e_chofer.idempleado
                 ORDER BY p.fecha_pedido DESC";
        
        return self::consultarSQL($query);
    }

    ///** Buscar un pedido por ID
 
    public static function find($id) {
        $query = "SELECT p.id, p.codigo_pedido, p.id_empleado_registra, 
                        p.id_empleado_chofer, p.fecha_pedido, p.estado,
                        p.nombre_cliente, p.domicilio_cliente, p.telefono_cliente,
                        p.observaciones,
                        e_reg.nombre AS empleado_registra, 
                        e_chofer.nombre AS chofer
                FROM pedidos p
                JOIN empleados e_reg ON p.id_empleado_registra = e_reg.idempleado
                LEFT JOIN empleados e_chofer ON p.id_empleado_chofer = e_chofer.idempleado
                WHERE p.id = {$id}";
        
        $resultado = self::consultarSQL($query);
        return array_shift($resultado); // Devuelve el primer elemento o null
    }


    public function guardarConProductos($productos, $cantidades) {
        self::$db->begin_transaction();
        
        try {
            $resultado = $this->guardar();
            
            if (!$resultado) {
                throw new Exception("Error al guardar pedido: " . self::$db->error);
            }
            
            // Obtener el ID del pedido recién insertado
            $id_pedido = self::$db->insert_id;
            
            // Guardar productos
            foreach ($productos as $index => $idproducto) {
                if (!empty($idproducto)) {
                    $cantidad = $cantidades[$index] ?? 1;
                    
                    $query = "INSERT INTO pedido_productos (id_pedido, idproducto, cantidad) 
                            VALUES ($id_pedido, $idproducto, $cantidad)";
                    
                    $result = self::$db->query($query);
                    
                    if (!$result) {
                        throw new Exception("Error al guardar producto: " . self::$db->error);
                    }
                }
            }
            
            self::$db->commit();
            return true;
            
        } catch (Exception $e) {
            self::$db->rollback();
            error_log("Error en guardarConProductos: " . $e->getMessage());
            self::$alertas['error'][] = $e->getMessage();
            return false;
        }
    }


    public function validar() {
        $alertas = [];
        
        if (!$this->id_empleado_chofer) {
            $alertas['error'][] = 'Seleccione un chofer';
        }
        if (!$this->fecha_pedido) {
            $alertas['error'][] = 'La fecha del pedido es obligatoria';
        }
        if (!$this->nombre_cliente) {
            $alertas['error'][] = 'El nombre del cliente es obligatorio';
        }
        if (!$this->domicilio_cliente) {
            $alertas['error'][] = 'El domicilio del cliente es obligatorio';
        }
        
        return $alertas;
    }

    public function actualizarProductos($productos, $cantidades) {
        self::$db->begin_transaction();
        
        try {
            // Eliminar productos actuales
            $query_delete = "DELETE FROM pedido_productos WHERE id_pedido = {$this->id}";
            self::$db->query($query_delete);
            
            // Insertar nuevos productos
            foreach ($productos as $index => $idproducto) {
                if (!empty($idproducto)) {
                    $cantidad = $cantidades[$index] ?? 1;
                    
                    $query = "INSERT INTO pedido_productos (id_pedido, idproducto, cantidad) 
                            VALUES ({$this->id}, {$idproducto}, {$cantidad})";
                    
                    $result = self::$db->query($query);
                    
                    if (!$result) {
                        throw new Exception("Error al guardar producto: " . self::$db->error);
                    }
                }
            }
            
            self::$db->commit();
            return true;
            
        } catch (Exception $e) {
            self::$db->rollback();
            error_log("Error en actualizarProductos: " . $e->getMessage());
            return false;
        }
    }
}