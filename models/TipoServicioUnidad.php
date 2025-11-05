<?php
namespace Model;

use Model\ActiveRecord;

class TipoServicioUnidad extends ActiveRecord {
    protected static $tabla = 'tipos_servicio_unidad';
    protected static $columnasDB = ['id_tipo_servicio', 'nombre_servicio', 'descripcion', 'intervalo_meses'];

    public $id_tipo_servicio;
    public $nombre_servicio;
    public $descripcion;
    public $intervalo_meses;

    public function __construct($args = []) {
        $this->id_tipo_servicio = $args['id_tipo_servicio'] ?? null;
        $this->nombre_servicio = $args['nombre_servicio'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->intervalo_meses = $args['intervalo_meses'] ?? 0;
    }

    public static function todos() {
        $query = "SELECT * FROM tipos_servicio_unidad ORDER BY nombre_servicio";
        return self::consultarSQL($query);
    }

        // Método para buscar por id_tipo_servicio
    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE id_tipo_servicio = {$id}";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }
}