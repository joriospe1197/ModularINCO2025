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

    #chart {
        grid-column: 1 / span 12;
    }

    #ingresos {
        grid-column: 1 / span 4;
    }

    #materiales {
        grid-column: 8 / span 4;
    }

    #historico {
        grid-column: 8 / span 4;
    }

    #prediccion_ingresos {
        grid-column: 1 / span 4;
    }

    @media screen and (max-width:1200px) {
        #historico {
            grid-column: 1 / span 12;
        }

        #prediccion_ingresos {
            grid-column: 1 / span 12;
        }

        #ingresos {
            grid-column: 1 / span 12;
        }

        #materiales {
            grid-column: 1 / span 12;
            /* ocupa todo el ancho */
            grid-row: auto;
            /* se acomoda en la siguiente fila debajo */
        }
        #chart{
            width: 600px;
        }
    }
</style>

<?php foreach ($datos as $material): ?>
    <?php $datosVista[] = ['x' => $material->servicio, 'y' => intval($material->num_pedidos)]; ?>
<?php endforeach; ?>

<?php foreach ($ultimoMes as $pedido): ?>
    <?php $materiales[] = ['x' => $pedido->servicio, 'y' => intval($pedido->num_pedidos)]; ?>
<?php endforeach; ?>

<?php foreach ($clientes as $cliente): ?>
    <?php $datosClientes[] = ['x' => $cliente->nombre_cliente, 'y' => intval($cliente->ingresos)]; ?>
<?php endforeach; ?>

<?php foreach ($ingresosMes as $pred): ?>
    <?php $datosPrediccionIngresos[] = floatval($pred->ingresos) ?>
<?php endforeach; ?>

<?php foreach ($prediccionMes as $in): ?>
    <?php $datosPrediccionIngresos[] = floatval($in->ingresos) ?>
<?php endforeach; ?>
<?php foreach ($ingresosLastYear as $casos): ?>
    <?php $datosUltimoAnio[] = floatval($casos->ingresos) ?>
<?php endforeach; ?>
<?php $test ?>
<div id="main_container_s">
    <section id="chart"></section>
    <section id="prediccion_ingresos"></section>
    <section id="historico"></section>
    
    <section id="ingresos">
        <h4> Ingresos por clientes del ultimo mes </h4>
    </section>
    <section id="materiales"></section>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    function loadChart() {
        const data = <?= json_encode($datosVista, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        const data2 = <?= json_encode($materiales, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        const data3 = <?= json_encode($datosClientes, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        const data4 = <?= json_encode($datosPrediccionIngresos, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        const currentyear = <?= $currentYear ?>;
        const lastyear = <?= $lastYear ?>;
        const data5 = <?= json_encode($datosUltimoAnio, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        //Gráfico de los 3 materiales con mayor demanda para el siguiente periodo
        var options = {
            series: [{
                data
            }],

            legend: {
                show: false
            },
            chart: {
                height: 350,
                width: 1200,
                type: 'treemap'
            },
            title: {
                text: 'Materiales mas demandados para el siguiente periodo'
            }

        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

        //Carga del gráfico para los ingresos
        var options2 = {
            series: [{
                data: data3
            }],
            chart: {
                width: 800,
                type: 'pie',
            },

            responsive: [{
                breakpoint: 1200,
                options: {
                    chart: {
                        width: 600
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart2 = new ApexCharts(document.querySelector("#ingresos"), options2);
        chart2.render();

        //Gráfico de la cantidad de servicios y cuales del ultimo materiales
        var options3 = {

            series: [{
                data: data2
            }],
            chart: {
                type: 'bar',
                height: 380
            },

            plotOptions: {
                bar: {
                    barHeight: '100%',
                    distributed: true,
                    horizontal: true,
                    dataLabels: {
                        position: 'bottom'
                    },
                }
            },
            colors: ['#33b2df', '#546E7A', '#d4526e', '#13d8aa', '#A5978B', '#2b908f', '#f9a3a4', '#90ee7e',
                '#f48024', '#69d2e7'
            ],
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: {
                    colors: ['#fff']
                },
                formatter: function(val, opt) {
                    return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
                },
                offsetX: 0,
                dropShadow: {
                    enabled: true
                }
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            yaxis: {
                labels: false,
            },
            title: {
                text: 'Materiales/Servicios del ultimo mes',
                align: 'center',
                floating: true
            },
            subtitle: {
                text: '',
                align: 'center',
            },
            tooltip: {
                theme: 'dark',
                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: function() {
                            return ''
                        }
                    }
                }
            },
            responsive: [{
                breakpoint: 1200,
                options: {
                    chart: {
                        width: 450
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart3 = new ApexCharts(document.querySelector("#materiales"), options3);
        chart3.render();


        var options4 = {
            series: [{
                name: "Ingresos $",
                data: data4
            }],
            chart: {
                height: 250,
                width: 500,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight'
            },
            title: {
                text: 'Predicción de ingresos para el siguiente periodo',
                align: 'center'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },

            },
            xaxis: {
                categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            }

        };

        var chart4 = new ApexCharts(document.querySelector("#prediccion_ingresos"), options4);
        chart4.render();

        var options5 = {
            series: [{
                    name: currentyear,
                    data: data4
                },
                {
                    name: lastyear,
                    data: data5
                }
            ],
            chart: {
                height: 300,
                width: 500,
                type: 'line',
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 18,
                    left: 7,
                    blur: 10,
                    opacity: 0.5
                },
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                }
            },
            colors: ['#77B6EA', '#545454'],
            dataLabels: {
                enabled: true,
            },
            stroke: {
                curve: 'smooth'
            },
            title: {
                text: 'Comparación año anterior al año en curso',
                align: 'left'
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            },
            markers: {
                size: 1
            },

            yaxis: {
                title: {
                    text: 'Ingresos'
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -25,
                offsetX: -5
            }
        };

        var chart5 = new ApexCharts(document.querySelector("#historico"), options5);
        chart5.render();


    }

    document.addEventListener('DOMContentLoaded', loadChart, {
        once: true
    });
</script>
