<!-- Modal para Ver Detalles de Tarea -->
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <!-- Encabezado del Modal -->
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Detalles de la Tarea</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <!-- Cuerpo del Modal -->
        <div class="modal-body">
            <!-- Información general de la tarea -->
            <!-- Información general de la tarea -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Información Completa</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p><strong>ID Tarea:</strong> <span id="detalle-id"></span></p>
                            <p><strong>Área:</strong> <span id="detalle-area"></span></p>
                            <p><strong>Departamento:</strong> <span id="detalle-departamento"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha de Creación:</strong> <span id="detalle-fecha"></span></p>
                            <p><strong>Estado:</strong> <span id="detalle-estado" class="badge"></span>Evaluada</p>
                        </div>
                        <div class="col-12">
                            <p><strong>Descripción:</strong> <span id="detalle-descripcion"></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Asignado (se mantiene igual) -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Personal Asignado</h6>
                </div>
                <div class="card-body">
                    <ul id="detalle-personal" class="list-group list-group-flush"></ul>
                </div>
            </div>

            <!-- Materiales -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Materiales Utilizados</h6>
                </div>
                <div class="card-body">
                    <ul id="detalle-materiales" class="list-group list-group-flush"></ul>
                </div>
            </div>

            <!-- Evaluaciones -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Evaluaciones</h6>
                </div>
                <div class="card-body" id="detalle-comentarios"></div>
            </div>

            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>