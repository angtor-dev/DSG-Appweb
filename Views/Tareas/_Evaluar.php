<style>
    .card-header .badge {
        font-size: 0.8rem;
    }

    #tabla-materialesDevueltos input {
        max-width: 80px;
    }

    #tabla-materialesDevueltos select {
        max-width: 120px;
    }
</style>


<!-- Modal para Evaluar Tarea -->
<div class="modal-body">


    <!-- Encabezado del Modal -->
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Evaluación de Tarea</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <!-- Cuerpo del Modal -->
    <div class="modal-body">
        <!-- Progreso de Evaluación -->
        <form id="form-evaluacion">
            

            <!-- Materiales utilizados -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Materiales Utilizados</h6>
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
                                <tr data-id="1">
                                    <td>Tornillos 3/8"</td>
                                    <td>50</td>
                                    <td><input type="number" class="form-control form-control-sm" value="45" min="0" max="50"></td>
                                    <td><input type="number" class="form-control form-control-sm" value="5" min="0" max="5"></td>

                                </tr>
                                <tr data-id="2">
                                    <td>Cable eléctrico 14 AWG</td>
                                    <td>10m</td>
                                    <td><input type="number" class="form-control form-control-sm" value="8.5" step="0.1" min="0" max="10"></td>
                                    <td><input type="number" class="form-control form-control-sm" value="1.5" step="0.1" min="0" max="1.5"></td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Evaluación del Supervisor -->
            <div class="card mb-4" id="seccion-supervisor">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Evaluación del Supervisor</h6>
                    <span class="badge bg-info">Pendiente</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label"><strong>Ponderación:</strong></label>
                        <select class="form-select" id="ponderacion-supervisor" name="ponderacion">
                            <option value="" disabled selected>Seleccione una ponderación</option>
                            <option value="buenobueno">Bueno-Bueno</option>
                            <option value="buenomedio">Bueno-Medio</option>
                            <option value="buenomalo">Bueno-Malo</option>
                            <option value="mediomedio">Medio-Medio</option>
                            <option value="mediomalo">Medio-Malo</option>
                            <option value="malomalo">Malo-Malo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="comentarios-supervisor" class="form-label"><strong>Comentarios:</strong></label>
                        <textarea class="form-control" id="comentarios-supervisor" name="comentarios" rows="3"
                            placeholder="Describa su evaluación de la tarea"></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="aprobacion-supervisor" name="aprobacion">
                        <label class="form-check-label" for="aprobacion-supervisor">
                            Aprobar finalización de tarea
                        </label>
                    </div>
                </div>
            </div>

            <div class="card mb-4" id="seccion-director">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Evaluación del Director</h6>
                    <span class="badge bg-secondary">No disponible</span>
                </div>
                <div class="card-body" id="contenido-director" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label"><strong>Ponderación:</strong></label>
                        <select class="form-select" id="ponderacion-director" name="ponderacion_director">
                            <option value="" disabled selected>Seleccione una ponderación</option>
                            <option value="buenobueno">Bueno-Bueno</option>
                            <option value="buenomedio">Bueno-Medio</option>
                            <option value="buenomalo">Bueno-Malo</option>
                            <option value="mediomedio">Medio-Medio</option>
                            <option value="mediomalo">Medio-Malo</option>
                            <option value="malomalo">Malo-Malo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="comentarios-director" class="form-label"><strong>Comentarios:</strong></label>
                        <textarea class="form-control" id="comentarios-director" name="comentarios_director" rows="3"
                            placeholder="Describa su evaluación de la tarea"></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="aprobacion-director" name="aprobacion_director">
                        <label class="form-check-label" for="aprobacion-director">
                            Aprobar finalización definitiva de tarea
                        </label>
                    </div>
                </div>
                <div class="card-body" id="mensaje-director">
                    <div class="alert alert-info">
                        Esta sección se habilitará después de la aprobación del supervisor.
                    </div>
                </div>
            </div>
            <!-- Confirmación -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Confirmación de Evaluación</h6>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirmacion-evaluacion" name="confirmacion">
                        <label class="form-check-label" for="confirmacion-evaluacion">
                            Confirmo que la información proporcionada es correcta y completa
                        </label>
                    </div>
                </div>
            </div>
    </div>

    <!-- Pie del Modal -->
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btn-guardar-evaluacion">
            <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Evaluación
        </button>
    </div>
    </form>

</div>