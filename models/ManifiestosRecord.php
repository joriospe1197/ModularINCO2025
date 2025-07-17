<?php

namespace Model;

class ManifiestosRecord {
    protected static $db;
    protected static $tabla;
    protected static $columnasDB = [];
    protected static $alertas = [];
    public $id;
    public $razon_social;
    public $domicilio;
    public $municipio;
    public $estado;
    public $codigo_postal;
    public $correo_electronico;
    public $telefono;

    public $mes;
    public $cliente;
    public $anio;
    public $total_m3;
    

    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }
    public static function getAlertas() {
        return static::$alertas;
    }

    public static function allClientes(){
        $query = "SELECT * FROM clientes";
        $resultado = self::consultarSQL($query);
        return $resultado;
        

    }
    public static function obtenerDir($cliente){
        $query = "SELECT domicilio FROM clientes WHERE id = {$cliente}";
        $resultado = self::consultarSQL($query);
        $resultado = $resultado[0];
        return $resultado->domicilio;
    }
    public static function obtenerNombre($cliente){
        $query = "SELECT razon_social FROM clientes WHERE id = {$cliente}";
        $resultado = self::consultarSQL($query);
        $resultado = $resultado[0];
        return $resultado->razon_social;
    }
    public static function obtenerMunicipio($cliente){
        $query = "SELECT municipio FROM clientes WHERE id = {$cliente}";
        $resultado = self::consultarSQL($query);
        $resultado = $resultado[0];
        return $resultado->municipio;
    }
    public static function obtenerEstado($cliente){
        $query = "SELECT estado FROM clientes WHERE id = {$cliente}";
        $resultado = self::consultarSQL($query);
        $resultado = $resultado[0];
        return $resultado->estado;
    }
    public static function obtenerCodP($cliente){
        $query = "SELECT codigo_postal FROM clientes WHERE id = {$cliente}";
        $resultado = self::consultarSQL($query);
        $resultado = $resultado[0];
        return $resultado->codigo_postal;
    }
    public static function obtenerCorreo($cliente){
        $query = "SELECT correo_electronico FROM clientes WHERE id = {$cliente}";
        $resultado = self::consultarSQL($query);
        $resultado = $resultado[0];
        return $resultado->correo_electronico;
    }
    public static function obtenerTel($cliente){
        $query = "SELECT telefono FROM clientes WHERE id = {$cliente}";
        $resultado = self::consultarSQL($query);
        $resultado = $resultado[0];
        return $resultado->telefono;
    }
    public static function calcularM3($nombre,$anio,$mes,$tipo){
        $query = "SELECT COUNT(*) as total FROM pedidos WHERE servicio = '{$tipo}'
        AND cliente = '{$nombre}' AND YEAR(fecha) = {$anio}
        AND MONTH(fecha) = {$mes}";
        $resultado = self::$db->query($query);  
        $row = $resultado->fetch_assoc();
        return $row['total'];
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
    public function atributos() {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            // Si es la columna 'idempleado', no incluirla si es autoincremental
            if ($this->$columna === null) continue;  // No incluir 'idempleado'
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
    public static function registrar($cliente,$mes,$anio,$totalm3){
        $query = "INSERT INTO manifiestos (cliente, mes, anio, total_m3)
        VALUES ('{$cliente}','{$mes}','{$anio}','{$totalm3}')";
        $resultado = self::$db->query($query);
        return [
            'resultado' => $resultado,
           
        ];
    }
    public static function obtenerHistorialManifiestos(){
        $query = "SELECT * FROM manifiestos";
        $resultado = self::consultarSQL($query);      
        return $resultado;
    }
}