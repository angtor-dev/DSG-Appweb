<div class="panel-header">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Reportes de Tareas</h3>
                <span class="opacity-75 mb-2">Genere reportes detallados de tareas y descárguelos en PDF</span>
            </div>
            <button class="btn btn-success mt-3 mt-md-0" id="btnGenerarReporte">
                <i class="fas fa-file-pdf mr-2"></i>Generar PDF
            </button>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="row">
        <!-- Panel de Configuración -->
        <div class="col-md-4">
            <div class="card border-0 box-shadow-alt">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Configurar Reporte</h4>
                </div>
                <div class="card-body">
                    <form id="formReporte">
                        <!-- Tipo de Reporte -->
                        <div class="mb-3">
                            <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
                            <select name="tipo_reporte" id="tipo_reporte" class="form-select" required>
                                <option value="">Seleccione un tipo</option>
                                <option value="trabajadores">Por Trabajador</option>
                                <option value="departamentos">Por División</option>
                                <option value="turnos">Por Turno</option>
                                <option value="evaluaciones">Evaluaciones de Calidad</option>
                                <option value="recursos">Uso de Recursos</option>
                                <option value="temporal">Evolución Temporal</option>
                                <option value="general">Resumen General</option>
                            </select>
                        </div>

                        <!-- Rango de Fechas -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="fechaInicio" class="form-label">Desde</label>
                                <input type="date" class="form-control" id="fechaInicio" name="fechaInicio">
                            </div>
                            <div class="col-6">
                                <label for="fechaFin" class="form-label">Hasta</label>
                                <input type="date" class="form-control" id="fechaFin" name="fechaFin">
                            </div>
                        </div>

                        <!-- Filtros Específicos -->
                        <div id="filtrosEspecificos">
                            <!-- Filtros para Reporte por Trabajador -->
                            <div class="filtro-grupo" data-tipo="trabajadores">
                                <div class="mb-3">
                                    <label for="filtroTrabajador" class="form-label">Trabajador Específico</label>
                                    <select name="filtroTrabajador" id="filtroTrabajador" class="form-select">
                                        <option value="">Todos los trabajadores</option>
                                        <?php foreach ($trabajadores as $trabajador): ?>
                                            <option value="<?= $trabajador->id ?>">
                                                <?= $trabajador->nombre . ' ' . $trabajador->apellido ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Filtros para Reporte por División -->
                            <div class="filtro-grupo" data-tipo="departamentos">
                                <div class="mb-3">
                                    <label for="filtroDivision" class="form-label">División Específica</label>
                                    <select name="filtroDivision" id="filtroDivision" class="form-select">
                                        <option value="">Todas las divisiones</option>
                                        <?php foreach ($departamentos as $departamento): ?>
                                            <option value="<?= $departamento->id ?>"><?= $departamento->getNombre() ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Filtros para Reporte por Turno -->
                            <div class="filtro-grupo" data-tipo="turnos">
                                <div class="mb-3">
                                    <label for="filtroTurno" class="form-label">Turno Específico</label>
                                    <select name="filtroTurno" id="filtroTurno" class="form-select">
                                        <option value="">Todos los turnos</option>
                                        <?= Turno::getTurnosOptions(); ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Filtros para Evaluaciones -->
                            <div class="filtro-grupo" data-tipo="evaluaciones">
                                <div class="mb-3">
                                    <label for="filtroEvaluacion" class="form-label">Rango de Evaluación</label>
                                    <select name="filtroEvaluacion" id="filtroEvaluacion" class="form-select">
                                        <option value="">Todas las evaluaciones</option>
                                        <option value="excelente">Excelente (buenobueno)</option>
                                        <option value="bueno">Bueno (buenomedio)</option>
                                        <option value="regular">Regular (mediomedio)</option>
                                        <option value="mejorable">Necesita Mejora (malomalo)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Filtros para Recursos -->
                            <div class="filtro-grupo" data-tipo="recursos">
                                <div class="mb-3">
                                    <label for="filtroCategoria" class="form-label">Categoría de Recursos</label>
                                    <select name="filtroCategoria" id="filtroCategoria" class="form-select">
                                        <option value="">Todas las categorías</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria->id ?>"><?= $categoria->nombre ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Estado de Tareas -->
                        <div class="mb-3">
                            <label class="form-label">Estado de Tareas</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="estados[]" value="activo" id="estadoActivo" checked>
                                <label class="form-check-label" for="estadoActivo">Activas</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="estados[]" value="evaluada" id="estadoEvaluada" checked>
                                <label class="form-check-label" for="estadoEvaluada">Evaluadas</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="estados[]" value="cancelado" id="estadoCancelado">
                                <label class="form-check-label" for="estadoCancelado">Canceladas</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="estados[]" value="vencida" id="estadoVencida">
                                <label class="form-check-label" for="estadoVencida">Vencidas</label>
                            </div>
                        </div>

                        <!-- Opciones de PDF -->
                        <div class="mb-3">
                            <label class="form-label">Opciones de PDF</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="incluirGraficos" id="incluirGraficos" checked>
                                <label class="form-check-label" for="incluirGraficos">Incluir Gráficos</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="incluirDetalles" id="incluirDetalles" checked>
                                <label class="form-check-label" for="incluirDetalles">Incluir Detalles</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="soloResumen" id="soloResumen">
                                <label class="form-check-label" for="soloResumen">Solo Resumen</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Vista Previa Rápida -->
            <div class="card border-0 box-shadow-alt mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Vista Previa Rápida</h5>
                </div>
                <div class="card-body">
                    <div id="vistaPrevia" class="text-center text-muted">
                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                        <p>Seleccione un tipo de reporte para ver la vista previa</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Área de Visualización -->
        <div class="col-md-8">
            <div class="card border-0 box-shadow-alt">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0" id="tituloReporte">Reporte de Tareas</h4>
                    <div class="btn-group">
                        <button class="btn btn-light btn-sm" id="btnVistaPrevia">
                            <i class="fas fa-eye mr-1"></i>Vista Previa
                        </button>
                        <button class="btn btn-success btn-sm" id="btnDescargarPDF">
                            <i class="fas fa-download mr-1"></i>Descargar PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="contenedorReporte" class="reporte-container">
                        <!-- Aquí se mostrará el reporte generado -->
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-file-alt fa-4x mb-3"></i>
                            <h4>No hay datos para mostrar</h4>
                            <p>Configure los filtros y genere un reporte</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métricas Rápidas -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card card-stats bg-gradient-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-tasks fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0" id="metricTotalTareas">0</h5>
                                    <small>Total Tareas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats bg-gradient-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0" id="metricCompletadas">0</h5>
                                    <small>Completadas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats bg-gradient-warning text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0" id="metricActivas">0</h5>
                                    <small>En Progreso</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stats bg-gradient-danger text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0" id="metricVencidas">0</h5>
                                    <small>Vencidas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Carga -->
<div class="modal fade" id="modalCarga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Generando reporte...</span>
                </div>
                <h5>Generando PDF</h5>
                <p class="mb-0">Por favor espere...</p>
            </div>
        </div>
    </div>
</div>

<style>
.reporte-container {
    min-height: 400px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
}

.card-stats {
    border: none;
    border-radius: 10px;
    transition: transform 0.2s;
}

.card-stats:hover {
    transform: translateY(-2px);
}

.filtro-grupo {
    display: none;
}

.filtro-grupo.activo {
    display: block;
}

.form-check {
    margin-bottom: 0.5rem;
}

.bg-gradient-primary { background: linear-gradient(45deg, #007bff, #0056b3); }
.bg-gradient-success { background: linear-gradient(45deg, #28a745, #1e7e34); }
.bg-gradient-warning { background: linear-gradient(45deg, #ffc107, #e0a800); }
.bg-gradient-danger { background: linear-gradient(45deg, #dc3545, #c82333); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoReporte = document.getElementById('tipo_reporte');
    const filtrosEspecificos = document.getElementById('filtrosEspecificos');
    const btnGenerarReporte = document.getElementById('btnGenerarReporte');
    const btnVistaPrevia = document.getElementById('btnVistaPrevia');
    const btnDescargarPDF = document.getElementById('btnDescargarPDF');
    const contenedorReporte = document.getElementById('contenedorReporte');
    const modalCarga = new bootstrap.Modal(document.getElementById('modalCarga'));

    // Mostrar/ocultar filtros específicos
    tipoReporte.addEventListener('change', function() {
        // Ocultar todos los filtros
        document.querySelectorAll('.filtro-grupo').forEach(filtro => {
            filtro.classList.remove('activo');
        });

        // Mostrar filtros del tipo seleccionado
        const filtroSeleccionado = document.querySelector(`.filtro-grupo[data-tipo="${this.value}"]`);
        if (filtroSeleccionado) {
            filtroSeleccionado.classList.add('activo');
        }

        // Actualizar título
        document.getElementById('tituloReporte').textContent = 
            `Reporte de ${this.options[this.selectedIndex].text}`;
    });

    // Generar vista previa
    btnVistaPrevia.addEventListener('click', function() {
        generarReporte(false);
    });

    // Descargar PDF
    btnDescargarPDF.addEventListener('click', function() {
        generarReporte(true);
    });

    function generarReporte(descargarPDF = false) {
        const formData = new FormData(document.getElementById('formReporte'));
        
        // Validaciones básicas
        if (!formData.get('tipo_reporte')) {
            alert('Por favor seleccione un tipo de reporte');
            return;
        }

        if (descargarPDF) {
            modalCarga.show();
        }

        // Simular generación de reporte (aquí iría tu llamada AJAX real)
        setTimeout(() => {
            if (descargarPDF) {
                modalCarga.hide();
                // Aquí iría la lógica real para descargar el PDF
                simularDescargaPDF();
            } else {
                mostrarVistaPrevia();
            }
        }, 2000);
    }

    function mostrarVistaPrevia() {
        const tipo = document.getElementById('tipo_reporte').value;
        let contenido = '';

        switch(tipo) {
            case 'trabajadores':
                contenido = generarVistaTrabajadores();
                break;
            case 'departamentos':
                contenido = generarVistaDepartamentos();
                break;
            case 'turnos':
                contenido = generarVistaTurnos();
                break;
            case 'evaluaciones':
                contenido = generarVistaEvaluaciones();
                break;
            case 'recursos':
                contenido = generarVistaRecursos();
                break;
            default:
                contenido = generarVistaGeneral();
        }

        contenedorReporte.innerHTML = contenido;
        actualizarMetricas();
    }

    function simularDescargaPDF() {
        // Aquí iría la lógica real para generar y descargar el PDF
        alert('PDF generado exitosamente. En una implementación real, se descargaría el archivo.');
        
        // Ejemplo de cómo podría ser:
        // window.open('generar_pdf.php?' + new URLSearchParams(formData), '_blank');
    }

    function actualizarMetricas() {
        // Simular actualización de métricas
        document.getElementById('metricTotalTareas').textContent = '156';
        document.getElementById('metricCompletadas').textContent = '89';
        document.getElementById('metricActivas').textContent = '45';
        document.getElementById('metricVencidas').textContent = '22';
    }

    // Funciones para generar vistas previas (simuladas)
    function generarVistaTrabajadores() {
        return `
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Trabajador</th>
                            <th>División</th>
                            <th>Total Tareas</th>
                            <th>Completadas</th>
                            <th>Activas</th>
                            <th>Eficiencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Juan Pérez</td>
                            <td>Jardinería</td>
                            <td>15</td>
                            <td>12</td>
                            <td>3</td>
                            <td>80%</td>
                        </tr>
                        <tr>
                            <td>María García</td>
                            <td>Herrería</td>
                            <td>22</td>
                            <td>18</td>
                            <td>4</td>
                            <td>82%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
    }

    function generarVistaDepartamentos() {
        return `
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Tareas por División</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartDepartamentos" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Eficiencia por Área</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Jardinería
                                    <span class="badge bg-success rounded-pill">85%</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Herrería
                                    <span class="badge bg-warning rounded-pill">78%</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Plomería
                                    <span class="badge bg-danger rounded-pill">65%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Inicializar
    tipoReporte.dispatchEvent(new Event('change'));
});
</script>