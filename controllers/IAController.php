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
        $ingresos = MachineLearningRecord::verificarIngresos();
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
            ?>
            <!DOCTYPE html>
            <html lang="es">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Procesando Análisis</title>
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }

                    body {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        background: linear-gradient(135deg, #2c3e7d 0%, #1e2a52 100%);
                        min-height: 100vh;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }

                    .container {
                        background: white;
                        padding: 50px;
                        border-radius: 20px;
                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                        text-align: center;
                        max-width: 500px;
                    }

                    .spinner {
                        border: 8px solid #f3f3f3;
                        border-top: 8px solid #2c3e7d;
                        border-radius: 50%;
                        width: 80px;
                        height: 80px;
                        animation: spin 1s linear infinite;
                        margin: 0 auto 30px;
                    }

                    @keyframes spin {
                        0% {
                            transform: rotate(0deg);
                        }

                        100% {
                            transform: rotate(360deg);
                        }
                    }

                    h1 {
                        color: #2c3e7d;
                        margin-bottom: 20px;
                        font-size: 28px;
                        font-weight: 600;
                    }

                    p {
                        color: #666;
                        font-size: 16px;
                        line-height: 1.6;
                        margin-bottom: 10px;
                    }

                    .progress-dots {
                        margin-top: 30px;
                    }

                    .dot {
                        display: inline-block;
                        width: 12px;
                        height: 12px;
                        background: #2c3e7d;
                        border-radius: 50%;
                        margin: 0 5px;
                        animation: pulse 1.5s ease-in-out infinite;
                    }

                    .dot:nth-child(2) {
                        animation-delay: 0.3s;
                    }

                    .dot:nth-child(3) {
                        animation-delay: 0.6s;
                    }

                    @keyframes pulse {

                        0%,
                        100% {
                            transform: scale(1);
                            opacity: 1;
                        }

                        50% {
                            transform: scale(1.5);
                            opacity: 0.5;
                        }
                    }

                    .info {
                        margin-top: 30px;
                        padding: 15px;
                        background: #f8f9fa;
                        border-radius: 10px;
                        border-left: 4px solid #2c3e7d;
                    }

                    .info p {
                        font-size: 14px;
                        color: #555;
                        margin: 5px 0;
                    }

                    #statusMessage {
                        font-weight: bold;
                        color: #2c3e7d;
                    }
                </style>
            </head>

            <body>
                <div class="container">
                    <div class="spinner"></div>
                    <h1>JIMARSOFT</h1>
                    <p id="statusMessage">Iniciando análisis mensual...</p>
                    <p><strong>Bienvenido!</strong></p>

                    <div class="progress-dots">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>

                    <div class="info">
                        <p> Espere un segundo porfavor...</p>

                    </div>
                </div>

                <script>
                    const messages = [
                        'Iniciando análisis mensual...',
                        'Procesando información...',
                        'Generando pronósticos de materiales...',
                        'Calculando predicciones de ingresos...',
                        'Guardando resultados...',
                        'Finalizando análisis...'
                    ];

                    let currentMessage = 0;
                    const messageElement = document.getElementById('statusMessage');

                    const interval = setInterval(() => {
                        if (currentMessage < messages.length - 1) {
                            currentMessage++;
                            messageElement.textContent = messages[currentMessage];
                        }
                    }, 2000);
                </script>
            </body>

            </html>
<?php

            // Forzar que se envíe el HTML al navegador ANTES de ejecutar Python
            if (ob_get_level()) ob_end_flush();
            flush();

            // Ejecutar primer script (predictions.py)
            $python = PYTHON_PATH;
            $script1 = SCRIPTS_DIR . DIRECTORY_SEPARATOR . 'predictions.py';

            if (!file_exists($script1)) {
                echo "<script>alert('Error: Script Python no encontrado'); window.location.href='/';</script>";
                exit;
            }

            $cmd1 = "\"$python\" \"$script1\" 2>&1";
            $out1 = [];
            $code1 = 0;
            exec($cmd1, $out1, $code1);

            if ($code1 !== 0) {
                // Log del error
                error_log("Error en predictions.py: " . implode("\n", $out1));
                echo "<script>alert('Error al ejecutar análisis de materiales. Contacta al administrador.'); window.location.href='/';</script>";
                exit;
            }

            // Ejecutar segundo script (ingresos_predictions.py)
            $script2 = SCRIPTS_DIR . DIRECTORY_SEPARATOR . 'ingresos_predictions.py';

            if (!file_exists($script2)) {
                echo "<script>alert('Error: Script de ingresos no encontrado'); window.location.href='/';</script>";
                exit;
            }

            $cmd2 = "\"$python\" \"$script2\" 2>&1";
            $out2 = [];
            $code2 = 0;
            exec($cmd2, $out2, $code2);

            if ($code2 !== 0) {
                error_log("Error en ingresos_predictions.py: " . implode("\n", $out2));
                echo "<script>alert('Error al ejecutar análisis de ingresos. Contacta al administrador.'); window.location.href='/';</script>";
                exit;
            }

            // Scripts ejecutados con éxito, redirigir
            echo "<script>
            document.getElementById('statusMessage').textContent = '¡Análisis completado con éxito!';
            setTimeout(function() {
                window.location.href = window.location.href;
            }, 2000);
        </script>";

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
            'ingresosLastYear' => $ingresosLastYear,
            'ingresosCheck' => $ingresos
        ];
        $router->render('dashboard/inicio', $datos_vista);
    }
}
