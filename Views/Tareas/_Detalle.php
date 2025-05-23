<!-- Modal para Ver Detalles de Tarea -->
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <!-- Encabezado del Modal -->
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Detalles de la Tarea #<?= $tarea->id ?></h5>
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
                            <p><strong>ID Tarea:</strong> <?= $tarea->id ?></p>
                            <p><strong>Área:</strong> <?= htmlspecialchars($tarea->area->getNombre()) ?></p>
                            <p><strong>Departamento:</strong> <?= htmlspecialchars($tarea->departamento->getNombre()) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha de Creación:</strong> <?= date('d/m/Y H:i', strtotime($tarea->fechaCreacion)) ?></p>
                            <p><strong>Turno:</strong> Matutino</p>
                            <p><strong>Estado:</strong> 
                                <span class="badge bg-success">
                                    Activa
                                </span>
                            </p>
                        </div>
                        <div class="col-12">
                            <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($tarea->descripcion)) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Asignado -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Personal Asignado</h6>
                </div>
                <div class="card-body">
                    <ul id="detalle-personal" class="list-group list-group-flush">
                        <?php if (!empty($tarea->personal)): ?>
                            <?php foreach ($tarea->personal as $persona): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($persona->nombre) ?>
                                    <span class="badge bg-info rounded-pill"><?= htmlspecialchars($persona->nombre) ?></span>
                                </li>
                            <?php endforeach ?>
                        <?php else: ?>
                            <li class="list-group-item">No hay personal asignado</li>
                        <?php endif ?>
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