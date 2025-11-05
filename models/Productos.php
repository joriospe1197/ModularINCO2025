<?php

namespace Model;

use Model\ActiveRecord;

class Productos extends ActiveRecord {
    protected static $tabla = 'productos';
    protected static $columnasDB = ['idproducto', 'descripcion', 'precio'];

    public function __construct($args = []) {
        $this->idproducto = $args['idproducto'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->precio = $args['precio'] ?? '';
    }

    // Validación del producto
    public function validar() {
        $alertas = [];

        if (!$this->descripcion) {
            $alertas[] = 'La descripción es obligatoria';
        }

        if (!$this->precio) {
            $alertas[] = 'El precio es obligatorio';
        }

        return $alertas;
    }

    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        return self::consultarSQL($query);
    }

    // Validación de otros campos
    public function validarProducto() {
        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción es obligatoria';
        }

        if (!$this->precio) {
            self::$alertas['error'][] = 'El precio es obligatorio';
        } 

        return self::$alertas;
    }

    public function validarIdProducto() {
        if(!$this->idproducto) {
            self::$alertas['error'][] = 'El ID del producto es obligatorio';
        }
        return self::$alertas;
    }

    

    
}

