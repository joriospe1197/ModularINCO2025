<?php
namespace Model;

use Model\ActiveRecord;

class Clientes extends ActiveRecord
{
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

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->razon_social = $args['razon_social'] ?? '';
        $this->domicilio = $args['domicilio'] ?? '';
        $this->municipio = $args['municipio'] ?? '';
        $this->estado = $args['estado'] ?? '';
        $this->codigo_postal = $args['codigo_postal'] ?? '';
        $this->correo_electronico = $args['correo_electronico'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
    }

    public static function all()
    {
        $query = "SELECT * FROM " . static::$tabla;
        return self::consultarSQL($query);
    }

    public static function eliminarCliente($id) {
        //  VALIDAR Y SANITIZAR EL ID
        $id = (int) self::$db->escape_string($id);
        
        if ($id <= 0) {
            return false;
        }
        
        $query = "DELETE FROM clientes WHERE id = {$id}";
        $resultado = self::$db->query($query);
        
        // VERIFICAR SI SE ELIMINÓ ALGUNA FILA
        return ($resultado && self::$db->affected_rows > 0);
    }
    public static function findById($id)
    {
        $query = "SELECT * FROM clientes WHERE id = {$id}";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }
    public static function findByName($name)
    {
        $query = "SELECT * FROM clientes WHERE razon_social = '{$name}'";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    public static function registerClient($razon_social, $domicilio, $estado, $municipio, $codigo_postal, $telefono, $correo_electronico)
    {
        $query = "INSERT INTO clientes (razon_social, domicilio, estado, municipio, codigo_postal, telefono, correo_electronico) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = self::$db->prepare($query);
        $stmt->bind_param('sssssss', $razon_social, $domicilio, $estado, $municipio, $codigo_postal, $telefono, $correo_electronico);
        $resultado = $stmt->execute();
        return [
            'resultado' => $resultado,
            'id' => self::$db->insert_id
        ];
    }

    public static function updateData($id, $razon_social, $domicilio, $estado, $municipio, $codigo_postal, $telefono, $correo_electronico)
    {
        $query = "UPDATE clientes 
              SET razon_social = ?, 
                  domicilio = ?, 
                  estado = ?, 
                  municipio = ?, 
                  codigo_postal = ?, 
                  telefono = ?, 
                  correo_electronico = ? 
              WHERE id = ?";

        $stmt = self::$db->prepare($query);
        $stmt->bind_param('sssssssi', $razon_social, $domicilio, $estado, $municipio, $codigo_postal, $telefono, $correo_electronico, $id);
        $resultado = $stmt->execute();

        return [
            'resultado' => $resultado,
            'affected_rows' => $stmt->affected_rows
        ];
    }
    

}
