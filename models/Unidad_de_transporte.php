<?php

namespace Model;

use Model\ActiveRecord;

class Unidad_de_transporte extends ActiveRecord {
    protected static $tabla = 'unidades_de_transporte';
    protected static $columnasDB = ['idunidad', 'modelo', 'placas', 'chofer', 'url'];

    public function __construct($args = []) {
        $this->idunidad = $args['idunidad'] ?? null;
        $this->modelo = $args['modelo'] ?? '';
        $this->placas = $args['placas'] ?? '';
        $this->chofer = $args['chofer'] ?? '';
        $this->url = $args['url'] ?? '';
    }

    // Método para buscar una unidad por un campo
    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE $columna = '$valor'";
        $resultado = self::consultarSQL($query);
        return !empty($resultado) ? $resultado[0] : null;  // Devuelve el primer registro o null si no existe
    }

    // Validación del ID de la unidad
    public function validarIdUnidad() {
        if(!$this->idunidad) {
            self::$alertas['error'][] = 'El ID de la unidad es obligatorio';
        }
    }

    // Validación de otros campos de la unidad
    public function validarUnidad() {
        if (!$this->modelo) {
            self::$alertas['error'][] = 'El modelo es obligatorio';
        }

        if (!$this->placas) {
            self::$alertas['error'][] = 'Las placas son obligatorias';
        } 

        return self::$alertas;
    }

    // Asignar chofer a la unidad
    public function asignarChofer($idChofer) {
        if ($idChofer > 0) {
            $this->chofer = $idChofer;
        } else {
            self::$alertas['error'][] = 'El ID del chofer no es válido';
        }
    }


}
