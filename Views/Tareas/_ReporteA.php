<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Reporte Estadístico Personalizado - Dirección de Servicios Generales</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="container-fluid">
                <!-- Filtros de búsqueda -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6>Filtros de Estadísticas</h6>
                            </div>
                            <div class="card-body">
                                <form id="formEstadisticas">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="tipoEstadistica" class="form-label">Tipo de Estadística</label>
                                                <select class="form-select" id="tipoEstadistica" required>
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="recurso_consumible">Recurso consumible más utilizado</option>
                                                    <option value="mes_mas_tareas">Mes con más tareas realizadas</option>
                                                  
                                                       </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="fechaInicio" class="form-label">Fecha Inicio</label>
                                                <input type="date" class="form-control" id="fechaInicio" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="fechaFin" class="form-label">Fecha Fin</label>
                                                <input type="date" class="form-control" id="fechaFin" required>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="text-center">
                                        <button type="button" class="btn btn-primary" onclick="generarEstadistica()">
                                            <i class="fas fa-chart-bar me-2"></i>Generar Estadística
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row contenidoMostrar" >

                    <!-- Gráfico resultante -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 id="tituloGrafico">Estadística Seleccionada</h6>
                                </div>
                                <div class="card-body">
                                    <div id="explicacionEstadistica" class="alert alert-info mb-4">
                                        <!-- Aquí irá una explicación textual de los resultados -->
                                    </div>
                                    <canvas id="graficoEstadistica" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de datos detallados -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-warning">
                                    <h6>Detalle de Datos</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="tablaDetalleEstadisticas">
                                            <thead>
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
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="generarPDF()">Exportar a PDF</button>
        </div>
    </div>
</div>