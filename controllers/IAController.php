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
            echo "Procedere a realizar el analisis mensual";
            echo implode(PHP_EOL, $out);
            exec($cmd, $out, $code);
            echo "<pre>";
            echo "Salida del script:\n";
            echo htmlspecialchars(implode(PHP_EOL, $out));
            echo "\n\nCódigo de salida: $code";
            echo "</pre>";
            if ($code !== 0) {
                die("\nError: El script Python falló");
            }
            echo "\nAnálisis completado. Redirigiendo...";
            header("Refresh:3; url=/inicio"); // Espera 3 segundos y recarga
            exit;

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
