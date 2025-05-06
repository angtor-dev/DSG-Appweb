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
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Información Completa</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p><strong>ID Tarea:</strong> <span id="detalle-id">Cargando...</span></p>
                            <p><strong>Área:</strong> <span id="detalle-area">Cargando...</span></p>
                            <p><strong>Departamento:</strong> <span id="detalle-departamento">Cargando...</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha de Creación:</strong> <span id="detalle-fecha">Cargando...</span></p>
                            <p><strong>Turno:</strong> <span id="detalle-turno">Cargando...</span></p>
                            <p><strong>Estado:</strong> <span id="detalle-estado">Cargando...</span></p>
                        </div>
                        <div class="col-12">
                             <p><strong>Descripción:</strong> <span id="detalle-descripcion">Cargando...</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Asignado (Ejemplo) -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Personal Asignado</h6>
                </div>
                <div class="card-body">
                    <ul id="detalle-personal" class="list-group list-group-flush">
                        <!-- Los elementos de personal se cargarán aquí dinámicamente -->
                        <!-- Ejemplo de una persona asignada -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Juan Pérez
                            <span class="badge bg-info rounded-pill">Plomería</span>
                        </li>
                        <!-- Ejemplo de otra persona asignada -->
                         <li class="list-group-item d-flex justify-content-between align-items-center">
                            María García
                            <span class="badge bg-warning text-dark rounded-pill">Electricidad</span>
                        </li>
                         <!-- Puedes añadir más ejemplos aquí -->
                         <li class="list-group-item">Cargando personal...</li> <!-- Placeholder que se reemplazaría -->
                    </ul>
                </div>
            </div>

             <!-- Materiales Necesarios (Ejemplo) -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Materiales Necesarios</h6>
                </div>
                <div class="card-body">
                     <ul id="detalle-materiales" class="list-group list-group-flush">
                        <!-- Los elementos de materiales se cargarán aquí -->
                        <li class="list-group-item">Cargando materiales...</li>
                    </ul>
                </div>
            </div>

             <!-- Comentarios (Ejemplo) -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Comentarios</h6>
                </div>
                <div class="card-body">
                     <p id="detalle-comentarios">Cargando comentarios...</p>
                </div>
            </div>

        </div>

        <!-- Pie del Modal -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
    </div>
</div>

<script>


</script>
