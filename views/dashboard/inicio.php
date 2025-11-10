<!-- Alertas de operaciones exitosas/errores -->
<?php if (!empty($_SESSION['alerta'])): ?>
    <div class="alerta <?php echo $_SESSION['alerta']['tipo']; ?> mb-4">
        <p><?php echo $_SESSION['alerta']['mensaje']; ?></p>
    </div>
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>

<style>
    #main_container_s {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 16px;
    }

    #chart { grid-column: 1 / span 12; }
    #ingresos { grid-column: 1 / span 4; }
    #materiales { grid-column: 8 / span 4; }
    #historico { grid-column: 8 / span 4; }
    #prediccion_ingresos { grid-column: 1 / span 4; }

    @media screen and (max-width:1200px) {
        #historico, #prediccion_ingresos, #ingresos, #materiales { grid-column: 1 / span 12; }
        #chart { width: 600px; }
    }
</style>

<?php
// Inicializar arrays para evitar errores
$datosVista = [];
$materiales = [];
$datosClientes = [];
$datosPrediccionIngresos = [];
$prediccionIngresos = [];
$datosUltimoAnio = [];

// Datos para gráficos
if(!empty($datos)){
    foreach ($datos as $material) {
        $datosVista[] = ['x' => $material->servicio, 'y' => intval($material->num_pedidos)];
    }
}

if(!empty($ultimoMes)){
    foreach ($ultimoMes as $pedido) {
        $materiales[] = ['x' => $pedido->servicio, 'y' => intval($pedido->num_pedidos)];
    }
}

if(!empty($clientes)){
    foreach ($clientes as $cliente) {
        $datosClientes[] = ['x' => $cliente->nombre_cliente, 'y' => intval($cliente->ingresos)];
    }
}

if(!empty($ingresosMes)){
    foreach ($ingresosMes as $pred) {
        $datosPrediccionIngresos[] = floatval($pred->ingresos);
    }
}

if(!empty($prediccionMes)){
    foreach ($prediccionMes as $in) {
        $prediccionIngresos[] = floatval($in->ingresos);
    }
}

if(!empty($ingresosLastYear)){
    foreach ($ingresosLastYear as $casos) {
        $datosUltimoAnio[] = floatval($casos->ingresos);
    }
}
?>

<div id="main_container_s">
    <section id="chart"></section>
    <section id="prediccion_ingresos"></section>
    <section id="historico"></section>
    <section id="ingresos">
        <h4>Ingresos por clientes del último mes</h4>
    </section>
    <section id="materiales"></section>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
function loadChart() {
    const data = <?= json_encode($datosVista) ?>;
    const data2 = <?= json_encode($materiales) ?>;
    const data3 = <?= json_encode($datosClientes) ?>;
    const prediccionIngresos = <?= json_encode($prediccionIngresos) ?>;
    const ingresosMes = <?= json_encode($datosPrediccionIngresos) ?>;
    const data5 = <?= json_encode($datosUltimoAnio) ?>;
    const currentyear = <?= $currentYear ?>;
    const lastyear = <?= $lastYear ?>;

    // Gráfico Materiales más demandados
    var chart = new ApexCharts(document.querySelector("#chart"), {
        series: [{ data }],
        legend: { show: false },
        chart: { height: 350, width: 1200, type: 'treemap' },
        title: { text: 'Materiales más demandados para el siguiente periodo' }
    });
    chart.render();

    // Gráfico Ingresos por cliente
    var chart2 = new ApexCharts(document.querySelector("#ingresos"), {
        series: [{ data: data3 }],
        chart: { width: 800, type: 'pie' },
        responsive: [{ breakpoint: 1200, options: { chart: { width: 600 }, legend: { position: 'bottom' }}}]
    });
    chart2.render();

    // Gráfico Materiales/Servicios último mes
    var chart3 = new ApexCharts(document.querySelector("#materiales"), {
        series: [{ data: data2 }],
        chart: { type: 'bar', height: 380 },
        plotOptions: { bar: { barHeight: '100%', distributed: true, horizontal: true, dataLabels: { position: 'bottom' } } },
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: { colors: ['#fff'] },
            formatter: function(val, opt) { return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val; },
        },
        stroke: { width: 1, colors: ['#fff'] },
        yaxis: { labels: false },
        title: { text: 'Materiales/Servicios del último mes', align: 'center', floating: true }
    });
    chart3.render();

    // Gráfico Predicción de ingresos siguiente periodo
    var chart4 = new ApexCharts(document.querySelector("#prediccion_ingresos"), {
        series: [{ name: "Ingresos $", data: prediccionIngresos }],
        chart: { height: 250, width: 500, type: 'line', zoom: { enabled: false } },
        dataLabels: { enabled: false },
        stroke: { curve: 'straight' },
        title: { text: 'Predicción de ingresos para el siguiente periodo', align: 'center' },
        xaxis: { categories: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'] }
    });
    chart4.render();

    // Gráfico Comparación año actual vs anterior
    var chart5 = new ApexCharts(document.querySelector("#historico"), {
        series: [{ name: currentyear, data: ingresosMes }, { name: lastyear, data: data5 }],
        chart: { height: 300, width: 500, type: 'line', zoom: { enabled: false }, toolbar: { show: false } },
        colors: ['#77B6EA', '#545454'],
        dataLabels: { enabled: true },
        stroke: { curve: 'smooth' },
        title: { text: 'Comparación año anterior al año en curso', align: 'left' },
        xaxis: { categories: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'] },
        yaxis: { title: { text: 'Ingresos' } }
    });
    chart5.render();
}

document.addEventListener('DOMContentLoaded', loadChart, { once: true });
</script>
