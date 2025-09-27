<?php
namespace Model;

use Model\ActiveRecord;

class Clientes extends ActiveRecord {
    protected static $tabla = 'clientes';
    protected static $columnasDB = [
        'id',
        'razon_social',
        'domicilio',
        'municipio',
        'estado',
        'codigo_postal',
        'correo_electronico',
        'telefono'
    ];
    protected static $idCampo = 'id'; // Asegurar que use 'id' como clave primaria

    public $id;
    public $razon_social;
    public $domicilio;
    public $municipio;
    public $estado;
    public $codigo_postal;
    public $correo_electronico;
    public $telefono;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->razon_social = $args['razon_social'] ?? '';
        $this->domicilio = $args['domicilio'] ?? '';
        $this->municipio = $args['municipio'] ?? '';
        $this->estado = $args['estado'] ?? '';
        $this->codigo_postal = $args['codigo_postal'] ?? '';
        $this->correo_electronico = $args['correo_electronico'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
    }

    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        return self::consultarSQL($query);
    }

    
    public static function findById($id) {
        $query = "SELECT * FROM clientes WHERE id = {$id}";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }
}
