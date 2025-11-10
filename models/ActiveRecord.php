<?php

namespace Model;

class ActiveRecord {

    // Base de datos
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];
    public $num_pedidos;
    public $razon_social;
    public $nombre_cliente;
    public $servicio;
    public $periodo;
    public $ingresos;
    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    // Asegurar acceso a la conexion de BD
    protected static function getDB() {
        return self::$db;
    }
    // Para consultas que no devuelven resultados (INSERT, UPDATE, DELETE)
    public static function ejecutarSQL($query) {
        $resultado = self::$db->query($query);
        return $resultado;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // Registros - CRUD
    public function guardar() {
        $resultado = '';
        
        // Detectar ID de forma más robusta
        $idCampo = null;
        $idCamposPosibles = ['id', 'idempleado', 'idproducto', 'idunidad', 'id_servicio'];
        
        foreach ($idCamposPosibles as $campo) {
            if (property_exists($this, $campo)) {
                $idCampo = $campo;
                break;
            }
        }
        
        if ($idCampo && !is_null($this->$idCampo) && $this->$idCampo > 0) {
            $resultado = $this->actualizar($idCampo);
        } else {
            $resultado = $this->register();
        }
        
        return $resultado;
    }
    
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
    public static function find($idempleado) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE idempleado = $idempleado";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Obtener Registro
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT $limite";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Busqueda Where con Columna 
    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE $columna = '$valor'";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // SQL para Consultas Avanzadas.
    public static function SQL($consulta) {
        $query = $consulta;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Crea un nuevo registro
    public function register() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();
    
        // Asegurarse de que 'idempleado' no esté presente si es autoincremental
         // Eliminar cualquier ID auto-incremental que no deba insertarse
        unset($atributos['idempleado'], $atributos['idunidad'], $atributos['idproducto']);// Eliminar 'idempleado' si es autoincremental
    
        // Insertar en la base de datos
        $query = "INSERT INTO " . static::$tabla . " (";  
        $query .= join(', ', array_keys($atributos));  // Usar solo las columnas correctas
        $query .= ") VALUES ('";
        $query .= join("', '", array_values($atributos));  // Usar los valores sanitizados
        $query .= "')";
    
        // Resultado de la consulta
        $resultado = self::$db->query($query);
    
        return [
            'resultado' => $resultado,
            'idempleado' => self::$db->insert_id  // Obtener el id insertado correctamente
        ];
    }

    public function actualizar($idCampo = 'idempleado') {
        $atributos = $this->sanitizarAtributos();
    
        $valores = [];
        foreach ($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }
    
        $query = "UPDATE " . static::$tabla . " SET ";
        $query .= join(', ', $valores);
        $query .= " WHERE {$idCampo} = '" . self::$db->escape_string($this->$idCampo) . "' ";
        $query .= "LIMIT 1";
    
        $resultado = self::$db->query($query);
        return [
            'resultado' => $resultado
        ];
    }
    

    // Eliminar un registro - Toma el ID de Active Record
    public function eliminar() {
        // Detectar el campo ID según el modelo (idunidad o idempleado)
        $idCampo = property_exists($this, 'idunidad') ? 'idunidad' : (property_exists($this, 'idempleado') ? 'idempleado' : 'idproducto');
    
        $query = "DELETE FROM " . static::$tabla . " WHERE {$idCampo} = " . self::$db->escape_string($this->$idCampo) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }
    

    public static function consultarSQL($query) {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // Liberar la memoria
        $resultado->free();

        // Retornar los resultados
        return $array;
    }

    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }
    

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            // Si es la columna 'idempleado', no incluirla si es autoincremental
            if ($columna === 'idempleado' || $this->$columna === null) continue;  // No incluir 'idempleado'
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }



    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    public function sincronizar($args = []) { 
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }

    public static function escape($string) {
        return self::$db->real_escape_string($string);
    }

    // En ActiveRecord.php, agregar este método:
    public static function ejecutarSQLDirecto($query) {
        try {
            $resultado = self::$db->query($query);
            return $resultado !== false;
        } catch (Exception $e) {
            error_log("Error ejecutando consulta: " . $e->getMessage());
            return false;
        }
    }
}
