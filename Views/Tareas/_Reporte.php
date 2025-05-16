<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Informe de Logros - Dirección de Servicios Generales</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <!-- Contenedor principal para impresión -->
            <div id="formato-informe" class="p-4">
                <!-- Encabezado institucional -->
                <div class="text-center mb-4">
                    <h3 class="mb-1">DIRECCIÓN DE SERVICIOS GENERALES</h3>
                    <h4 class="mb-1">INFORME DE LOGROS Y GESTIÓN</h4>
                    <p class="mb-0">Periodo: <span id="informe-periodo">01/01/2023 - 31/12/2023</span></p>
                </div>
                
                <!-- Resumen ejecutivo -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Resumen de Tareas DSG</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-white bg-success mb-3">
                                    <div class="card-body text-center">
                                        <h1 class="card-title" id="tareas-completadas">87%</h1>
                                        <p class="card-text">Tareas Completadas</p>
                                        <small class="opacity-75">↑ 0% vs periodo anterior</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-warning mb-3">
                                    <div class="card-body text-center">
                                        <h1 class="card-title" id="tareas-pendientes">9%</h1>
                                        <p class="card-text">Tareas Pendientes</p>
                                        <small class="opacity-75">↓ 0% vs periodo anterior</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-danger mb-3">
                                    <div class="card-body text-center">
                                        <h1 class="card-title" id="tareas-canceladas">4%</h1>
                                        <p class="card-text">Tareas Canceladas</p>
                                        <small class="opacity-75">↓ 0% vs periodo anterior</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <strong>Análisis:</strong> El porcentaje de tareas completadas ha aumentado un 12% respecto al periodo anterior, 
                            demostrando una mejora en la eficiencia operativa. Las tareas pendientes y canceladas muestran 
                            una tendencia a la baja, indicando mejor planificación y ejecución.
                        </div>
                    </div>
                </div>
                
                
                
                <!-- Eficiencia por departamento -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Eficiencia por Departamento</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Departamento</th>
                                        <th>T. Completadas</th>
                                        <th>T. Pendientes</th>
                                        <th>T. Canceladas</th>
                                        <th>Eficiencia</th>
                                        <th>Tendencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Plomería</td>
                                        <td>120</td>
                                        <td>8</td>
                                        <td>5</td>
                                        <td>90%</td>
                                        <td><span class="badge bg-success">↑ 5%</span></td>
                                    </tr>
                                    <tr>
                                        <td>Electricidad</td>
                                        <td>95</td>
                                        <td>12</td>
                                        <td>7</td>
                                        <td>83%</td>
                                        <td><span class="badge bg-success">↑ 3%</span></td>
                                    </tr>
                                    <tr>
                                        <td>Mecánica</td>
                                        <td>78</td>
                                        <td>15</td>
                                        <td>10</td>
                                        <td>76%</td>
                                        <td><span class="badge bg-warning">→ 0%</span></td>
                                    </tr>
                                    <tr>
                                        <td>Jardinería</td>
                                        <td>65</td>
                                        <td>5</td>
                                        <td>2</td>
                                        <td>90%</td>
                                        <td><span class="badge bg-success">↑ 8%</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mt-3">
                            <strong>Métrica de Eficiencia:</strong> Calculada como (Tareas Completadas) / (Total de Tareas Asignadas) × 100. 
                            Un valor por encima del 85% se considera excelente, entre 70-85% es aceptable, 
                            y por debajo del 70% requiere revisión.
                        </div>
                    </div>
                </div>
                
              
                <!-- Top 5 tareas más frecuentes -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Tareas Más Frecuentes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Tarea</th>
                                                <th>Frecuencia</th>
                                                <th>% del Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Reparación de tuberías</td>
                                                <td>45</td>
                                                <td>18%</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Mantenimiento eléctrico</td>
                                                <td>32</td>
                                                <td>13%</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Podas de jardín</td>
                                                <td>28</td>
                                                <td>11%</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Limpieza de áreas</td>
                                                <td>25</td>
                                                <td>10%</td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>Reparación de equipos</td>
                                                <td>20</td>
                                                <td>8%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <canvas id="grafico-frecuencia" height="250"></canvas>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3">
                            <strong>Análisis de Pareto:</strong> Las 5 tareas más frecuentes representan el 60% del total de actividades, 
                            lo que sugiere oportunidades para estandarizar procesos o crear plantillas para estas tareas recurrentes.
                        </div>
                    </div>
                </div>
                
                <!-- Datos para impresión -->
                <div class="d-none d-print-block text-center mt-4">
                    <p class="mb-0">Documento generado el: <span id="fecha-generacion"><?= date('d/m/Y H:i') ?></span></p>
                    <p class="mb-0 text-muted">Sistema de Gestión de Tareas - Dirección de Servicios Generales</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-success" onclick="imprimirInforme()">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
            <button type="button" class="btn btn-primary" onclick="generarPDFInforme()">
                <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
            </button>
        </div>
    </div>
</div>

<!-- Incluir Chart.js para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Función para imprimir el informe
/* function imprimirInforme() {
    const elemento = document.getElementById('formato-informe');
    const ventanaImpresion = window.open('', '_blank');
    
    ventanaImpresion.document.write(`
        <html>
            <head>
                <title>Informe de Logros - Dirección de Servicios Generales</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { padding: 20px; }
                    @page { size: auto; margin: 5mm; }
                    @media print {
                        .no-print { display: none !important; }
                        body { padding: 0; }
                        .card { border: 1px solid #ddd !important; }
                        .table { font-size: 0.8rem; }
                    }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                ${elemento.innerHTML}
                <script src="https://cdn.jsdelivr.net/npm/chart.js"><\/script>
                <script>
                    // Recrear gráficos para impresión
                    ${recrearGraficosParaImpresion()}
                <\/script>
            </body>
        </html>
    `);
    ventanaImpresion.document.close();
} */

function generarPDFInforme() {
    // Implementación similar a imprimir pero para PDF
    alert("Función de generación de PDF se implementará con la librería correspondiente");
}

// Variables para los gráficos
let graficoEstados, graficoTiempos, graficoFrecuencia;

$(document).ready(function() {
    // Inicializar gráficos
    inicializarGraficos();
    
    // Configurar periodo del informe
    const fechaInicio = '01/01/2023';
    const fechaFin = '31/12/2023';
    $('#informe-periodo').text(`${fechaInicio} - ${fechaFin}`);
});

function inicializarGraficos() {
    // Gráfico de distribución de tareas
    const ctxEstados = document.getElementById('grafico-estados').getContext('2d');
    graficoEstados = new Chart(ctxEstados, {
        type: 'pie',
        data: {
            labels: ['Completadas', 'Pendientes', 'Canceladas', 'En Progreso'],
            datasets: [{
                data: [87, 9, 4, 0],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#17a2b8'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.raw}%`;
                        }
                    }
                }
            }
        }
    });
    
    // Gráfico de tiempos de ejecución
    const ctxTiempos = document.getElementById('grafico-tiempos').getContext('2d');
    graficoTiempos = new Chart(ctxTiempos, {
        type: 'bar',
        data: {
            labels: ['Urgentes', 'Programadas', 'Preventivas', 'Correctivas'],
            datasets: [{
                label: 'Tiempo Promedio (días)',
                data: [2.4, 5.2, 3.7, 4.5],
                backgroundColor: [
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(23, 162, 184, 0.7)',
                    'rgba(108, 117, 125, 0.7)'
                ],
                borderColor: [
                    'rgba(220, 53, 69, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(108, 117, 125, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Días'
                    }
                }
            }
        }
    });
    
    // Gráfico de frecuencia de tareas
    const ctxFrecuencia = document.getElementById('grafico-frecuencia').getContext('2d');
    graficoFrecuencia = new Chart(ctxFrecuencia, {
        type: 'doughnut',
        data: {
            labels: ['Rep. tuberías', 'Mant. eléctrico', 'Podas', 'Limpieza', 'Rep. equipos', 'Otros'],
            datasets: [{
                data: [45, 32, 28, 25, 20, 100],
                backgroundColor: [
                    '#007bff',
                    '#6610f2',
                    '#6f42c1',
                    '#e83e8c',
                    '#fd7e14',
                    '#6c757d'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const value = context.raw;
                            const percentage = Math.round((value / total) * 100);
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function actualizarGrafico(tipo) {
    graficoEstados.config.type = tipo;
    graficoEstados.update();
}

function recrearGraficosParaImpresion() {
    // Esta función generaría el código JavaScript necesario para recrear los gráficos en la ventana de impresión
    return `
        // Código para recrear gráficos en la ventana de impresión
        // Similar a la función inicializarGraficos() pero para el contexto de impresión
    `;
}
</script>

<style>
/* Estilos específicos para el informe */
#formato-informe {
    background-color: white;
    font-family: Arial, sans-serif;
}

.card {
    margin-bottom: 1.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    font-weight: 600;
}

.table th {
    background-color: #f8f9fa;
}

@media print {
    .no-print {
        display: none !important;
    }
    body {
        padding: 0;
        background: white;
    }
    #formato-informe {
        padding: 0;
    }
    .card {
        page-break-inside: avoid;
    }
}
</style>