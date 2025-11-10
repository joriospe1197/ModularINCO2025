<?php

namespace Controllers;

use MVC\Router;
use Model\MachineLearningRecord;

require_once __DIR__ . '/../includes/funciones.php';

class IAController
{
    public static function index(Router $router)
    {
        session_start();

        // Inicializar variables para evitar warnings
        $datos = [];
        $soloServicios = [];
        $historialUltimoMes = [];
        $clientesHistorial = [];
        $ingresosPredicción = [];
        $prediccion = [];
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;
        $ingresosLastYear = [];

        if (!defined('PYTHON_PATH') || !defined('SCRIPTS_DIR')) {
            die('Error: Configuración de Python no encontrada. Verifica config.php');
        }

        $python = PYTHON_PATH;

        // Scripts Python
        $scriptMateriales = SCRIPTS_DIR . DIRECTORY_SEPARATOR . 'predictions.py';
        if (!file_exists($scriptMateriales)) {
            die("Error: Script predictions.py no encontrado en: $scriptMateriales");
        }
        $cmdMateriales = "\"$python\" \"$scriptMateriales\" 2>&1";

        $scriptIngresos = SCRIPTS_DIR . DIRECTORY_SEPARATOR . 'ingresos_predictions.py';
        if (!file_exists($scriptIngresos)) {
            die("Error: Script ingresos_predictions.py no encontrado en: $scriptIngresos");
        }
        $cmdIngresos = "\"$python\" \"$scriptIngresos\" 2>&1";

        $respuesta = MachineLearningRecord::verificar();

        if ($respuesta) {
            // Datos ya existentes en la base de datos
            $datos = MachineLearningRecord::top3Materiales();
            $historialUltimoMes = MachineLearningRecord::getLastMonth();
            $clientesHistorial = MachineLearningRecord::getClientesSaldo();
            $ingresosPredicción = MachineLearningRecord::getIncomePredictions();
            $prediccion = MachineLearningRecord::getPrediction();
            $ingresosLastYear = MachineLearningRecord::getPreviousYear();

            foreach ($datos as $material) {
                $soloServicios[] = $material->servicio;
            }
        } else {
            // Ejecuta ambos scripts Python
            exec($cmdMateriales, $outMateriales, $codeMateriales);
            if ($codeMateriales !== 0) {
                die("Error: predictions.py falló:\n" . implode("\n", $outMateriales));
            }

            exec($cmdIngresos, $outIngresos, $codeIngresos);
            if ($codeIngresos !== 0) {
                die("Error: ingresos_predictions.py falló:\n" . implode("\n", $outIngresos));
            }

            // Cargar datos nuevamente desde la base de datos
            $datos = MachineLearningRecord::top3Materiales();
            $historialUltimoMes = MachineLearningRecord::getLastMonth();
            $clientesHistorial = MachineLearningRecord::getClientesSaldo();
            $ingresosPredicción = MachineLearningRecord::getIncomePredictions();
            $prediccion = MachineLearningRecord::getPrediction();
            $ingresosLastYear = MachineLearningRecord::getPreviousYear();

            foreach ($datos as $material) {
                $soloServicios[] = $material->servicio;
            }
        }

        // Preparar datos para la vista
        $datos_vista = [
            'titulo' => 'Dashboard',
            'datos' => $datos,
            'servicios' => $soloServicios,
            'ultimoMes' => $historialUltimoMes,
            'clientes' => $clientesHistorial,
            'ingresosMes' => $ingresosPredicción,
            'prediccionMes' => $prediccion,
            'currentYear' => $currentYear,
            'lastYear' => $lastYear,
            'ingresosLastYear' => $ingresosLastYear
        ];

        $router->render('dashboard/inicio', $datos_vista);
    }
}
