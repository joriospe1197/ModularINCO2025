<?php
namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Pedidos;

class WeeklyController {

    public static function historial_semanal(Router $router) {
        session_start();
        isAuth();
        
        // 1. Obtener choferes
        $choferes = Usuario::allChoferes();
        
        // 2. Lógica para Vista Detallada
        $choferSeleccionado = $_GET['chofer'] ?? ($choferes[0]->idempleado ?? 0);
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('monday this week'));
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d', strtotime('sunday this week'));
        
        $pedidos = Pedidos::obtenerPorChoferYFechas($choferSeleccionado, $fecha_inicio, $fecha_fin);
        
        $totales = ['gastos' => 0, 'costo' => 0, 'pagados' => 0, 'almacen' => 0, 'depositos' => 0];
        foreach ($pedidos as $pedido) {
            $totales['gastos'] += $pedido->gastos;
            $totales['costo'] += $pedido->costo;
            $totales['pagados'] += $pedido->pagados;
            $totales['almacen'] += $pedido->almacen;
            $totales['depositos'] += $pedido->depositos;
        }
        
        $nombre_chofer = "Seleccione un chofer";
        foreach ($choferes as $chofer) {
            if ($chofer->idempleado == $choferSeleccionado) {
                $nombre_chofer = $chofer->nombre;
                break;
            }
        }
        
        // 3. Lógica para Generar Resumen
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['tab'] ?? '') === 'resumen') {
            $chofer_id = $_POST['chofer'] ?? '';
            $fecha_inicio_resumen = $_POST['fecha_inicio'] ?? '';
            $fecha_fin_resumen = $_POST['fecha_fin'] ?? '';
            $justificacion = $_POST['justificacion'] ?? '';
            
            // Validación básica
            if (empty($chofer_id) || empty($fecha_inicio_resumen) || empty($fecha_fin_resumen)) {
                $alertas['error'][] = 'Todos los campos son obligatorios';
            } else {
                // Calcular totales del período
                $totales_resumen = self::calcularTotalesPeriodo($chofer_id, $fecha_inicio_resumen, $fecha_fin_resumen);
                
                // Guardar en la base de datos
                $resultado = self::guardarResumenSemanal(
                    $chofer_id, 
                    $fecha_inicio_resumen, 
                    $fecha_fin_resumen, 
                    $totales_resumen, 
                    $justificacion
                );
                
                if ($resultado) {
                    $_SESSION['alerta'] = [
                        'tipo' => 'exito',
                        'mensaje' => 'Resumen semanal ' . (self::existeResumen($chofer_id, $fecha_inicio_resumen) ? 'actualizado' : 'generado') . ' correctamente'
                    ];
                    header('Location: /historial_de_pedidos?tab=reportes');
                    return;
                } else {
                    $alertas['error'][] = 'Error al guardar el resumen';
                }
            }
        }
        
        // 4. Lógica para Reportes Consolidados
        $choferReporteSeleccionado = $_GET['chofer'] ?? null;
        
        $reportes = [];
        if ($choferReporteSeleccionado) {
            $reportes = self::obtenerReportesPorChofer($choferReporteSeleccionado);
        } else {
            $reportes = self::obtenerTodosReportes();
        }

        $titulo_reportes = self::obtenerTituloReportes($choferReporteSeleccionado, $reportes);
        
        // 5. Pasar todos los datos a la vista
        $router->render('dashboard/historial_de_pedidos', [
            'titulo' => 'Historial y Reportes de Pedidos',
            // Vista Detallada
            'pedidos' => $pedidos,
            'totales' => $totales,
            'choferSeleccionado' => $choferSeleccionado,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'nombre_chofer' => $nombre_chofer,
            // Generar Resumen
            'alertas' => $alertas,
            // Reportes Consolidados
            'choferReporteSeleccionado' => $choferReporteSeleccionado,
            'reportes' => $reportes,
            'choferes' => $choferes,
            'titulo_reportes' => $titulo_reportes
        ]);
    }

    // Métodos principales
    private static function calcularTotalesPeriodo($chofer_id, $fecha_inicio, $fecha_fin) {
        $pedidos = Pedidos::obtenerPorChoferYFechas($chofer_id, $fecha_inicio, $fecha_fin);
        
        $totales = [
            'total_gastos' => 0,
            'total_costos' => 0,
            'total_pagados' => 0,
            'total_almacen' => 0,
            'total_depositos' => 0
        ];
        
        foreach ($pedidos as $pedido) {
            $totales['total_gastos'] += (float)$pedido->gastos;
            $totales['total_costos'] += (float)$pedido->costo;
            $totales['total_pagados'] += (float)$pedido->pagados;
            $totales['total_almacen'] += (float)$pedido->almacen;
            $totales['total_depositos'] += (float)$pedido->depositos;
        }
        
        $totales['utilidad_neta'] = $totales['total_pagados'] - $totales['total_gastos'] - $totales['total_costos'];
        
        return $totales;
    }

    private static function guardarResumenSemanal($chofer_id, $fecha_inicio, $fecha_fin, $totales, $justificacion) {
        $justificacion_escape = Pedidos::escape($justificacion);
        $utilidad_neta = $totales['total_pagados'] - $totales['total_gastos'] - $totales['total_costos'];
        
        $query = "INSERT INTO historial_semanal 
                (fecha_inicio, fecha_fin, chofer, total_gastos, total_costos, total_pagados, 
                total_almacen, total_depositos, justificacion, saldo_actual) 
                VALUES ('$fecha_inicio', '$fecha_fin', $chofer_id, {$totales['total_gastos']}, 
                        {$totales['total_costos']}, {$totales['total_pagados']}, {$totales['total_almacen']}, 
                        {$totales['total_depositos']}, '$justificacion_escape', 
                        $utilidad_neta)
                ON DUPLICATE KEY UPDATE
                fecha_fin = VALUES(fecha_fin),
                total_gastos = VALUES(total_gastos),
                total_costos = VALUES(total_costos),
                total_pagados = VALUES(total_pagados),
                total_almacen = VALUES(total_almacen),
                total_depositos = VALUES(total_depositos),
                justificacion = VALUES(justificacion),
                saldo_actual = VALUES(saldo_actual)";

        return self::ejecutarConsultaDirecta($query);
    }

    private static function existeResumen($chofer_id, $fecha_inicio) {
        $query = "SELECT COUNT(*) as existe FROM historial_semanal 
                WHERE chofer = $chofer_id AND fecha_inicio = '$fecha_inicio'";
        
        $resultados = Pedidos::ejecutarSQL($query);
        return ($resultados[0]->existe ?? 0) > 0;
    }

    private static function ejecutarConsultaDirecta($query) {
        try {
            if (method_exists('Model\Pedidos', 'ejecutarSQLDirecto')) {
                return Pedidos::ejecutarSQLDirecto($query) !== false;
            } else {
                return Pedidos::ejecutarSQLWrite($query) !== false;
            }
        } catch (Exception $e) {
            error_log("ERROR en ejecutarConsultaDirecta: " . $e->getMessage());
            return false;
        }
    }

    private static function procesarReporte($row) {
        $total_gastos = isset($row->total_gastos) ? (float)$row->total_gastos : 0;
        $total_costos = isset($row->total_costos) ? (float)$row->total_costos : 0;
        $total_pagados = isset($row->total_pagados) ? (float)$row->total_pagados : 0;
        $total_almacen = isset($row->total_almacen) ? (float)$row->total_almacen : 0;
        $total_depositos = isset($row->total_depositos) ? (float)$row->total_depositos : 0;
        
        $utilidad_neta = $total_pagados - $total_gastos - $total_costos;
        $saldo_actual = isset($row->saldo_actual) ? (float)$row->saldo_actual : $utilidad_neta;
        
        $fecha_inicio = !empty($row->fecha_inicio) && $row->fecha_inicio != '0000-00-00' 
            ? $row->fecha_inicio : null;
        
        $fecha_fin = !empty($row->fecha_fin) && $row->fecha_fin != '0000-00-00' 
            ? $row->fecha_fin : null;
        
        $justificacion = !empty($row->justificacion) ? $row->justificacion : 'Sin justificación';
        
        return (object)[
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'nombre_chofer' => $row->nombre_chofer ?? 'Sin nombre',
            'total_gastos' => $total_gastos,
            'total_costos' => $total_costos,
            'total_pagados' => $total_pagados,
            'total_almacen' => $total_almacen,
            'total_depositos' => $total_depositos,
            'utilidad_neta' => $utilidad_neta,
            'justificacion' => $justificacion,
            'saldo_actual' => $saldo_actual
        ];
    }

    private static function obtenerTituloReportes($choferReporteSeleccionado, $reportes) {
        if (empty($reportes)) {
            return 'No hay reportes para mostrar';
        }
        
        if ($choferReporteSeleccionado) {
            return 'Reportes de ' . ($reportes[0]->nombre_chofer ?? 'chofer seleccionado');
        }
        
        return 'Reportes Generales';
    }

    private static function obtenerReportesPorChofer($chofer_id) {
        $query = "SELECT hs.*, e.nombre as nombre_chofer 
                FROM historial_semanal hs
                JOIN empleados e ON hs.chofer = e.idempleado
                WHERE hs.chofer = $chofer_id
                ORDER BY hs.fecha_inicio DESC";
        
        $resultados = Pedidos::ejecutarSQL($query);
        $reportes = [];
        
        foreach ($resultados as $row) {
            $reportes[] = self::procesarReporte($row);
        }
        
        return $reportes;
    }

    private static function obtenerTodosReportes() {
        $query = "SELECT hs.*, e.nombre as nombre_chofer 
                FROM historial_semanal hs
                JOIN empleados e ON hs.chofer = e.idempleado
                ORDER BY hs.fecha_inicio DESC";
        
        $resultados = Pedidos::ejecutarSQL($query);
        $reportes = [];
        
        foreach ($resultados as $row) {
            $reportes[] = self::procesarReporte($row);
        }
        
        return $reportes;
    }
}