<style>
    .card-header .badge {
        font-size: 0.8rem;
    }

    #tabla-materialesCancelacion input {
        max-width: 80px;
    }

    #tabla-materialesCancelacion select {
        max-width: 120px;
    }
</style>

<!-- Modal para Cancelar Tarea -->
<div class="modal-body">

    <!-- Encabezado del Modal -->
    <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Cancelación de Tarea</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <!-- Cuerpo del Modal -->
    <div class="modal-body">
        <form id="form-cancelacion">
            <input type="hidden" id="id-tarea" name="id_tarea" value="" style="display: none;">

            <!-- Información de la Tarea -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Información de la Tarea a Cancelar</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID Tarea:</strong> <span id="cancelacion-id"></span></p>
                            <p><strong>Departamento:</strong> <span id="cancelacion-departamento"></span></p>
                            <p><strong>Área:</strong> <span id="cancelacion-area"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha Inicio:</strong> <span id="cancelacion-fecha"></span></p>
                            <p><strong>Estado:</strong> <span class="badge bg-warning">En Progreso</span></p>
                        </div>
                        <div class="col-12">
                            <p><strong>Descripción:</strong> <span id="cancelacion-descripcion"></span></p>
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
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cargo</th>
                                    <th>Turno</th>
                                </tr>
                            </thead>
                            <tbody id="cancelacion-personal">
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

           <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Materiales Asignados</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm" id="tabla-materialesDevueltos">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th width="100px">Asignado</th>
                                            <th width="100px">Utilizado</th>
                                            <th width="100px">Devuelto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Se llenará dinámicamente por tu función JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

            <!-- Motivo de Cancelación -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Motivo de Cancelación</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="observaciones-cancelacion" class="form-label"><strong>Observaciones:</strong></label>
                        <textarea class="form-control" id="observaciones-cancelacion" name="comentarios" rows="4"
                            placeholder="Describa el motivo de la cancelación de la tarea..." required></textarea>
                        <div class="form-text">Este campo es obligatorio para proceder con la cancelación.</div>
                    </div>
                </div>
            </div>

            <!-- Confirmación -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Confirmación de Cancelación</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer. La tarea será marcada como cancelada y los materiales serán devueltos al inventario según las cantidades especificadas.
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmacion-cancelacion" name="confirmacion" required>
                        <label class="form-check-label" for="confirmacion-cancelacion">
                            Confirmo que deseo cancelar esta tarea y que la información proporcionada es correcta
                        </label>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Pie del Modal -->
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-danger" id="btn-confirmar-cancelacion" onclick="event.preventDefault(); enviarFormularioCancelacion(event)">
            <i class="fa-solid fa-ban me-2"></i>Confirmar Cancelación
        </button>
    </div>
</div>

