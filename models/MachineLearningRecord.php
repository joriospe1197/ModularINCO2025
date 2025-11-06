<?php

namespace Model;

use Svg\Tag\Group;


class MachineLearningRecord extends ActiveRecord {

    protected static $db;
    protected static $tabla;
    protected static $columnasDB = [];
    protected static $alertas = [];
    
    public static function verificar() {
        $query = "SELECT COUNT(*) AS total FROM pronosticos_materiales 
                  WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
                  AND YEAR(created_at) = YEAR(CURRENT_DATE());";
    
        $resultado = self::consultarSQL($query);
    
        if (!empty($resultado) && isset($resultado[0]->total)) {
            return $resultado[0]->total > 0;
        }
    
        return false;
    }
    
    
    public static function top3Materiales(){
        $query = "SELECT servicio as servicio,
                        pronostico_mes as num_pedidos,
                        periodo as periodo 
                FROM pronosticos_materiales
                ORDER BY pronostico_mes DESC LIMIT 3";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    public static function getLastMonth(){
        $query = "SELECT servicio , 
                    COUNT(*) AS num_pedidos,     
                    DATE_FORMAT(CURDATE() - INTERVAL 1 MONTH, '%Y-%m') AS periodo
                    FROM pedidos
                    WHERE fecha_pedido >= DATE_FORMAT(CURDATE()-INTERVAL 1 MONTH, '%Y-%m-01')
                    AND fecha_pedido <= LAST_DAY(CURDATE() - INTERVAL 1 MONTH)
                    GROUP BY servicio
                    ORDER BY num_pedidos DESC"
                    ;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    public static function getClientesSaldo(){
        $query = "SELECT nombre_cliente ,
                COUNT(*) AS num_pedidos,
                DATE_FORMAT(CURDATE() - INTERVAL 1 MONTH, '%Y-%m') AS periodo,
                SUM(costo) AS ingresos
                FROM pedidos
                WHERE fecha_pedido >= DATE_FORMAT(CURDATE()-INTERVAL 1 MONTH, '%Y-%m-01')
                AND fecha_pedido <= LAST_DAY(CURDATE() - INTERVAL 1 MONTH)
                GROUP BY nombre_cliente
                ORDER BY periodo DESC";
        $resultado = self::consultarSQL($query);
        return $resultado;       
    }
    public static function getIncomePredictions(){
        $query = "SELECT 
                    DATE_FORMAT(fecha_pedido, '%Y-%m') AS periodo,
                    SUM(costo) AS ingresos
                    FROM pedidos
                    WHERE YEAR(fecha_pedido) = YEAR(CURDATE())
                    GROUP BY DATE_FORMAT(fecha_pedido, '%Y-%m')
                    ORDER BY periodo;";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    public static function getPrediction(){
        $query = "SELECT ingreso_pronosticado as ingresos FROM pronosticos_ingresos";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    public static function getPreviousYear(){
        $query = "SELECT 
                    DATE_FORMAT(fecha_pedido, '%Y-%m') AS periodo,
                    SUM(costo) AS ingresos
                    FROM pedidos
                    WHERE YEAR(fecha_pedido) = YEAR(CURDATE()) - 1 
                    GROUP BY DATE_FORMAT(fecha_pedido, '%Y-%m')
                    ORDER BY periodo;";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    
}