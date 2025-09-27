<?php

namespace Controllers;

use Model\ManifiestosRecord;
use Model\ManifiestosActiveRecord;
use MVC\Router;
use Dompdf\Dompdf;
use Dompdf\Options;
use Model\WeeklyRecord;

class ManifiestosController{

    public static function crear_manifiesto(Router $router){
        session_start();

        $alertas = [];
        $clientes = ManifiestosRecord::allClientes();
        $meses = [
            "01" => "Enero",
            "02" => "Febrero",
            "03" => "Marzo",
            "04" => "Abril",
            "05" => "Mayo",
            "06" => "Junio",
            "07" => "Julio",
            "08" => "Agosto",
            "09" => "Septiembre",
            "10" => "Octubre",
            "11" => "Noviembre",
            "12" => "Diciembre"
        ];
        $mesSeleccionado = $_GET['mes'] ?? date('m');

        $router->render('manifiestos/crear_manifiesto', [
            'titulo' => 'Crear manifiesto',
            'alertas' => $alertas,
            'clientes' => $clientes,
            'meses' => $meses,
            'mesSeleccionado' => $mesSeleccionado
            
        ]);
    }


    public static function generar_manifiesto(Router $router){
        session_start();
        
        // Validar datos de entrada
        if (empty($_POST['clientes']) || empty($_POST['mes']) || empty($_POST['anio'])) {
            ManifiestosRecord::setAlerta('error', 'Todos los campos son obligatorios');
            self::crear_manifiesto($router);
            return;
        }
        
        $cliente_id = $_POST['clientes'];
        $mesM = $_POST['mes'];
        $anio = $_POST['anio'];
        $dirObra = $_POST['dirObra'] ?? '';
        $tipoResiduo = $_POST['tipoResiduo'] ?? '';



         // CONVERTIR NOMBRE DEL MES A NÚMERO
        $meses = [
            "Enero" => "01", "Febrero" => "02", "Marzo" => "03",
            "Abril" => "04", "Mayo" => "05", "Junio" => "06",
            "Julio" => "07", "Agosto" => "08", "Septiembre" => "09",
            "Octubre" => "10", "Noviembre" => "11", "Diciembre" => "12"
        ];
        $mes_numero = $meses[$mesM] ?? date('m'); // Usar mes actual si no se encuentra

        
         // OBTENER DATOS DEL CLIENTE
        $direccion = ManifiestosRecord::obtenerDir($cliente_id); 
        $correo = ManifiestosRecord::obtenerCorreo($cliente_id); 
        $codigo = ManifiestosRecord::obtenerCodP($cliente_id);
        $municipio = ManifiestosRecord::obtenerMunicipio($cliente_id);
        $estado = ManifiestosRecord::obtenerEstado($cliente_id);
        $telefono = ManifiestosRecord::obtenerTel($cliente_id);
        $nombre = ManifiestosRecord::obtenerNombre($cliente_id);
        
        // Calcular m³
        $viajes = ManifiestosRecord::calcularM3($cliente_id, $anio, $mes_numero, $tipoResiduo); 
        $totalm3 = $viajes * 7;
        
        
        $router->render('manifiestos/vista_previa_manifiesto',[
            'titulo' => 'Vista Previa de Manifiesto',
            'alertas' => ManifiestosRecord::getAlertas(),
            'clienteM' => $cliente_id,
            'mesM' => $mesM,
            'mes_numero' => $mes_numero,
            'anio' => $anio,
            'dirObra' => $dirObra,
            'direccion' => $direccion,
            'correo' => $correo,
            'tipoResiduo' => $tipoResiduo,
            'totalm3' => $totalm3,
            'codigo' => $codigo,
            'estado' => $estado,
            'municipio' => $municipio,
            'telefono' => $telefono,
            'nombre' => $nombre
        ]);
    }
    // Agregar este método para verificación de consistencia ... eliminado


    public static function generar_PDF(Router $router){
        
        $alertas = [];
        $cliente_id = $_POST['clientes'] ?? '';
        $mesM = $_POST['mes'] ?? '';
        $mes_numero = $_POST['mes_numero'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $anio = $_POST['anio'] ?? ''; 
        $direccion = $_POST['direccion'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $tipoResiduo = $_POST['tipoResiduo'] ?? '';
        $totalm3 = $_POST['totalm3'] ?? '';
        $dirObra = $_POST['dirObra'] ?? '';

        // SI mes_numero ESTÁ VACÍO, RECALCULARLO
        if (empty($mes_numero)) {
            $meses = [
                "Enero" => "01", "Febrero" => "02", "Marzo" => "03",
                "Abril" => "04", "Mayo" => "05", "Junio" => "06", 
                "Julio" => "07", "Agosto" => "08", "Septiembre" => "09",
                "Octubre" => "10", "Noviembre" => "11", "Diciembre" => "12"
            ];
            $mes_numero = $meses[$mesM] ?? date('m');
            
            error_log("SOLUCIÓN: mes_numero recalculado = " . $mes_numero);
        }



        //  VERIFICAR SI YA EXISTE EL MANIFIESTO
        $busqueda = ManifiestosRecord::buscarRegistro($nombre, $dirObra, $tipoResiduo, $mes_numero, $anio);

        if($busqueda && count($busqueda) > 0){
            ManifiestosRecord::setAlerta('error', 'Ya existe un manifiesto con estos datos');
            // Redirigir en lugar de generar PDF corrupto
            self::crear_manifiesto($router);
            return;
        }
        
        $registro_manifiestos = ManifiestosRecord::registrar($nombre, $dirObra, $tipoResiduo, $mes_numero, $anio, $totalm3);
        

        // LIMPIAR CUALQUIER OUTPUT ANTES DEL PDF
        if (ob_get_length()) {
            ob_clean();
        }
        
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        
        $html = '
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .titulo { text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 10px; }
        .seccion { border: 1px solid black; padding: 5px; margin-top: 10px; }
        .label { font-weight: bold; }
    </style>

    <div class="titulo">MANIFIESTO DE ENTREGA, TRANSPORTE Y RECEPCIÓN DE RESIDUOS DE MANEJO ESPECIAL</div>

    <div class="seccion">
    <div class="label">GENERADOR</div>
    <p><b>Razón Social:</b> '. $nombre .'</p>
    <p><b>Domicilio:</b> '. $direccion .'</p>
    <p><b>Correo:</b> '. $correo .'</p>
    <p><b>Tipo de residuo:</b> '. $tipoResiduo .'</p>
    <p><b>Total m³ recolectados en el mes:</b> '. $totalm3 .' m3</p>
    </div>

    <div class="seccion">
    <div class="label">TRANSPORTISTA</div>
    <p><b>Residuos recolectados en el periodo:</b>'. $mesM .'-'. $anio .'</p>
    <p><b>Nombre:</b> JIMAR CONSTRUCCIONES Y MATERIALES SA DE CV</p>
    <p><b>Responsable de la entrega de los residuos (nombre y firma)</b>'. $dirObra .'</p>
    <p><b>Vehículo:</b> Volteo 7m³</p>
    <p><b>Placas:</b> JGS4946, JP72072, ...</p>
    </div>

    <div class="seccion">
    <div class="label">DESTINO FINAL</div>
    <p><b>Nombre:</b> JOSE GUADALUPE GUTIERREZ PADILLA</p>
    <p><b>Destino:</b> RELLENO PARCELA 67 Z1P1/6</p>
    </div>

    ';

        try {
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            //  ENVIAR HEADERS CORRECTOS PARA PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="manifiesto_' . $nombre . '_' . $mesM . '.pdf"');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');
            
            // ENVIAR EL PDF Y TERMINAR LA EJECUCIÓN
            echo $dompdf->output();
            exit;
            
        } catch (Exception $e) {
            //  MANEJO DE ERRORES
            error_log("Error generando PDF: " . $e->getMessage());
            
            // Redirigir a página de error
            header('Location: /crear_manifiesto?error=pdf');
            exit;
        }
    }
    public static function manifiestos(Router $router){
        session_start();
        $manifiestos = ManifiestosRecord::obtenerHistorialManifiestos();
        
        
        $datos_para_vista = [
            'titulo' => 'Manifiestos',
            'manifiestos' => $manifiestos,
            'alertas' => ManifiestosRecord::getAlertas()
               
        ];
        
        
        $router->render('dashboard/manifiestos', $datos_para_vista);
    }
    
    public static function vista_manifiesto(Router $router){
        session_start();
        $alertas = [];
        $cliente_nombre = $_GET['cliente'] ?? '';
        $mes = $_GET['mes'] ?? '';
        $anio = $_GET['anio'] ?? '';
        $dirObra = $_GET['dirObra'] ?? '';
        $tipo_residuo = $_GET['tipo_residuo'] ?? '';

        // OBTENER EL ID_CLIENTE DESDE EL NOMBRE
        $datos_cliente = ManifiestosRecord::busquedaPorNombre($cliente_nombre);
        
        if (!$datos_cliente) {
            $alertas['error'][] = 'Cliente no encontrado: ' . $cliente_nombre;
            $cliente_id = null;
        } else {
            $cliente_id = $datos_cliente[0]->id;
        }
        
        $busqueda = ManifiestosRecord::busquedaSec($cliente_nombre, $dirObra, $mes, $anio);
        
        // USAR EL NUEVO MÉTODO CON ID_CLIENTE
        if ($cliente_id) {
            $viajes = ManifiestosRecord::calcularM3($cliente_id, $anio, $mes, $tipo_residuo);
        } else {
            $viajes = 0;
            $alertas['error'][] = 'No se pudo calcular m³ - cliente no válido';
        }
        
        $totalm3 = $viajes * 7;

        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
            '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
            '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
            '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        
        $router->render('manifiestos/vista_previa_manifiesto',[
            'titulo' => 'Vista de Manifiesto',
            'alertas' => array_merge($alertas, ManifiestosRecord::getAlertas()),
            'clienteM' => $cliente_nombre,
            'mesM' => $meses[$mes] ?? $mes,
            'mes_numero' => $mes,
            'anio' => $anio,
            'dirObra' => $dirObra,
            'direccion' => $datos_cliente[0]->domicilio ?? '',
            'correo' => $datos_cliente[0]->correo_electronico ?? '',
            'tipoResiduo' => $tipo_residuo,
            'totalm3' => $totalm3,
            'codigo' => $datos_cliente[0]->codigo_postal ?? '',
            'estado' => $datos_cliente[0]->estado ?? '',
            'municipio' => $datos_cliente[0]->municipio ?? '',
            'telefono' => $datos_cliente[0]->telefono ?? '',
            'nombre' => $cliente_nombre
        ]);
    }
    // verificarConsistenciaAPI ... eliminado

    public static function eliminar() {
        session_start();
        isAuth();
        
        // VERIFICAR PERMISOS (solo admin puede eliminar)
        if ($_SESSION['tipo_usuario'] != 1) {
            echo json_encode([
                'success' => false, 
                'error' => 'No tienes permisos para eliminar manifiestos'
            ]);
            return;
        }
        
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }
        
        try {
            $resultado = ManifiestosRecord::eliminar($id);
            
            if ($resultado) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al eliminar el manifiesto o el manifiesto no existe']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
