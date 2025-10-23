<?php
/**
 * Vista de Estadísticas Personalizadas - Dirección de Servicios Generales
 */
?>

<style>
.estadisticas-container {
    background: #f8f9fa;
    min-height: 100vh;
}
.card-estadistica {
    border: none;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}
.card-header-estadistica {
    border-radius: 10px 10px 0 0 !important;
    padding: 1rem 1.5rem;
}
.grafico-container {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    height: 100%;
}
.datos-container {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    height: 100%;
}
.estadistica-row {
    min-height: 500px;
}
.explicacion-estadistica {
    background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
    border-left: 4px solid #2196F3;
}
.btn-exportar {
    border-radius: 25px;
    padding: 0.5rem 2rem;
}
</style>

<div class="estadisticas-container">
    <div class="page-inner">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row mb-4">
            <div>
                <h1 class="text-dark mb-2">Reporte Estadístico Personalizado</h1>
                <h4 class="text-muted mb-0">Dirección de Servicios Generales</h4>
            </div>
            <div class="mt-3 mt-md-0">
                <button type="button" class="btn btn-success btn-exportar" onclick="generarPDF()">
                    <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                </button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card card-estadistica mb-4">
            <div class="card-header card-header-estadistica bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Estadísticas</h5>
            </div>
            <div class="card-body">
                <form id="formEstadisticas">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="tipoEstadistica" class="form-label fw-bold">Tipo de Estadística</label>
                            <select class="form-select form-select-lg" id="tipoEstadistica" required>
                                <option value="">Seleccione una opción</option>
                                <option value="recurso_consumible">Recurso consumible más utilizado</option>
                                <option value="mes_mas_tareas">Mes con más tareas realizadas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="fechaInicio" class="form-label fw-bold">Fecha Inicio</label>
                            <input type="date" class="form-control form-control-lg" id="fechaInicio" required>
                        </div>
                        <div class="col-md-3">
                            <label for="fechaFin" class="form-label fw-bold">Fecha Fin</label>
                            <input type="date" class="form-control form-control-lg" id="fechaFin" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-lg w-100" onclick="generarEstadistica()">
                                <i class="fas fa-chart-bar me-2"></i>Generar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contenido Principal - Layout Horizontal -->
        <div class="contenidoMostrar">
            <!-- Explicación de resultados -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card card-estadistica">
                        <div class="card-body">
                            <div id="explicacionEstadistica" class="explicacion-estadistica alert alert-info mb-0 p-4">
                                <h6 class="alert-heading mb-3"><i class="fas fa-info-circle me-2"></i>Instrucciones</h6>
                                <p class="mb-0">Seleccione el tipo de estadística, el rango de fechas y haga clic en "Generar" para visualizar los resultados. Los datos se mostrarán en el gráfico a la izquierda y en la tabla detallada a la derecha.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico y Datos en layout horizontal -->
            <div class="row estadistica-row mb-4">
                <!-- Columna del Gráfico -->
                <div class="col-lg-8 col-md-7 mb-4 mb-md-0">
                    <div class="card card-estadistica h-100">
                        <div class="card-header card-header-estadistica bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i><span id="tituloGrafico">Estadística Seleccionada</span></h5>
                        </div>
                        <div class="card-body grafico-container">
                            <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                                <canvas id="graficoEstadistica"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna de la Tabla de Datos -->
                <div class="col-lg-4 col-md-5">
                    <div class="card card-estadistica h-100">
                        <div class="card-header card-header-estadistica bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Detalle de Datos</h5>
                        </div>
                        <div class="card-body datos-container">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered table-striped table-hover mb-0" id="tablaDetalleEstadisticas">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <!-- Las columnas se generarán dinámicamente según el tipo de estadística -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Los datos se llenarán con JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Adicional (si es necesaria) -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-estadistica">
                        <div class="card-header card-header-estadistica bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Resumen Estadístico</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center" id="resumenEstadistico">
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="border rounded p-3 bg-light">
                                        <h6 class="text-muted mb-2">Período Analizado</h6>
                                        <h4 class="text-primary" id="periodoAnalizado">-</h4>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="border rounded p-3 bg-light">
                                        <h6 class="text-muted mb-2">Total Registros</h6>
                                        <h4 class="text-success" id="totalRegistros">-</h4>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="border rounded p-3 bg-light">
                                        <h6 class="text-muted mb-2">Fecha Generación</h6>
                                        <h4 class="text-info" id="fechaGeneracion">-</h4>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="border rounded p-3 bg-light">
                                        <h6 class="text-muted mb-2">Estado</h6>
                                        <span class="badge bg-warning text-dark" id="estadoReporte">Pendiente</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Incluir las librerías para PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
// Configuración global
window.estadisticaChart = null;

// Función para actualizar el resumen
function actualizarResumen(fechaInicio, fechaFin, totalRegistros) {
    document.getElementById('periodoAnalizado').textContent = 
        `${fechaInicio} a ${fechaFin}`;
    document.getElementById('totalRegistros').textContent = totalRegistros;
    document.getElementById('fechaGeneracion').textContent = new Date().toLocaleDateString();
    document.getElementById('estadoReporte').textContent = 'Generado';
    document.getElementById('estadoReporte').className = 'badge bg-success';
}

async function generarPDF() {
    try {
        if (!window.jspdf || !window.html2canvas) {
            throw new Error("Las librerías necesarias no están cargadas");
        }
        
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'pt', 'a4');
        
        const element = document.querySelector('.contenidoMostrar');
        
        if (!element) {
            throw new Error("No se encontró el contenido a exportar");
        }
        
        const canvas = await html2canvas(element, {
            scale: 1.5,
            logging: true,
            useCORS: true,
            scrollX: 0,
            scrollY: 0,
            backgroundColor: '#FFFFFF'
        });
        
        const imgData = canvas.toDataURL('image/png');
        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth() - 20;
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
        
        pdf.addImage(imgData, 'PNG', 10, 10, pdfWidth, pdfHeight);
        
        pdf.save('reporte_estadistico_' + new Date().toLocaleDateString() + '.pdf');
        
    } catch (error) {
        console.error("Error detallado:", error);
        alert("No se pudo generar el PDF: " + error.message);
    }
}

async function generarEstadistica() {
    const tipoEstadistica = document.getElementById('tipoEstadistica').value;
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;
    const departamento = "1";

    if (!tipoEstadistica || !fechaInicio || !fechaFin) {
        alert('Por favor complete todos los campos obligatorios');
        return;
    }

    try {
        // Mostrar loading
        document.getElementById('estadoReporte').textContent = 'Generando...';
        document.getElementById('estadoReporte').className = 'badge bg-info';

        const datos = await obtenerDatosEstadisticos(tipoEstadistica, fechaInicio, fechaFin, departamento);
        
        // Actualizar interfaz
        document.getElementById('tituloGrafico').textContent = obtenerTituloEstadistica(tipoEstadistica);
        document.getElementById('explicacionEstadistica').innerHTML = generarExplicacion(tipoEstadistica, datos);
        
        // Generar gráfico y tabla
        generarGrafico(tipoEstadistica, datos);
        generarTablaDetalle(tipoEstadistica, datos.detalle || []);
        
        // Actualizar resumen
        const totalRegistros = datos.detalle ? datos.detalle.length : 0;
        actualizarResumen(fechaInicio, fechaFin, totalRegistros);

    } catch (error) {
        console.error('Error al generar estadística:', error);
        alert('Error al generar la estadística: ' + error.message);
        document.getElementById('estadoReporte').textContent = 'Error';
        document.getElementById('estadoReporte').className = 'badge bg-danger';
    }
}

// Las demás funciones (obtenerDatosEstadisticos, obtenerTituloEstadistica, 
// generarExplicacion, generarGrafico, generarTablaDetalle) se mantienen igual que en tu código original

async function obtenerDatosEstadisticos(tipo, fechaInicio, fechaFin, departamento = null) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'Tareas',
            method: 'POST',
            dataType: 'json',
            data: {
                tipo: tipo,
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin,
                departamento: departamento
            },
            success: function (response) {
                if (response && response.success) {
                    resolve(response.data);
                } else {
                    reject(new Error(response.message || 'Datos inválidos desde el servidor'));
                }
            },
            error: function (xhr, status, error) {
                reject(new Error('Error de conexión: ' + error));
            }
        });
    });
}

function obtenerTituloEstadistica(tipo) {
    const titulos = {
        'recurso_consumible': 'Recurso Consumible Más Utilizado',
        'mes_mas_tareas': 'Mes con Más Tareas Realizadas'
    };
    return titulos[tipo] || 'Estadística Seleccionada';
}

function generarExplicacion(tipo, datos) {
    switch (tipo) {
        case 'recurso_consumible':
            return `<strong>Resultado:</strong> El recurso consumible más utilizado en el período seleccionado 
                    fue <strong>${datos.recurso}</strong> con un total de <strong>${datos.cantidad} ${datos.unidades}</strong> 
                    utilizadas en las tareas.`;
        case 'mes_mas_tareas':
            return `<strong>Resultado:</strong> El mes con mayor cantidad de tareas realizadas fue 
                    <strong>${datos.mes}</strong> con un total de <strong>${datos.cantidad} tareas</strong>. 
                    La distribución por departamentos se muestra en el gráfico.`;
        default:
            return 'Explicación no disponible para esta estadística.';
    }
}

function generarGrafico(tipo, datos) {
    const ctx = document.getElementById('graficoEstadistica').getContext('2d');

    // Destruir el gráfico anterior si existe
    if (window.estadisticaChart) {
        window.estadisticaChart.destroy();
    }

    let config = {};

    switch (tipo) {
        case 'recurso_consumible':
            config = {
                type: 'bar',
                data: {
                    labels: ['Recurso más usado'],
                    datasets: [{
                        label: datos.recurso,
                        data: [datos.cantidad],
                        backgroundColor: '#4CAF50',
                        borderColor: '#388E3C',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Recurso Más Utilizado'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad'
                            }
                        }
                    }
                }
            };
            break;
        case 'mes_mas_tareas':
            config = {
                type: 'pie',
                data: {
                    labels: datos.detalle.map(item => item.departamento),
                    datasets: [{
                        data: datos.detalle.map(item => item.cantidad),
                        backgroundColor: ['#2196F3', '#4CAF50', '#FFC107', '#9C27B0', '#F44336', '#607D8B'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: 'Distribución por Departamento'
                        }
                    }
                }
            };
            break;
    }

    window.estadisticaChart = new Chart(ctx, config);
}

function generarTablaDetalle(tipo, detalle) {
    const tabla = document.getElementById('tablaDetalleEstadisticas');
    const thead = tabla.querySelector('thead');
    const tbody = tabla.querySelector('tbody');

    // Limpiar la tabla
    thead.innerHTML = '';
    tbody.innerHTML = '';

    // Crear encabezados según el tipo de estadística
    let headers = [];
    switch (tipo) {
        case 'recurso_consumible':
            headers = ['Tarea', 'Cantidad Usada', 'Fecha'];
            break;
        case 'mes_mas_tareas':
            headers = ['Departamento', 'Cantidad de Tareas'];
            break;
    }

    // Crear fila de encabezado
    const headerRow = document.createElement('tr');
    headers.forEach(header => {
        const th = document.createElement('th');
        th.textContent = header;
        th.className = 'text-center';
        headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);

    // Llenar con datos
    if (detalle.length === 0) {
        const row = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = headers.length;
        td.textContent = 'No hay datos disponibles';
        td.className = 'text-center text-muted py-4';
        row.appendChild(td);
        tbody.appendChild(row);
    } else {
        detalle.forEach(item => {
            const row = document.createElement('tr');

            switch (tipo) {
                case 'recurso_consumible':
                    row.innerHTML = `
                        <td>${item.tarea || 'N/A'}</td>
                        <td class="text-center">${item.cantidad || '0'}</td>
                        <td class="text-center">${item.fecha || 'N/A'}</td>
                    `;
                    break;
                case 'mes_mas_tareas':
                    row.innerHTML = `
                        <td>${item.departamento || 'N/A'}</td>
                        <td class="text-center">${item.cantidad || '0'}</td>
                    `;
                    break;
            }

            tbody.appendChild(row);
        });
    }
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Establecer fechas por defecto (último mes)
    const today = new Date();
    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
    
    document.getElementById('fechaFin').value = today.toISOString().split('T')[0];
    document.getElementById('fechaInicio').value = lastMonth.toISOString().split('T')[0];
});
</script>