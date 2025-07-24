<?php

namespace Model;

class WeeklyRecord {

    protected static $db;
    protected static $tabla = 'historial_semanal';

    protected static $columnasDB = ['primer_folio', 'ultimo_folio', 'saldo_actual', 'justificacion','chofer'];

    protected static $alertas = [];

    public $primer_folio;
    public $ultimo_folio;
    public $justificacion;
    public $saldo_actual;
    public $saldo_anterior_1;

    public $nombre;

    public $chofer;
    public $idempleado;



    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validarNuevoRegistro() {
        static::$alertas = [];
        if (!$this->primer_folio || !$this->ultimo_folio) {
            static::setAlerta('error', 'Ambos folios son obligatorios');
        } else if ($this->primer_folio > $this->ultimo_folio) {
            static::setAlerta('error', 'El primer folio debe ser menor o igual al último folio');
        } else {
            // Validar folios
            $query = "SELECT COUNT(*) as total FROM pedidos WHERE folio BETWEEN {$this->primer_folio} AND {$this->ultimo_folio}";
            $resultado = self::$db->query($query);
            $datos = $resultado->fetch_assoc();
    
            $esperado = $this->ultimo_folio - $this->primer_folio + 1;
            if ((int)$datos['total'] !== $esperado) {
                static::setAlerta('error', 'No existen todos los folios dentro del rango especificado');
            }
        }
        return static::$alertas;
    }
    public function calcularSaldo() {
        
        $query = "SELECT SUM(gastos - pagados - depositos) as saldo FROM pedidos WHERE folio BETWEEN {$this->primer_folio} AND {$this->ultimo_folio}";
        $resultado = self::$db->query($query);
        $datos = $resultado->fetch_assoc();
        
        $saldo_base = floatval($datos['saldo'] ?? 0);
        $justificacion = floatval($this->justificacion);
        $saldo_anterior = floatval($this->saldo_anterior_1);
        
        return $saldo_base + $justificacion + $saldo_anterior;
    }
    
    
    public function guardar() {
        // Calcular saldo antes de guardar
        $this->saldo_actual = $this->calcularSaldo();
        
        // Validar
        $alertas = $this->validarNuevoRegistro();
        if (!empty($alertas)) return false;
    
        // Guardar registro nuevo
        return $this->register();
    }
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    public static function allChoferes(){
        $query = "SELECT * FROM  empleados WHERE tipo_puesto = 'Chofer' ORDER BY nombre ASC";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    public static function find($folio) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE primer_folio = " . self::$db->escape_string($folio) . " LIMIT 1";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT {$limite}";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '{$valor}'";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    public function register() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();
    
        // Asegurarse de que 'idempleado' no esté presente si es autoincremental
        //unset($atributos['idempleado']); // Eliminar 'idempleado' si es autoincremental
    
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
           
        ];
    }
    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach ($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }

        $query = "UPDATE " . static::$tabla . " SET ";
        $query .= join(', ', $valores);
        $query .= " WHERE primer_folio = '" . self::$db->escape_string($this->primer_folio) . "' ";
        $query .= "LIMIT 1"; 

        // Ejecutar la consulta
        $resultado = self::$db->query($query);
        return $resultado;
    }
    public function eliminar() {
        $query = "DELETE FROM " . static::$tabla . " WHERE primer_folio = " . self::$db->escape_string($this->primer_folio) . " LIMIT 1";
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
    public static function obtenerPedidos($primer_folio, $ultimo_folio)
    {
        $query = "SELECT * FROM pedidos WHERE folio BETWEEN {$primer_folio} AND {$ultimo_folio}";
        $resultado = self::$db->query($query);
    
        $pedidos = [];
        while ($registro = $resultado->fetch_object()) {
            $pedidos[] = $registro;
        }
    
        return $pedidos;
    }
    public static function calcularGastosTotal($primer_folio, $ultimo_folio) {
        
        $query = "SELECT SUM( gastos ) as saldo FROM pedidos WHERE folio BETWEEN {$primer_folio} AND {$ultimo_folio}";
        $resultado = self::$db->query($query);
        $datos = $resultado->fetch_assoc();

        return $datos['saldo'] ?? 0;
    }

    public static function calcularPagadosTotal($primer_folio, $ultimo_folio) {
        
        $query = "SELECT SUM( pagados ) as saldo FROM pedidos WHERE folio BETWEEN {$primer_folio} AND {$ultimo_folio}";
        $resultado = self::$db->query($query);
        $datos = $resultado->fetch_assoc();

        return $datos['saldo'] ?? 0;
    }
    public static function calcularDepositosTotal($primer_folio, $ultimo_folio) {
        
        $query = "SELECT SUM( depositos ) as saldo FROM pedidos WHERE folio BETWEEN {$primer_folio} AND {$ultimo_folio}";
        $resultado = self::$db->query($query);
        $datos = $resultado->fetch_assoc();

        return $datos['saldo'] ?? 0;
    }
    
    public static function calcularAlmacenTotal($primer_folio, $ultimo_folio) {
        
        $query = "SELECT SUM( almacen ) as saldo FROM pedidos WHERE folio BETWEEN {$primer_folio} AND {$ultimo_folio}";
        $resultado = self::$db->query($query);
        $datos = $resultado->fetch_assoc();

        return $datos['saldo'] ?? 0;
    }
    public static function calcularCostoTotal($primer_folio, $ultimo_folio) {
        
        $query = "SELECT SUM( costo ) as saldo FROM pedidos WHERE folio BETWEEN {$primer_folio} AND {$ultimo_folio}";
        $resultado = self::$db->query($query);
        $datos = $resultado->fetch_assoc();

        return $datos['saldo'] ?? 0;
    }
    public static function obtenerSemanasPorChofer($chofer){
        $query = "SELECT * FROM historial_semanal WHERE chofer = {$chofer}" ;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    public static function obtenerSaldoAnterior($chofer_id, $primer_folio_actual) {

        $query = "SELECT saldo_actual FROM historial_semanal WHERE chofer = {$chofer_id} AND primer_folio < {$primer_folio_actual} ORDER BY primer_folio DESC LIMIT 1";
        $resultado = self::$db->query($query);
        $datos = $resultado->fetch_assoc();
        return floatval($datos['saldo_actual'] ?? 0);

    }
    public static function obtenerVistaEditar($primer_folio,$ultimo_folio){
        $query = "SELECT * FROM pedidos WHERE folio BETWEEN {$primer_folio} and {$ultimo_folio}" ;
        $resultado = self::$db->query($query);
        $registros = [];
        while ($row = $resultado->fetch_object()) {
            $registros[] = $row;
        }
        return $registros;
    }
    public static function updateWeek($primer_folio_original,$primer_folio,$ultimo_folio,$justificacion,$chofer){
        $primer_folio = intval($primer_folio);
        $ultimo_folio = intval($ultimo_folio);

        $justificacion = self::$db->real_escape_string($justificacion);
        $justificacion_valor = floatval($justificacion);

        $saldo_ = self::obtenerSaldoAnterior($chofer, $primer_folio);
        $saldo_base = floatval($saldo_);

        $saldo_actual = $saldo_base + self::calcularResultadoSemana($primer_folio, $ultimo_folio)  + $justificacion_valor;

        $query = "UPDATE historial_semanal SET primer_folio = $primer_folio, 
                      ultimo_folio = $ultimo_folio, 
                      justificacion = '$justificacion',
                      saldo_actual = $saldo_actual WHERE primer_folio = $primer_folio_original";
        return self::$db->query($query);  
    }
    public static function rangoFoliosYaRegistrado($nuevoInicio, $nuevoFin, $folioOriginal) {
        $query = "SELECT * FROM historial_semanal 
                  WHERE primer_folio != $folioOriginal AND (
                      (primer_folio BETWEEN $nuevoInicio AND $nuevoFin) OR
                      (ultimo_folio BETWEEN $nuevoInicio AND $nuevoFin) OR
                      ($nuevoInicio BETWEEN primer_folio AND ultimo_folio) OR
                      ($nuevoFin BETWEEN primer_folio AND ultimo_folio)
                  )";
        $resultado = self::consultarSQL($query);
        return !empty($resultado);
    }
    public static function calcularResultadoSemana($primer_folio, $ultimo_folio) {

        $query = "SELECT SUM(gastos - pagados - depositos) as resultado FROM pedidos WHERE folio BETWEEN $primer_folio AND $ultimo_folio";
        $resultado = self::$db->query($query);
        $datos = $resultado->fetch_assoc();
        $saldo_base = floatval($datos['resultado'] ?? 0);
        $saldo_anterior = floatval($saldo_base);

        return $saldo_anterior;
    }
    public static function delete_week($chofer,$primer_folio){

        $query = "DELETE FROM historial_semanal WHERE chofer = {$chofer} AND primer_folio = {$primer_folio}";
        $resultado = self::$db->query($query);
        return $resultado;
    }
}