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

    @media screen and (max-width:1200px){
        #historico, #prediccion_ingresos, #ingresos, #materiales { grid-column: 1 / span 12; }
        #chart { width: 600px; }
    }
</style>

<?php
// Inicializar arrays para gráficos
$datosVista = [];
$materiales = [];
$datosClientes = [];
$datosPrediccionIngresos = [];
$datosUltimoAnio = [];

foreach ($datos as $material) {
    $datosVista[] = ['x' => $material->servicio, 'y' => intval($material->num_pedidos)];
}

foreach ($ultimoMes as $pedido) {
    $materiales[] = ['x' => $pedido->servicio, 'y' => intval($pedido->num_pedidos)];
}

foreach ($clientes as $cliente) {
    $datosClientes[] = ['x' => $cliente->nombre_cliente, 'y' => intval($cliente->ingresos)];
}

foreach ($ingresosMes as $pred) {
    $datosPrediccionIngresos[] = floatval($pred->ingresos);
}

foreach ($prediccionMes as $in) {
    $datosPrediccionIngresos[] = floatval($in->ingresos);
}

foreach ($ingresosLastYear as $casos) {
    $datosUltimoAnio[] = floatval($casos->ingresos);
}
?>

<div id="main_container_s">
    <section id="chart"></section>
    <section id="prediccion_ingresos"></section>
    <section id="historico"></section>
    <section id="ingresos">
        <h4> Ingresos por clientes del último mes </h4>
    </section>
    <section id="materiales"></section>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
function loadChart() {
    const data = <?= json_encode($datosVista, JSON_UNESCAPED_UNICODE) ?>;
    const data2 = <?= json_encode($materiales, JSON_UNESCAPED_UNICODE) ?>;
    const data3 = <?= json_encode($datosClientes, JSON_UNESCAPED_UNICODE) ?>;
    const data4 = <?= json_encode($datosPrediccionIngresos, JSON_UNESCAPED_UNICODE) ?>;
    const data5 = <?= json_encode($datosUltimoAnio, JSON_UNESCAPED_UNICODE) ?>;
    const currentyear = <?= $currentYear ?>;
    const lastyear = <?= $lastYear ?>;

    // Gráfico Treemap: Top materiales
    new ApexCharts(document.querySelector("#chart"), {
        series: [{ data }],
        chart: { height: 350, width: 1200, type: 'treemap' },
        legend: { show: false },
        title: { text: 'Materiales más demandados para el siguiente periodo' }
    }).render();

    // Gráfico Pie: ingresos por cliente
    new ApexCharts(document.querySelector("#ingresos"), {
        series: [{ data: data3 }],
        chart: { width: 800, type: 'pie' },
        responsive: [{ breakpoint: 1200, options: { chart: { width: 600 }, legend: { position: 'bottom' } } }]
    }).render();

    // Gráfico Bar: materiales/servicios último mes
    new ApexCharts(document.querySelector("#materiales"), {
        series: [{ data: data2 }],
        chart: { type: 'bar', height: 380 },
        plotOptions: { bar: { barHeight: '100%', distributed: true, horizontal: true, dataLabels: { position: 'bottom' } } },
        colors: ['#33b2df','#546E7A','#d4526e','#13d8aa','#A5978B','#2b908f','#f9a3a4','#90ee7e','#f48024','#69d2e7'],
        dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, formatter: (val,opt)=> opt.w.globals.labels[opt.dataPointIndex] + ":  " + val },
        stroke: { width: 1, colors: ['#fff'] },
        yaxis: { labels: false },
        title: { text: 'Materiales/Servicios del último mes', align: 'center', floating: true },
        responsive: [{ breakpoint: 1200, options: { chart: { width: 450 }, legend: { position: 'bottom' } } }]
    }).render();

    // Gráfico Line: Predicción ingresos siguiente periodo
    new ApexCharts(document.querySelector("#prediccion_ingresos"), {
        series: [{ name: "Ingresos $", data: data4 }],
        chart: { height: 250, width: 500, type: 'line', zoom: { enabled: false } },
        dataLabels: { enabled: false },
        stroke: { curve: 'straight' },
        title: { text: 'Predicción de ingresos para el siguiente periodo', align: 'center' },
        xaxis: { categories: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'] }
    }).render();

    // Gráfico Line: comparación año anterior
    new ApexCharts(document.querySelector("#historico"), {
        series: [
            { name: currentyear, data: data4 },
            { name: lastyear, data: data5 }
        ],
        chart: { height: 300, width: 500, type: 'line', dropShadow: { enabled: true, color: '#000', top: 18, left: 7, blur: 10, opacity: 0.5 }, zoom: { enabled: false }, toolbar: { show: false } },
        colors: ['#77B6EA','#545454'],
        dataLabels: { enabled: true },
        stroke: { curve: 'smooth' },
        title: { text: 'Comparación año anterior al año en curso', align: 'left' },
        grid: { borderColor: '#e7e7e7', row: { colors: ['#f3f3f3','transparent'], opacity: 0.5 } },
        xaxis: { categories: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'] },
        markers: { size: 1 },
        yaxis: { title: { text: 'Ingresos' } },
        legend: { position: 'top', horizontalAlign: 'right', floating: true, offsetY: -25, offsetX: -5 }
    }).render();
}

document.addEventListener('DOMContentLoaded', loadChart, { once: true });
</script>
