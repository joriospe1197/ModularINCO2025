<?php

namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = 'empleados';
    protected static $columnasDB = ['idempleado','nombre', 'direccion', 'telefono', 'contrasena', 'email', 'token', 'confirmado', 'tipo_usuario','tipo_puesto']; // Eliminar 'idempleado'

    // Método para obtener todos los choferes
    public static function allChoferes() {
        $query = "SELECT * FROM " . static::$tabla . " WHERE tipo_puesto = 'chofer'";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    
    public function __construct($args = []) {
        // Ya no necesitamos pasar 'idempleado' porque es autoincremental
        $this->idempleado = $args['idempleado'] ?? '';
        $this->nombre = $args['nombre'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->contrasena = $args['contrasena'] ?? '';
        $this->contrasena2 = $args['contrasena2'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->tipo_usuario = $args['tipo_usuario'] ?? 0;
        $this->tipo_puesto = $args['tipo_puesto'] ?? '';
    }

    //Validar Login
    public function validarLogin(){
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no válido';
        }
        if (!$this->contrasena) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }
        return self::$alertas;
    }

    //Validar que alla ingresado el id del empleado
    public function validaridempleado(){
        if (!$this->idempleado){
            self::$alertas['error'][] = 'El id del empleado es obligatorio';
        }
    }
    
    // Validación para cuentas nuevas
    public function validarNuevaCuenta() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if (!$this->direccion) {
            self::$alertas['error'][] = 'La dirección es obligatorio';
        }
        if (!$this->telefono) {
            self::$alertas['error'][] = 'El teléfono es obligatorio';
        }
        if (!$this->contrasena) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }
        if (strlen($this->contrasena) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }
        if ($this->contrasena !== $this->contrasena2) {
            self::$alertas['error'][] = 'Las contraseñas deben coincidir';
        }
    
        // Validación para tipo_puesto (puesto)
        if (empty($this->tipo_puesto)) {
            self::$alertas['error'][] = 'El puesto es obligatorio';
        }
    
    
        return self::$alertas;
    }
    

    //Valida un email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no válido';
        }

        return self::$alertas;
    }

    //Valida el password
    public function validarPassword(){
        if (!$this->contrasena) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }
        if (strlen($this->contrasena) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    // Encriptar el password
    public function hashPassword() {
        $this->contrasena = password_hash($this->contrasena, PASSWORD_BCRYPT);
    }

    // Generar token
    public function crearToken() {
        $this->token = md5(uniqid());
    }


}
