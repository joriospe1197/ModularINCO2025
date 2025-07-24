<?php

namespace Controllers;


use Model\WeeklyHistory;
use Model\WeeklyRecord;
use MVC\Router;




class WeeklyController {

    public static function create_weekly_history(Router $router){
        session_start();
        //isAuth();
        $alertas = []; 
        $semana = new WeeklyHistory;
        $choferes = WeeklyRecord::allChoferes();
        $choferSeleccionado = $_GET['chofer'] ?? 0;
        


        if(isset($choferes[$choferSeleccionado])){
            $chofer = $choferes[$choferSeleccionado];
            $nombre = $chofer->nombre;

        }else{
            $nombre = " ";
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $semana->sincronizar($_POST);
            $choferSeleccionado = $_POST['chofer'] ?? 0;
            $alertas = $semana->validarNuevoRegistro();
            
            if(empty($alertas)){
                // Verificar si el primer folio ya esta asociado a una semana
                $existePrimerFolio = WeeklyHistory::where('primer_folio', $semana->primer_folio);
                if($existePrimerFolio){
                    WeeklyHistory::setAlerta('error', 'Ese folio ya tiene semana registrada');
                    $alertas = WeeklyHistory::getAlertas();
                }

                // Verificar si el ultimo folio ya esta asociado a una semana
                $existeUltimoFolio = WeeklyHistory::where('ultimo_folio', $semana->ultimo_folio);
                if($existeUltimoFolio){
                    WeeklyHistory::setAlerta('error', 'Ese folio ya tiene semana registrada');
                    $alertas = WeeklyHistory::getAlertas();
                }

                // Si no hay alertas, procesar el registro
                if(empty($alertas)){

                    // Crear un nuevo usuario
                    $saldoAnterior = WeeklyHistory::obtenerSaldoAnterior($choferSeleccionado, $semana->primer_folio);
                    $semana->saldo_anterior_1 = $saldoAnterior;
                    $resultado = $semana->guardar();
                    if($resultado){
                        WeeklyHistory::setAlerta('error', 'Se realizó el registro semanal');
                        $alertas = WeeklyHistory::getAlertas();
                        header('Location: /historial_de_pedidos');
                        
                    }
                }
            }
        }
        // Render a la vista
        $router->render('auth/create_weekly_history', [
            'titulo' => 'Crear semana',
            'semana' => $semana,
            'alertas' => $alertas,
            'choferes' => $choferes,
            'nombre' => $nombre,
            'choferSeleccionado' => $choferSeleccionado,
            
        ]);
    }

    public static function historial_semanal(Router $router)
    {
        session_start();
        //isAuth();
        $choferes = WeeklyRecord::allChoferes();
        $choferSeleccionado = $_GET['chofer'] ?? $choferes[0]->idempleado;
        $semanas = WeeklyRecord::obtenerSemanasPorChofer($choferSeleccionado); 
        $semanaSeleccionada = isset($_GET['semana']) ? intval($_GET['semana']) : 0;
        
        

        if (isset($semanas[$semanaSeleccionada])) {
            $semana = $semanas[$semanaSeleccionada];
            $primer_folio = $semana->primer_folio;
            $ultimo_folio = $semana->ultimo_folio;
            $justificacion = $semana->justificacion;
            
            $total_gastos = WeeklyRecord::calcularGastosTotal($primer_folio,$ultimo_folio);
            $total_pagados = WeeklyRecord::calcularPagadosTotal($primer_folio,$ultimo_folio);
            $total_depositos = WeeklyRecord::calcularDepositosTotal($primer_folio,$ultimo_folio);
            $total_almacen = WeeklyRecord::calcularAlmacenTotal($primer_folio,$ultimo_folio);
            $total_costo = WeeklyRecord::calcularCostoTotal($primer_folio, $ultimo_folio);
            $registros = WeeklyRecord::obtenerPedidos($primer_folio, $ultimo_folio);
            $saldo_anterior_1 = WeeklyRecord::obtenerSaldoAnterior($choferSeleccionado,$primer_folio);
            $saldo = $semana->saldo_actual;
        } else {
            $registros = [];
            $saldo = 0;
            $justificacion = 0;
            $semanaSeleccionada = 0;
        }
        
        if(isset($choferes[$choferSeleccionado])){
            $chofer = $choferes[$choferSeleccionado];
            $nombre = $chofer->nombre;
            $idempleado = $chofer->idempleado;

        }else{
            $nombre = " ";
        }

        $datos_para_vista = [
            'titulo' => 'Historial de pedidos',
            'registros' => $registros,
            'saldo' => $saldo,
            'justificacion' => $justificacion,
            'semanas' => $semanas,
            'semanaSeleccionada' => $semanaSeleccionada,
            'totalGastos' => $total_gastos,
            'totalPagados' => $total_pagados,
            'totalDepositos' => $total_depositos,
            'totalAlmacen' => $total_almacen,
            'totalCosto' => $total_costo,
            'choferes' => $choferes,
            'nombre' => $nombre,
            'idempleado' => $idempleado,
            'choferSeleccionado' => $choferSeleccionado,
            'saldo_anterior_1' => $saldo_anterior_1,
            
            
        ];
        
        
        $router->render('dashboard/historial_de_pedidos', $datos_para_vista);
    }
    public static function edit_week_pr(Router $router){
        //isAuth();
        session_start();
        $choferSeleccionado = $_GET['chofer'] ?? 0;
        $semanaSeleccionada = $_GET['semana'] ?? 0;
        $primer_folio = $_GET['primer_folio'] ?? 0;
        $ultimo_folio = $_GET['ultimo_folio'] ??0;


        $semanas = WeeklyRecord::obtenerSemanasPorChofer($choferSeleccionado);

        if (isset($semanas[$semanaSeleccionada])) {
            $semana = $semanas[$semanaSeleccionada];
            $justificacion = $semana->justificacion;
        }else{
            $justificacion = '';
        }
        
        $registros = WeeklyRecord::obtenerVistaEditar($primer_folio,$ultimo_folio);

        $router->render('auth/edit_week', [
            'titulo' => 'Editar semana',
            'choferSeleccionado' => $choferSeleccionado,
            'semanaSeleccionada' => $semanaSeleccionada,
            'primer_folio' => $primer_folio,
            'ultimo_folio' => $ultimo_folio,
            'registros' => $registros,
            'justificacion' => $justificacion,
            
        ]);
    }
    public static function update_week(Router $router){
        session_start();
        $alertas=[];

        $justificacion = $_POST['justificacion'] ?? '';
        $primer_folio_new = $_POST['primer_folio'] ?? 0;
        $ultimo_folio_new = $_POST['ultimo_folio'] ?? 0;
        $primer_folio_actual = $_POST['primer_folio_actual'] ?? 0;
        $choferSeleccionado = $_POST['choferSeleccionado'] ?? 0; 
        //isAuth();
        $semana = WeeklyHistory::where('primer_folio', $primer_folio_actual);
        if (!$semana) {
            WeeklyHistory::setAlerta('error', 'La semana que intentas actualizar no existe.');
            $alertas = WeeklyHistory::getAlertas();
        } else {
            // Comprobar si se modificaron los folios
            $foliosModificados = $primer_folio_new != $semana->primer_folio || $ultimo_folio_new != $semana->ultimo_folio;
    
            if ($foliosModificados) {
                // Validar que los nuevos folios no estén en uso por otra semana
                $conflicto = WeeklyRecord::rangoFoliosYaRegistrado($primer_folio_actual,$primer_folio_new, $ultimo_folio_new, $semana->primer_folio);
    
                if ($conflicto) {
                    WeeklyHistory::setAlerta('error', 'El nuevo rango de folios ya pertenece a otra semana.');
                    $alertas = WeeklyHistory::getAlertas();
                }
            }
    
            // Si no hay alertas, actualizar la semana
            if (empty($alertas)) {
                WeeklyRecord::updateWeek(
                    $semana->primer_folio, // clave primaria
                    $primer_folio_new,
                    $ultimo_folio_new,
                    $justificacion,
                    $choferSeleccionado,
                );
    
                WeeklyHistory::setAlerta('exito', 'Semana actualizada correctamente.');
                $_SESSION['alertas'] = WeeklyHistory::getAlertas();
                header('Location: /historial_de_pedidos');
                exit;
            }
        }
        $_SESSION['alertas'] = $alertas;
        header("Location: /edit_week?chofer={$semana->chofer}&semana={$semana->primer_folio}");
    }
    public static function remove_week(Router $router){
        //isAuth();
        session_start();
        $choferSeleccionado = $_GET['chofer'] ?? 0;
        $semanaSeleccionada = $_GET['semana'] ?? 0;
        $primer_folio = $_GET['primer_folio'] ?? 0;
        $ultimo_folio = $_GET['ultimo_folio'] ??0;


        $semanas = WeeklyRecord::obtenerSemanasPorChofer($choferSeleccionado);

        if (isset($semanas[$semanaSeleccionada])) {
            $semana = $semanas[$semanaSeleccionada];
            $justificacion = $semana->justificacion;
        }else{
            $justificacion = '';
        }
        
        $registros = WeeklyRecord::obtenerVistaEditar($primer_folio,$ultimo_folio);

        $router->render('auth/remove_week', [
            'titulo' => 'Eliminar Semana',
            'choferSeleccionado' => $choferSeleccionado,
            'semanaSeleccionada' => $semanaSeleccionada,
            'primer_folio' => $primer_folio,
            'ultimo_folio' => $ultimo_folio,
            'registros' => $registros,
            
        ]);
    }
    public static function delete_week(Router $router){
        session_start();
        //isAuth();
        $primer_folio_actual = $_POST['primer_folio_actual'] ?? 0;
        $choferSeleccionado = $_POST['choferSeleccionado'] ?? 0; 

        WeeklyRecord::delete_week($choferSeleccionado,$primer_folio_actual);
        header('Location: /historial_de_pedidos');


    }        
    public static function search_for_week(Router $router){
        session_start();
        //isAuth(); // Asegúrate de que el usuario esté autenticado
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lógica para encontrar el usuario
            $semana = new WeeklyHistory($_POST);  // Creamos el objeto Usuario con los datos del formulario
    
            // Validar el idempleado
            $alertas = $semana->validarFolio();
    
            if (empty($alertas)) {
                // Verificar que el usuario exista
                $semana = WeeklyHistory::where('primer_folio', $semana->primer_folio);  // Buscar el usuario por idempleado
    
                if (!$semana) {  // Verificar que el usuario exista y esté confirmado
                    WeeklyHistory::setAlerta('error', 'Ese folio no existe');
                } else {
                    // Aquí redirigimos al formulario de edición pasando el idempleado
                    header('Location: /edit_folio?primer_folio=' . $semana->primer_folio);
                    exit;  // Asegúrate de que no se ejecute más código después de la redirección
                }
            }
        }
    
        // Si no es un POST, simplemente renderizamos la vista con las alertas
        $alertas = WeeklyHistory::getAlertas();
    
        // Renderizar la vista
        $router->render('auth/edit_week', [
            'titulo' => 'Buscando folio',
            'alertas' => $alertas,
        ]);
    }
    public static function edit_folio(Router $router){
        session_start();
        //isAuth(); // Asegúrate de que el usuario esté autenticado
        $alertas = [];
        $semana = null;
    
        // Verificamos si se ha enviado el idempleado por GET (cuando el usuario es redirigido desde search_user)
        if (isset($_GET['primer_folio']) && $_GET['primer_folio']) {
            $primer_folio = $_GET['primer_folio'];
    
            // Buscamos al usuario en la base de datos por idempleado
            $semana = WeeklyHistory::where('primer_folio', $primer_folio);
    
            if (!$semana) {
               
                WeeklyHistory::setAlerta('error', 'El folio aun no tiene una semana asociada');
            }
        }
    
       
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $semana) {
           
            $semana->sincronizar($_POST);
          
            if (empty($semana->primer_folio)) {
                WeeklyHistory::setAlerta('error', 'El primer folio no puede estar vacío');
            }
            if (empty($semana->ultimo_folio)) {
                WeeklyHistory::setAlerta('error', 'El último folio no puede estar vacío');
            }
            if (empty($semana->justificacion)) {
                WeeklyHistory::setAlerta('error', 'La justificación no puede estar vacía');
            }

            $alertas = WeeklyHistory::getAlertas();
            if (empty($alertas)) {

                $resultado = $semana->guardar(); 
    
                if ($resultado) {
                    WeeklyHistory::setAlerta('exito', 'Semana actualizada correctamente');
                    header('Location: /message_update_user'); 
                    exit;
                } else {
                    WeeklyHistory::setAlerta('error', 'Error al actualizar la semana');
                }
            }
        }

        $alertas = WeeklyHistory::getAlertas();

        $router->render('auth/edit_week', [
            'titulo' => 'Editar datos de la semana',
            'semana' => $semana,  // Pasamos el usuario a la vista para mostrar los datos actuales
            'alertas' => $alertas
        ]);
    }
    public static function message_update_user(Router $router){
        session_start();
        //isAuth();
        $router->render('auth/message_update_user', [
            'titulo' => 'Semana actualizada correctamente'
        ]);
    }     

}