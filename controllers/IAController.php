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
        if (!defined('PYTHON_PATH') || !defined('SCRIPTS_DIR')) {
            die('Error: Configuración de Python no encontrada. Verifica config.php');
        }
        $python = PYTHON_PATH;
        $script = SCRIPTS_DIR . DIRECTORY_SEPARATOR . 'predictions.py';
        if (!file_exists($script)) {
            die("Error: Script Python no encontrado en: $script");
        }
        $cmd = "\"$python\" \"$script\" 2>&1";
        $out = [];
        $code = 0;
        
        $respuesta = MachineLearningRecord::verificar();
        
        // Inicializar variables para evitar warnings cuando $respuesta es false
        $datos = [];
        $soloServicios = [];
        $historialUltimoMes = [];
        $clientesHistorial = [];
        $ingresosPredicción = 0;
        $prediccion = [];
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;
        $ingresosLastYear = 0;


        if($respuesta){
            $datos = MachineLearningRecord::top3Materiales();
            $historialUltimoMes = MachineLearningRecord::getLastMonth();
            $clientesHistorial = MachineLearningRecord::getClientesSaldo();
            $ingresosPredicción = MachineLearningRecord::getIncomePredictions();
            $prediccion = MachineLearningRecord::getPrediction();
            $currentYear = date('Y');
            $lastYear    = $currentYear - 1;
            $ingresosLastYear = MachineLearningRecord::getPreviousYear();
            
            foreach($datos as $material){
                $soloServicios[] = $material->servicio;
            }
            
        }else{
            if (!$respuesta) {
                // Ejecutar script Python
                exec($cmd, $out, $code);
            
                if ($code !== 0) {
                    $_SESSION['alerta'] = [
                        'tipo' => 'error',
                        'mensaje' => "El script Python falló. Código de salida: $code"
                    ];
                } else {
                    $_SESSION['alerta'] = [
                        'tipo' => 'exito',
                        'mensaje' => "Análisis mensual completado correctamente."
                    ];
                }
            
                // Redirigir a la página de inicio sin hacer echo
                header("Location: /inicio");
                exit;
            }

        }
        
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
