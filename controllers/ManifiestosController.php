<?php

namespace Controllers;

use Model\ManifiestosRecord;
use Model\ManifiestosActiveRecord;
use MVC\Router;
use Dompdf\Dompdf;
use Dompdf\Options;
use Model\WeeklyRecord;
use Model\Clientes;

class ManifiestosController
{

    public static function crear_manifiesto(Router $router)
    {
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


    public static function generar_manifiesto(Router $router)
    {
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

        $cliente = Clientes::findByIdGetName($cliente_id);

        // CONVERTIR NOMBRE DEL MES A NÚMERO
        $meses = [
            "Enero" => "01",
            "Febrero" => "02",
            "Marzo" => "03",
            "Abril" => "04",
            "Mayo" => "05",
            "Junio" => "06",
            "Julio" => "07",
            "Agosto" => "08",
            "Septiembre" => "09",
            "Octubre" => "10",
            "Noviembre" => "11",
            "Diciembre" => "12"
        ];
        $mes_numero = $meses[$mesM] ?? date('m'); // Usar mes actual si no se encuentra
        //  VERIFICAR SI YA EXISTE EL MANIFIESTO
        $busqueda = ManifiestosRecord::buscarRegistro($cliente_id, $dirObra, $tipoResiduo, $mes_numero, $anio);


        if (!empty($busqueda)) {
            ManifiestosRecord::setAlerta('error', 'Ya existe un manifiesto con estos datos');
            // OBTENER DATOS DEL CLIENTE
            $direccion = ManifiestosRecord::obtenerDir($cliente_id);
            $correo = ManifiestosRecord::obtenerCorreo($cliente_id);
            $codigo = ManifiestosRecord::obtenerCodP($cliente_id);
            $municipio = ManifiestosRecord::obtenerMunicipio($cliente_id);
            $estado = ManifiestosRecord::obtenerEstado($cliente_id);
            $telefono = ManifiestosRecord::obtenerTel($cliente_id);
            $nombre = ManifiestosRecord::obtenerNombre($cliente_id);
            $viajes = ManifiestosRecord::calcularM3($cliente_id, $anio, $mes_numero, $tipoResiduo);
            $totalm3 = $viajes * 7;
            $router->render('manifiestos/vista_manifiesto_guardado', [
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
                'nombre' => $nombre,
                'busqueda' => $cliente
            ]);
        }


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
        if ($totalm3 === 0) {
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
            ManifiestosRecord::setAlerta('error', 'No se encontraron m3 trabajados/entregados durante el periodo seleccionado');
            $router->render('manifiestos/crear_manifiesto', [
                'titulo' => 'Crear manifiesto',
                'alertas' => ManifiestosRecord::getAlertas(),
                'clientes' => $clientes,
                'meses' => $meses,
                'mesSeleccionado' => $mesSeleccionado

            ]);
            return;
        }


        $router->render('manifiestos/vista_previa_manifiesto', [
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
            'nombre' => $nombre,
            'busqueda' => $cliente
        ]);
    }
    // Agregar este método para verificación de consistencia ... eliminado
    public static function guardar_manifiesto(Router $router)
    {
        session_start();
        $alertas = [];
        $cliente_id = $_POST['clientes'] ?? '';
        $nombre_cliente = $_POST['razon_social'] ?? '';
        $mesM = $_POST['mes'] ?? '';
        $mes_numero = $_POST['mes_numero'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $anio = $_POST['anio'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $tipoResiduo = $_POST['tipoResiduo'] ?? '';
        $totalm3 = $_POST['totalm3'] ?? '';
        $dirObra = $_POST['dirObra'] ?? '';
        if (empty($nombre)) {
            ManifiestosRecord::setAlerta('error', 'El nombre del cliente es obligatorio');
        }
        if (empty($dirObra)) {
            ManifiestosRecord::setAlerta('error', 'La dirección de obra es obligatoria');
        }
        if (empty($tipoResiduo)) {
            ManifiestosRecord::setAlerta('error', 'El tipo de residuo es obligatorio');
        }
        if (empty($anio)) {
            ManifiestosRecord::setAlerta('error', 'El año es obligatorio');
        }
        if (empty($totalm3)) {
            ManifiestosRecord::setAlerta('error', 'El total de m³ es obligatorio');
        }


        // SI mes_numero ESTÁ VACÍO, RECALCULARLO
        if (empty($mes_numero)) {
            $meses = [
                "Enero" => "01",
                "Febrero" => "02",
                "Marzo" => "03",
                "Abril" => "04",
                "Mayo" => "05",
                "Junio" => "06",
                "Julio" => "07",
                "Agosto" => "08",
                "Septiembre" => "09",
                "Octubre" => "10",
                "Noviembre" => "11",
                "Diciembre" => "12"
            ];
            $mes_numero = $meses[$mesM] ?? date('m');

            error_log("SOLUCIÓN: mes_numero recalculado = " . $mes_numero);
        }



        //  VERIFICAR SI YA EXISTE EL MANIFIESTO
        $busqueda = ManifiestosRecord::buscarRegistro($cliente_id, $dirObra, $tipoResiduo, $mes_numero, $anio);

        var_dump($busqueda);
        if ($busqueda) {
            ManifiestosRecord::setAlerta('error', 'Ya existe un manifiesto con estos datos');
            self::manifiestos($router);
            return;
        }
        error_log("SOLUCIÓN: mes_numero recalculado = " . $anio);
        $registro_manifiestos = ManifiestosRecord::registrar($cliente_id, $nombre, $dirObra, $tipoResiduo, $mes_numero, $anio, $totalm3);


        // LIMPIAR CUALQUIER OUTPUT ANTES DEL PDF
        if (ob_get_length()) {
            ob_clean();
        }

        self::manifiestos($router);
    }
    public static function vista_manifiesto_guardado(Router $router)
    {
        session_start();
        $alertas = [];
        $cliente_id = $_GET['id_cliente'] ?? '';
        $cliente_nombre = $_GET['cliente'] ?? '';
        $mes = $_GET['mes'] ?? '';
        $anio = $_GET['anio'] ?? '';
        $dirObra = $_GET['dirObra'] ?? '';
        $tipo_residuo = $_GET['tipo_residuo'] ?? '';

        // OBTENER EL ID_CLIENTE DESDE EL NOMBRE
        $datos_cliente = ManifiestosRecord::busquedaPorId($cliente_id);
        if (!$datos_cliente) {
            $alertas['error'][] = 'Cliente no encontrado: ' . $cliente_nombre;
            $cliente_id = null;
        } else {
            $cliente_id = $datos_cliente[0]->id;
        }

        $busqueda = ManifiestosRecord::busquedaSec($cliente_id, $dirObra, $mes, $anio);

        // USAR EL NUEVO MÉTODO CON ID_CLIENTE
        if ($cliente_id) {
            $viajes = ManifiestosRecord::calcularM3($cliente_id, $anio, $mes, $tipo_residuo);
        } else {
            $viajes = 0;
            $alertas['error'][] = 'No se pudo calcular m³ - cliente no válido';
        }

        $totalm3 = $viajes * 7;

        $meses = [
            '01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre'
        ];

        $router->render('manifiestos/vista_manifiesto_guardado', [
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

    public static function generar_PDF(Router $router)
    {

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
                "Enero" => "01",
                "Febrero" => "02",
                "Marzo" => "03",
                "Abril" => "04",
                "Mayo" => "05",
                "Junio" => "06",
                "Julio" => "07",
                "Agosto" => "08",
                "Septiembre" => "09",
                "Octubre" => "10",
                "Noviembre" => "11",
                "Diciembre" => "12"
            ];
            $mes_numero = $meses[$mesM] ?? date('m');

            error_log("SOLUCIÓN: mes_numero recalculado = " . $mes_numero);
        }



        // LIMPIAR CUALQUIER OUTPUT ANTES DEL PDF
        if (ob_get_length()) {
            ob_clean();
        }

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);

        $html = '
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
            color: #333;
            padding: 10px;
        }
        
        .titulo {
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            text-transform: uppercase;
            margin-bottom: 8px;
            padding: 5px;
            background-color: #4a90e2;
            color: white;
            border-radius: 3px;
        }
        
        .seccion {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        
        .seccion-header {
            font-weight: bold;
            font-size: 15px;
            text-transform: uppercase;
            background-color: #4a90e2;
            color: white;
            padding: 4px 8px;
            border-radius: 3px 3px 0 0;
            text-align: center;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        
        table td {
            padding: 4px 6px;
            border: 1px solid #ddd;
            vertical-align: top;
            line-height: 1.2;
        }
        
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .campo-label {
            font-weight: normal;
            color: #333;
            width: 40%;
        }
        
        .campo-valor {
            color: #000;
            font-weight: normal;
        }
        
        .firma-box {
            border: 1px solid #ddd;
            padding: 20px 8px 6px 8px;
            margin-top: 5px;
            text-align: center;
            background-color: #f9f9f9;
            min-height: 40px;
            font-size: 12px;
        }
        
        .nota {
            font-size: 11px;
            color: #666;
            font-style: italic;
            padding: 4px 6px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin-top: 3px;
        }
    </style>

    <div class="titulo">MANIFIESTO DE ENTREGA, TRANSPORTE Y RECEPCIÓN DE RESIDUOS DE MANEJO ESPECIAL</div>

    <!-- SECCIÓN GENERADOR -->
    <div class="seccion">
        <div class="seccion-header">GENERADOR</div>
        <table>
            <tr>
                <td class="campo-label">Razón social de la empresa</td>
                <td class="campo-valor">' . htmlspecialchars($nombre) . '</td>
            </tr>
            <tr>
                <td class="campo-label">Domicilio</td>
                <td class="campo-valor">' . htmlspecialchars($direccion) . '</td>
            </tr>
            <tr>
                <td class="campo-label">Municipio / Estado</td>
                <td class="campo-valor">Guadalajara, Jalisco</td>
            </tr>
            <tr>
                <td class="campo-label">Correo electrónico</td>
                <td class="campo-valor">' . htmlspecialchars($correo) . '</td>
            </tr>
            <tr>
                <td class="campo-label">N° Autorización SEMADET</td>
                <td class="campo-valor"></td>
            </tr>
            <tr>
                <td class="campo-label">Descripción de los residuos</td>
                <td class="campo-valor"></td>
            </tr>
            <tr>
                <td class="campo-label">Tipo de residuos</td>
                <td class="campo-label">Cantidad en m³</td>
            </tr>
            <tr>
                <td class="campo-valor">' . htmlspecialchars($tipoResiduo) . '</td>
                <td class="campo-valor">' . htmlspecialchars($totalm3) . ' m³</td>
            </tr>
        </table>
    </div>

    <!-- SECCIÓN TRANSPORTISTA -->
    <div class="seccion">
        <div class="seccion-header">TRANSPORTISTA</div>
        <table>
            <tr>
                <td class="campo-label">Periodo de recolección</td>
                <td class="campo-valor">' . htmlspecialchars($mesM) . '-' . htmlspecialchars($anio) . '</td>
            </tr>
            <tr>
                <td class="campo-label">Responsable de la entrega</td>
                <td class="campo-valor">' . htmlspecialchars($dirObra) . '</td>
            </tr>
            <tr>
                <td class="campo-label">Instrucciones de manejo</td>
                <td class="campo-valor"></td>
            </tr>
            <tr>
                <td class="campo-label">Nombre / Razón social</td>
                <td class="campo-valor">CONSTRUCTORA SA DE CV</td>
            </tr>
            <tr>
                <td class="campo-label">Domicilio</td>
                <td class="campo-valor">Calle 123 Colonia X Guadalajara, Jalisco</td>
            </tr>
            <tr>
                <td class="campo-label">N° Autorización SEMADET</td>
                <td class="campo-valor">ASDASD 12345</td>
            </tr>
            <tr>
                <td class="campo-label">Descripción del vehículo</td>
                <td class="campo-valor">Volteo 7m³</td>
            </tr>
            <tr>
                <td class="campo-label">N° de placas</td>
                <td class="campo-valor">JGS4046...</td>
            </tr>
            <tr>
                <td class="campo-label">Responsable de recolección</td>
                <td class="campo-valor"></td>
            </tr>
        </table>
        <div class="firma-box">
            Firma
        </div>
        <div class="nota">
            Recibí los residuos en el presente manifiesto para su transporte/Firma
        </div>
    </div>

    <!-- SECCIÓN DESTINO FINAL -->
    <div class="seccion">
        <div class="seccion-header">DESTINO FINAL</div>
        <table>
            <tr>
                <td class="campo-label">Nombre / Razón social</td>
                <td class="campo-valor">E JEMPLO JOSE</td>
            </tr>
            <tr>
                <td class="campo-label">Domicilio</td>
                <td class="campo-valor">CALLE 123 COL. X</td>
            </tr>
            <tr>
                <td class="campo-label">N° Autorización SEMADET</td>
                <td class="campo-valor"></td>
            </tr>
            <tr>
                <td class="campo-label">Oficio</td>
                <td class="campo-valor">E JEMPLO PARCELA</td>
            </tr>
            <tr>
                <td class="campo-label">Sello de recepción</td>
                <td class="campo-valor"></td>
            </tr>
        </table>
        <div class="firma-box">
            Nombre y Firma
        </div>
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
    public static function manifiestos(Router $router)
    {
        session_start();
        $manifiestos = ManifiestosRecord::obtenerHistorialManifiestos();


        $datos_para_vista = [
            'titulo' => 'Manifiestos',
            'manifiestos' => $manifiestos,
            'alertas' => ManifiestosRecord::getAlertas()

        ];


        $router->render('dashboard/manifiestos', $datos_para_vista);
    }

    public static function vista_manifiesto(Router $router)
    {
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
            '01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre'
        ];

        $router->render('manifiestos/vista_previa_manifiesto', [
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

    public static function eliminar()
    {
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
