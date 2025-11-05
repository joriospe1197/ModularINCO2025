<?php
namespace Model;

use Model\ActiveRecord;

class ServicioUnidad extends ActiveRecord {
    protected static $tabla = 'servicios_unidades';
    protected static $columnasDB = ['id_servicio', 'idunidad', 'id_tipo_servicio', 'fecha_servicio', 'descripcion_servicio', 'siguiente_servicio', 'estado', 'created_at'];

    public $id_servicio;
    public $idunidad;
    public $id_tipo_servicio;
    public $fecha_servicio;
    public $descripcion_servicio;
    public $siguiente_servicio;
    public $estado;
    public $created_at;

    // Propiedades para JOINs
    public $nombre_servicio;
    public $intervalo_meses;
    public $modelo;
    public $placas;
    public $chofer_nombre;
    public $dias_restantes;

    public function __construct($args = []) {
        $this->id_servicio = $args['id_servicio'] ?? null;
        $this->idunidad = $args['idunidad'] ?? '';
        $this->id_tipo_servicio = $args['id_tipo_servicio'] ?? '';
        $this->fecha_servicio = $args['fecha_servicio'] ?? '';
        $this->descripcion_servicio = $args['descripcion_servicio'] ?? '';
        $this->siguiente_servicio = $args['siguiente_servicio'] ?? '';
        $this->estado = $args['estado'] ?? 'pendiente'; //estado pendietne por defecto
        $this->created_at = $args['created_at'] ?? '';

        // Inicializar propiedades de JOIN
        $this->nombre_servicio = $args['nombre_servicio'] ?? '';
        $this->intervalo_meses = $args['intervalo_meses'] ?? 0;
        $this->modelo = $args['modelo'] ?? '';
        $this->placas = $args['placas'] ?? '';
        $this->chofer_nombre = $args['chofer_nombre'] ?? '';
        $this->dias_restantes = $args['dias_restantes'] ?? 0;
    }

    public function validarServicio() {
        if (!$this->idunidad) {
            self::$alertas['error'][] = 'La unidad es obligatoria';
        }
        if (!$this->id_tipo_servicio) {
            self::$alertas['error'][] = 'El tipo de servicio es obligatorio';
        }
        if (!$this->fecha_servicio) {
            self::$alertas['error'][] = 'La fecha del servicio es obligatoria';
        }
        
        return self::$alertas;
    }

    public function guardarManual() {
        // Sanitizar los datos manualmente
        $idunidad = self::$db->escape_string($this->idunidad);
        $id_tipo_servicio = self::$db->escape_string($this->id_tipo_servicio);
        $fecha_servicio = self::$db->escape_string($this->fecha_servicio);
        $descripcion_servicio = self::$db->escape_string($this->descripcion_servicio ?: '');
        $siguiente_servicio = self::$db->escape_string($this->siguiente_servicio);
        $estado = self::$db->escape_string($this->estado);

        // Construir la query manualmente
        $query = "INSERT INTO servicios_unidades (idunidad, id_tipo_servicio, fecha_servicio, descripcion_servicio, siguiente_servicio, estado) VALUES (";
        $query .= "'" . $idunidad . "', ";
        $query .= "'" . $id_tipo_servicio . "', ";
        $query .= "'" . $fecha_servicio . "', ";
        $query .= "'" . $descripcion_servicio . "', ";
        $query .= "'" . $siguiente_servicio . "', ";
        $query .= "'" . $estado . "')";
        
        // DEBUG: Mostrar la query
        // echo "<pre>Query manual: " . $query . "</pre>";
        
        // Ejecutar la query
        $resultado = self::$db->query($query);
        
        if ($resultado) {
            // Obtener el ID del nuevo registro
            $this->id_servicio = self::$db->insert_id;
            return [
                'resultado' => true,
                'id' => $this->id_servicio
            ];
        } else {
            return [
                'resultado' => false,
                'error' => self::$db->error
            ];
        }
    }

    // Calcular siguiente servicio basado en intervalo
    public function calcularSiguienteServicio($intervalo_meses) {
        $fecha_actual = new \DateTime($this->fecha_servicio);
        $fecha_actual->modify("+{$intervalo_meses} months");
        $this->siguiente_servicio = $fecha_actual->format('Y-m-d');
    }

    public static function obtenerServiciosPorUnidad($idunidad) {
        $query = "SELECT s.*, 
                        t.nombre_servicio, 
                        t.intervalo_meses,
                        u.modelo, 
                        u.placas
                FROM servicios_unidades s
                LEFT JOIN tipos_servicio_unidad t ON s.id_tipo_servicio = t.id_tipo_servicio
                LEFT JOIN unidades_de_transporte u ON s.idunidad = u.idunidad
                WHERE s.idunidad = {$idunidad}
                ORDER BY s.fecha_servicio DESC";
        
        return self::consultarSQL($query);
    }

    public static function obtenerServiciosPendientes() {
        $query = "SELECT s.*, 
                         t.nombre_servicio, 
                         t.intervalo_meses,
                         u.modelo, 
                         u.placas, 
                         e.nombre as chofer_nombre,
                         DATEDIFF(s.siguiente_servicio, CURDATE()) as dias_restantes
                 FROM servicios_unidades s
                 LEFT JOIN tipos_servicio_unidad t ON s.id_tipo_servicio = t.id_tipo_servicio
                 LEFT JOIN unidades_de_transporte u ON s.idunidad = u.idunidad
                 LEFT JOIN empleados e ON u.chofer = e.idempleado
                 WHERE s.estado IN ('pendiente', 'programado') AND s.siguiente_servicio <= CURDATE()
                 ORDER BY s.siguiente_servicio ASC";
        
        return self::consultarSQL($query);
    }

    public static function obtenerProximosServicios() {
        $query = "SELECT s.*, 
                         t.nombre_servicio, 
                         t.intervalo_meses,
                         u.modelo, 
                         u.placas, 
                         e.nombre as chofer_nombre,
                         DATEDIFF(s.siguiente_servicio, CURDATE()) as dias_restantes
                 FROM servicios_unidades s
                 LEFT JOIN tipos_servicio_unidad t ON s.id_tipo_servicio = t.id_tipo_servicio
                 LEFT JOIN unidades_de_transporte u ON s.idunidad = u.idunidad
                 LEFT JOIN empleados e ON u.chofer = e.idempleado
                 WHERE s.estado IN ('pendiente', 'programado') AND s.siguiente_servicio > CURDATE()
                 ORDER BY s.siguiente_servicio ASC";
        
        return self::consultarSQL($query);
    }

    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE id_servicio = {$id}";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Método para cambiar el estado
    public function cambiarEstado($nuevo_estado) {
        $estados_permitidos = ['pendiente', 'programado', 'completado'];
        
        if (in_array($nuevo_estado, $estados_permitidos)) {
            $this->estado = $nuevo_estado;
            return $this->actualizarManual(); // Usar actualizar en lugar de guardar
        }
        
        return ['resultado' => false, 'error' => 'Estado no válido'];
    }

    public function actualizarManual() {
        // Sanitizar los datos
        $id_servicio = self::$db->escape_string($this->id_servicio);
        $estado = self::$db->escape_string($this->estado);

        // Construir la query UPDATE
        $query = "UPDATE servicios_unidades SET estado = '" . $estado . "' WHERE id_servicio = '" . $id_servicio . "'";
        
        // Ejecutar la query
        $resultado = self::$db->query($query);
        
        if ($resultado) {
            return [
                'resultado' => true,
                'id' => $this->id_servicio
            ];
        } else {
            return [
                'resultado' => false,
                'error' => self::$db->error
            ];
        }
    }


}
