<div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Ordenes de trabajo</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

<div class="modal-body">
    <!-- Filtros de búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Departamento</label>
                    <select class="form-select" id="filtro-departamento">
                        <option value="">Todos</option>
                        <!-- Opciones se llenarán dinámicamente -->
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select class="form-select" id="filtro-estado">
                        <option value="">Todos</option>
                        <option value="activa">Activa</option>
                        <option value="vencida">Vencida</option>
                        <option value="evaluada">Evaluada</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

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
                            <th >ID</th>
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
    <button type="button" class="btn btn-primary" id="btn-generar-preview"  disabled onclick="mostrarEjemplo()">
        <i class="fas fa-eye me-2"></i>Generar Vista Previa
    </button>
    <button type="button" class="btn btn-success d-none" id="btn-imprimir-agrupadas">
        <i class="fas fa-print me-2"></i>Imprimir Selección
    </button>
    <button class="btn btn-outline-light rounded-pill" 
        data-bs-toggle="modal" 
        data-bs-target="#modal-ordenes" 
        data-backdrop="static"
        id="btn-generar-ordenes">
    <i class="fas fa-object-group me-2"></i> Generar Órdenes
</button>
</div>
