<?php

namespace Model;


class ManifiestosActiveRecord extends ManifiestosRecord {

    protected static $tabla;

    protected static $columnasDB = [];

    public function __construct($args = []) {

        $this->cliente = $args['cliente'] ?? '';
        $this->mes = $args['mes'] ?? '';
        $this->anio = $args['anio'] ?? '';
        $this->total_m3 = $args['totalm3'] ?? '';

        
    }
}