<?php

namespace Model;

class WeeklyHistory extends WeeklyRecord {
    protected static $tabla = 'historial_semanal';

    protected static $columnasDB = ['primer_folio','ultimo_folio','justificacion','saldo_actual','chofer'];

    public function __construct($args = []) {
        
        $this->primer_folio = $args['primer_folio'] ?? '';
        $this->ultimo_folio = $args['ultimo_folio'] ?? '';
        $this->saldo_actual = $args['saldo_actual'] ?? '';
        $this->justificacion = $args['justificacion'] ?? '';
        
        $this->chofer = $args['chofer'] ?? '';
        
    }
    public function validarNuevoRegistro() {
        if (!$this->primer_folio) {
            self::$alertas['error'][] = 'Es necesario ingresar un folio';
        }
        if (!$this->ultimo_folio) {
            self::$alertas['error'][] = 'Es necesario ingresar un folio';
        }
        if (!$this->justificacion) {
            self::$alertas['error'][] = 'Es necesario la justificacion';
        }
    
        return self::$alertas;
    }
    public function validarFolio(){
        if (!$this->primer_folio){
            self::$alertas['error'][] = 'Es necesario el primer folio';
        }
    }
}