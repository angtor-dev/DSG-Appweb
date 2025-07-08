<div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Ordenes de trabajo</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

<div class="modal-body">
    <!-- Filtros de búsqueda -->
   

    <!-- Listado de tareas -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Tareas Disponibles</h6>
            <div>
                <button class="btn btn-sm btn-outline-primary me-2" id="btn-seleccionar-todas">
                    <i class="fas fa-check-square me-1"></i> Seleccionar todas
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="btn-limpiar-seleccion">
                    <i class="fas fa-times-circle me-1"></i> Limpiar
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm" id="tabla-tareas-agrupar">
                    <thead>
                        <tr>
                            <th >
                                <input type="checkbox" class="form-check-input" id="seleccionar-todo">
                            </th>
                            <th style="display: none;">ID</th>
                            <th >Personal</th>
                            <th >Departamento/Área</th>
                            <th >Descripción</th>
                            <th >Fecha</th>
                            <th >Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Filas se llenarán dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Vista previa de agrupación -->
    <div class="card mt-4 d-none" id="card-preview">
        <div class="card-header bg-light">
            <h6 class="mb-0">Vista Previa de Orden Agrupada</h6>
        </div>
        <div class="card-body" id="preview-agrupacion">
            <!-- Contenido generado dinámicamente -->
        </div>
    </div>
</div>

<!-- Pie del Modal -->
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
 
    <button type="button" class="btn btn-success d-none" id="btn-imprimir-agrupadas">
        <i class="fas fa-print me-2"></i>Vista previa e imprimir
    </button>
   
</div>
