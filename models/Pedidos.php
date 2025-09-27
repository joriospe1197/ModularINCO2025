<?php
namespace Model;

use Model\ActiveRecord;
use Model\Clientes;

class Pedidos extends ActiveRecord {
    protected static $tabla = 'pedidos';
    protected static $columnasDB = [
        'id',
        'codigo_pedido',
        'id_empleado_registra',
        'id_empleado_chofer',
        'fecha_pedido',
        'id_cliente',           //  Nuevo campo (FK a clientes)
        'nombre_cliente',
        'domicilio_cliente',
        'telefono_cliente',
        'observaciones',
        'servicio',
        'estado',
        'gastos',
        'costo',
        'pagados',
        'almacen',
        'depositos'
    ];

    public $id;
    public $codigo_pedido;
    public $id_empleado_registra;
    public $id_empleado_chofer;
    public $fecha_pedido;
    public $id_cliente;          //  Nueva propiedad
    public $estado;
    public $nombre_cliente;
    public $domicilio_cliente;
    public $telefono_cliente;
    public $servicio;
    public $observaciones;
    public $gastos;
    public $costo;
    public $pagados;
    public $almacen;
    public $depositos;

    // Estas dos son necesarias para reflejar los alias del JOIN
    public $empleado_registra;
    public $chofer;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->codigo_pedido = $args['codigo_pedido'] ?? '';
        $this->id_empleado_registra = $args['id_empleado_registra'] ?? null;
        $this->id_empleado_chofer = $args['id_empleado_chofer'] ?? null;
        $this->fecha_pedido = $args['fecha_pedido'] ?? '';
        $this->id_cliente = $args['id_cliente'] ?? null;  
        $this->estado = $args['estado'] ?? '';
        $this->nombre_cliente = $args['nombre_cliente'] ?? '';
        $this->domicilio_cliente = $args['domicilio_cliente'] ?? '';
        $this->telefono_cliente = $args['telefono_cliente'] ?? '';
        $this->empleado_registra = $args['empleado_registra'] ?? '';
        $this->chofer = $args['chofer'] ?? '';
        $this->servicio = $args['servicio'] ?? '';
        $this->observaciones = $args['observaciones'] ?? '';
        $this->gastos = $args['gastos'] ?? 0;
        $this->costo = $args['costo'] ?? 0;
        $this->pagados = $args['pagados'] ?? 0;
        $this->almacen = $args['almacen'] ?? 0;
        $this->depositos = $args['depositos'] ?? 0;

        // PROCESAR CLIENTE DESPUÉS DE ASIGNAR TODAS LAS PROPIEDADES
        $this->procesarCliente();
    }

    public static function obtenerTodos() {
        $query = "SELECT p.id, p.codigo_pedido, p.id_empleado_registra, 
                e_reg.nombre AS empleado_registra, 
                e_chofer.nombre AS chofer, p.fecha_pedido, p.estado,
                p.id_cliente, p.servicio,
                p.nombre_cliente, p.domicilio_cliente, p.telefono_cliente,
                p.observaciones
                FROM pedidos p
                JOIN empleados e_reg ON p.id_empleado_registra = e_reg.idempleado
                LEFT JOIN empleados e_chofer ON p.id_empleado_chofer = e_chofer.idempleado
                ORDER BY p.fecha_pedido DESC";
        
        return self::consultarSQL($query);
    }

    public static function obtenerPorChofer($id_chofer) {
        $query = "SELECT p.id, p.codigo_pedido as folio, p.fecha_pedido as fecha, 
                        p.nombre_cliente as cliente, p.gastos, p.costo, p.pagados,
                        p.almacen, p.depositos,
                        GROUP_CONCAT(pr.descripcion SEPARATOR ', ') as servicios
                FROM pedidos p
                LEFT JOIN pedido_productos pp ON p.id = pp.id_pedido
                LEFT JOIN productos pr ON pp.idproducto = pr.idproducto
                WHERE p.id_empleado_chofer = {$id_chofer}
                GROUP BY p.id
                ORDER BY p.fecha_pedido DESC";
        
        return self::consultarSQL($query);
    }
    
    public static function obtenerPorChoferYFechas($id_chofer, $fecha_inicio, $fecha_fin) {
        $id_chofer = (int)$id_chofer;
        
        $query = "SELECT 
                    p.id,
                    p.codigo_pedido, 
                    p.fecha_pedido, 
                    CASE 
                        WHEN c.id IS NOT NULL THEN c.razon_social 
                        ELSE p.nombre_cliente 
                    END as nombre_cliente,
                    p.gastos, 
                    p.costo, 
                    p.pagados, 
                    p.almacen, 
                    p.depositos,
                    p.servicio 
                FROM pedidos p
                LEFT JOIN clientes c ON p.id_cliente = c.id
                WHERE p.id_empleado_chofer = {$id_chofer}
                AND p.fecha_pedido BETWEEN '{$fecha_inicio}' AND '{$fecha_fin}'
                ORDER BY p.fecha_pedido DESC";
        
        return self::consultarSQL($query);
    }
    
    public static function find($id) {
        $query = "SELECT p.id, p.codigo_pedido, p.id_empleado_registra, 
                        p.id_empleado_chofer, p.fecha_pedido, p.estado,
                        p.id_cliente, p.servicio,
                        p.nombre_cliente, p.domicilio_cliente, p.telefono_cliente,
                        p.observaciones,
                        e_reg.nombre AS empleado_registra, 
                        e_chofer.nombre AS chofer
                FROM pedidos p
                JOIN empleados e_reg ON p.id_empleado_registra = e_reg.idempleado
                LEFT JOIN empleados e_chofer ON p.id_empleado_chofer = e_chofer.idempleado
                WHERE p.id = {$id}";
        
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    public function guardarConProductos($productos, $cantidades) {
        self::$db->begin_transaction();
        
        try {
            // CONVERTIR id_cliente VACÍO A NULL
            $this->procesarCliente();

            $resultado = $this->guardar();
            
            if (!$resultado) {
                throw new Exception("Error al guardar pedido: " . self::$db->error);
            }
            
            $id_pedido = self::$db->insert_id;
            
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

        //  VALIDACIÓN MEJORADA - PROCESAR PRIMERO
        $this->procesarCliente();
        
        if ($this->id_cliente) {
            // Cliente frecuente: verificar que existe
            $cliente = Clientes::findById($this->id_cliente);
            if (!$cliente) {
                $alertas['error'][] = 'El cliente seleccionado no existe';
            }
        } else {
            // Cliente ocasional: validar campos obligatorios
            if (empty(trim($this->nombre_cliente))) {
                $alertas['error'][] = 'El nombre del cliente es obligatorio';
            }
            if (empty(trim($this->domicilio_cliente))) {
                $alertas['error'][] = 'El domicilio del cliente es obligatorio';
            }
            
            // Opcional: limpiar datos de cliente ocasional si no se usan
            if ($this->id_cliente === null) {
                $this->nombre_cliente = trim($this->nombre_cliente);
                $this->domicilio_cliente = trim($this->domicilio_cliente);
                $this->telefono_cliente = trim($this->telefono_cliente);
            }
        }

        return $alertas;
    }

    public function actualizarProductos($productos, $cantidades) {
        self::$db->begin_transaction();
        
        try {
             //PROCESAR CLIENTE ANTES DE ACTUALIZAR
            $this->procesarCliente();
            
            // Guardar cambios del pedido primero
            $this->guardar();



            $query_delete = "DELETE FROM pedido_productos WHERE id_pedido = {$this->id}";
            self::$db->query($query_delete);
            
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


    public function procesarCliente() {
        // Convertir id_cliente vacío o 0 a NULL
        if (empty($this->id_cliente) || $this->id_cliente === '0') {
            $this->id_cliente = null;
        }
        
        // Si es cliente ocasional (id_cliente = NULL), asegurar que los campos snapshot no estén vacíos
        if ($this->id_cliente === null) {
            $this->nombre_cliente = trim($this->nombre_cliente);
            $this->domicilio_cliente = trim($this->domicilio_cliente);
            $this->telefono_cliente = trim($this->telefono_cliente);
        } else {
            // Si es cliente frecuente, limpiar los campos snapshot
            $this->nombre_cliente = '';
            $this->domicilio_cliente = '';
            $this->telefono_cliente = '';
        }
    }

    public function validarDatosFinancieros() {
        $alertas = [];
        
        if ($this->gastos < 0) {
            $alertas['error'][] = 'Los gastos no pueden ser negativos';
        }
        
        if ($this->costo < 0) {
            $alertas['error'][] = 'El costo no puede ser negativo';
        }
        
        if ($this->pagados < 0) {
            $alertas['error'][] = 'El monto pagado no puede ser negativo';
        }
        
        if ($this->almacen < 0) {
            $alertas['error'][] = 'El valor de almacén no puede ser negativo';
        }
        
        if ($this->depositos < 0) {
            $alertas['error'][] = 'El valor de depósitos no puede ser negativo';
        }
        
        return $alertas;
    }

    public function capturarDatosFinancieros($datos) {
        $this->sincronizar($datos);
        return $this->validarDatosFinancieros();
    }

    // Método para ejecutar consultas SQL genéricas
    public static function ejecutarSQL($query) {
        try {
            $resultado = self::$db->query($query);
            
            if (!$resultado) {
                error_log("Error en consulta SQL: " . self::$db->error);
                return [];
            }
            
            $datos = [];
            while ($fila = $resultado->fetch_object()) {
                $datos[] = $fila;
            }
            
            $resultado->free();
            return $datos;
            
        } catch (Exception $e) {
            error_log("ERROR en ejecutarSQL: " . $e->getMessage());
            return [];
        }
    }

}