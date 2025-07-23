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
        //isAuth();
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

        $router->render('auth/crear_manifiesto', [
            'titulo' => 'Crear manifiesto',
            'alertas' => $alertas,
            'clientes' => $clientes,
            'meses' => $meses,
            'mesSeleccionado' => $mesSeleccionado
            
        ]);
    }
    public static function generar_manifiesto(Router $router){
        session_start();
        //isAuth();
        $alertas = [];
        $clienteM = $_POST['clientes'] ?? '';
        $mesM = $_POST['mes'] ?? '';
        $anio = $_POST['anio'] ?? '';
        $dirObra = $_POST['dirObra'] ?? '';
        $mes = date('m', strtotime("01 $mesM 2000"));
        $direccion = ManifiestosRecord::obtenerDir($clienteM); 
        $correo = ManifiestosRecord::obtenerCorreo($clienteM); 
        $codigo = ManifiestosRecord::obtenerCodP($clienteM);
        $municipio = ManifiestosRecord::obtenerMunicipio($clienteM);
        $estado = ManifiestosRecord::obtenerEstado($clienteM);
        $telefono = ManifiestosRecord::obtenerTel($clienteM);
        $nombre = ManifiestosRecord::obtenerNombre($clienteM);
        $tipoResiduo = $_POST['tipoResiduo'] ?? '';
        $viajes = ManifiestosRecord::calcularM3($nombre,$anio,$mes,$tipoResiduo); 
        $totalm3=$viajes*7;
        
        

        $router->render('auth/vista_previa_manifiesto',[
            'titulo' => 'Manifiesto Creado Correctamente',
            'alertas' => $alertas,
            'clienteM' => $clienteM,
            'mesM' => $mesM,
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
    public static function generar_PDF(Router $router){
        session_start();
        //isAuth();
        
        $alertas = [];
        $clienteM = $_POST['clientes'] ?? '';
        $mesM = $_POST['mes'] ?? '';
        $mes = date('m', strtotime("01 $mesM 2000"));
        $nombre = $_POST['nombre'] ?? '';
        $anio = $_POST['anio'] ?? ''; 
        $direccion = $_POST['direccion'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $tipoResiduo = $_POST['tipoResiduo'] ?? '';
        $totalm3 = $_POST['totalm3'] ?? '';
        $dirObra = $_POST['dirObra'] ?? '';
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $busqueda = ManifiestosRecord::buscarRegistro($nombre,$dirObra,$tipoResiduo,$mes,$anio);

        if($busqueda[0]->cliente == $nombre AND $busqueda[0]->obra == $dirObra AND  $busqueda[0]->mes == $mes AND $busqueda[0]->anio == $anio){
            ManifiestosRecord::setAlerta('error', 'Ya existe un manifiesto con estos datos');
            $alertas = ManifiestosRecord::getAlertas();
        }else{
            $registro_manifiestos = ManifiestosRecord::registrar($nombre,$dirObra,$tipoResiduo,$mes,$anio,$totalm3);
        }
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
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("manifiesto_" . $nombre . "_" . $mesM . ".pdf", ["Attachment" => true]);
        $router->render('auth/crear_manifiesto',[
            'titulo' => 'Manifiesto Creado Correctamente'
            
        ]);
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
        $cliente = $_GET['cliente'] ?? '';
        $mes = $_GET['mes'] ?? '';
        $anio = $_GET['anio'] ?? '';
        $dirObra = $_GET['dirObra'] ?? '';
        $tipo_residuo = $_GET['tipo_residuo'] ?? '';
        $busqueda = ManifiestosRecord::busquedaSec($cliente,$dirObra,$mes,$anio);
        $viajes = ManifiestosRecord::calcularM3($cliente,$anio,$mes,$tipo_residuo); 
        $totalm3=$viajes*7;
        $datos_cliente = ManifiestosRecord::busquedaPorNombre($cliente);
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
        $router->render('auth/vista_previa_manifiesto',[
            'titulo' => 'Manifiesto Creado Correctamente',
            'alertas' => $alertas,
            'clienteM' => $cliente,
            'mesM' => $meses[$mes] ?? $mes,
            'anio' => $anio,
            'dirObra' => $dirObra,
            'direccion' => $datos_cliente[0]->domicilio,
            'correo' => $datos_cliente[0]->correo_electronico,
            'tipoResiduo' => $tipo_residuo,
            'totalm3' => $totalm3,
            'codigo' => $datos_cliente[0]->codigo_postal,
            'estado' => $datos_cliente[0]->estado,
            'municipio' => $datos_cliente[0]->municipio,
            'telefono' => $datos_cliente[0]->telefono,
            'nombre' => $cliente
        ]);

    }
}
