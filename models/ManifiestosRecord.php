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
    public $obra;
    public $tipo_residuo;

    

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
    // Se cambio de nombre a  cliente_id ya que es la variable de los clientes registrados
    public static function calcularM3($cliente_id, $anio, $mes, $tipo){
        // BUSCAR POR ID_CLIENTE EN LUGAR DE NOMBRE
        $tipo_escapado = self::$db->escape_string($tipo);
        $query = "SELECT COUNT(*) as total FROM pedidos 
                WHERE servicio = '{$tipo}'
                AND id_cliente = {$cliente_id} 
                AND YEAR(fecha_pedido) = {$anio}
                AND MONTH(fecha_pedido) = {$mes}";
        
        error_log("🔍 CALCULAR M3 - Buscando por ID: cliente_id={$cliente_id}, año={$anio}, mes={$mes}, tipo='{$tipo}'");
        
        $resultado = self::$db->query($query);
        if (!$resultado) {
            error_log("❌ ERROR en consulta: " . self::$db->error);
            return 0;
        }  
        $row = $resultado->fetch_assoc();
        $total = $row['total'] ?? 0;
        
        error_log("🔍 CALCULAR M3 - Resultado: " . $total . " viajes encontrados");
        
        return $total;
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
    public static function registrar($cliente, $obra, $tipo_residuo, $mes, $anio, $totalm3){
        $query = "INSERT INTO manifiestos (cliente, obra, tipo_residuo, mes, anio, total_m3)
                VALUES ('{$cliente}','{$obra}','{$tipo_residuo}','{$mes}','{$anio}','{$totalm3}')";

        $resultado = self::$db->query($query);
        return [
            'resultado' => $resultado,
            'id' => self::$db->insert_id
        ];
    }
    public static function obtenerHistorialManifiestos(){
        $query = "SELECT * FROM manifiestos";
        $resultado = self::consultarSQL($query);

        return $resultado;
    }
    public static function buscarRegistro($cliente, $obra, $tipo_residuo, $mes, $anio){
        $query = "SELECT * FROM manifiestos 
                WHERE cliente = '{$cliente}'
                AND obra = '{$obra}' 
                AND tipo_residuo = '{$tipo_residuo}'
                AND mes = '{$mes}' 
                AND anio = '{$anio}'";
        
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    public static function busquedaSec($cliente,$obra,$mes,$anio){
        $query = "SELECT * FROM manifiestos WHERE cliente = '{$cliente}'
        AND obra = '{$obra}'AND mes = '{$mes}' AND anio = '{$anio}'";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    public static function busquedaPorNombre($nombre){
        $query = "SELECT * FROM clientes WHERE razon_social = '{$nombre}'";
        $resultado = self::consultarSQL($query);
        return $resultado;  
    }



    // metodos nuevos para conexion con pedidos

    public static function obtenerClienteCompleto($cliente_id) {
        $query = "SELECT * FROM clientes WHERE id = {$cliente_id}";
        $resultado = self::consultarSQL($query);
        return $resultado[0] ?? null;
    }

    public static function obtenerPedidosParaManifiesto($cliente_id, $anio, $mes, $tipo_residuo) {
        // Validar que $mes no esté vacío
        $query = "SELECT * FROM pedidos 
                WHERE servicio = '{$tipo_residuo}'
                AND id_cliente = {$cliente_id} 
                AND YEAR(fecha_pedido) = {$anio}";
        
        // Solo agregar condición del mes si no está vacío
        if (!empty($mes)) {
            $query .= " AND MONTH(fecha_pedido) = {$mes}";
        }
        
        $query .= " ORDER BY fecha_pedido";
        
        return self::consultarSQL($query);
    }

    public static function calcularM3Mejorado($cliente_id, $anio, $mes, $tipo_residuo, $nombre_cliente = null) {
        // Si se pasa el nombre del cliente en lugar del ID
        if ($cliente_id === null && $nombre_cliente !== null) {
            $cliente = self::busquedaPorNombre($nombre_cliente);
            if ($cliente) {
                $cliente_id = $cliente[0]->id;
            }
        }
        
        // Para servicios de materiales, calcular basado en productos
        if ($tipo_residuo === 'Materiales') {
            $query = "SELECT SUM(p.precio * pp.cantidad) as total_m3 
                    FROM pedidos pd
                    JOIN pedido_productos pp ON pd.id = pp.id_pedido
                    JOIN productos p ON pp.idproducto = p.idproducto
                    WHERE pd.servicio = '{$tipo_residuo}'
                    AND pd.id_cliente = {$cliente_id} 
                    AND YEAR(pd.fecha_pedido) = {$anio}";
        } else {
            // Para otros servicios (Escombro, Madera, etc.), contar viajes
            $query = "SELECT COUNT(*) as total_viajes 
                    FROM pedidos 
                    WHERE servicio = '{$tipo_residuo}'
                    AND id_cliente = {$cliente_id} 
                    AND YEAR(fecha_pedido) = {$anio}";
        }
        
        // Solo agregar condición del mes si no está vacío
        if (!empty($mes)) {
            $query .= " AND MONTH(fecha_pedido) = {$mes}";
        }
        
        $resultado = self::$db->query($query);  
        $row = $resultado->fetch_assoc();
        
        if ($tipo_residuo === 'Materiales') {
            return $row['total_m3'] ?? 0;
        } else {
            // Asumir 7m³ por viaje para servicios no materiales
            return ($row['total_viajes'] ?? 0) * 7;
        }
    }

    public static function eliminar($id) {
        //  VALIDAR Y SANITIZAR EL ID
        $id = (int) self::$db->escape_string($id);
        
        if ($id <= 0) {
            return false;
        }
        
        $query = "DELETE FROM manifiestos WHERE id = {$id}";
        $resultado = self::$db->query($query);
        
        // VERIFICAR SI SE ELIMINÓ ALGUNA FILA
        return ($resultado && self::$db->affected_rows > 0);
    }


    
}