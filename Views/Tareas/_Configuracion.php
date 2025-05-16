<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Configuración de Tarea Automática
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-config-tarea" action="<?= LOCAL_DIR ?>/Tareas/GuardarConfiguracion">
                <input type="hidden" name="id_tarea" value="<?= $_GET['id'] ?? '' ?>">
                
                <div class="row g-3">
                    <!-- Estado de la tarea automática -->
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="tarea-activa" name="activa" checked>
                            <label class="form-check-label" for="tarea-activa">Tarea automática activa</label>
                        </div>
                    </div>

                    <!-- Periodicidad -->
                    <div class="col-md-6">
                        <label for="periodicidad" class="form-label">Periodicidad</label>
                        <select class="form-select" id="periodicidad" name="periodicidad" required>
                            <option value="" selected disabled>Seleccione periodicidad</option>
                            <option value="diario">Diario</option>
                            <option value="semanal">Semanal</option>
                            <option value="quincenal">Quincenal</option>
                            <option value="mensual">Mensual</option>
                            <option value="trimestral">Trimestral</option>
                            <option value="anual">Anual</option>
                        </select>
                        <div class="invalid-feedback">Seleccione una periodicidad</div>
                    </div>

                    <!-- Días específicos (solo para semanal) -->
                    <div class="col-md-6" id="dias-semana-container" style="display: none;">
                        <label class="form-label">Días de la semana</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                            $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                            foreach ($dias as $index => $dia): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="dia-<?= strtolower($dia) ?>" 
                                           name="dias_semana[]" 
                                           value="<?= strtolower($dia) ?>">
                                    <label class="form-check-label" for="dia-<?= strtolower($dia) ?>"><?= $dia ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Día del mes (solo para mensual, trimestral, anual) -->
                    <div class="col-md-6" id="dia-mes-container" style="display: none;">
                        <label for="dia-mes" class="form-label">Día del mes</label>
                        <select class="form-select" id="dia-mes" name="dia_mes">
                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                <option value="<?= $i ?>" <?= $i == date('j') ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Hora de ejecución -->
                    <div class="col-md-6">
                        <label for="hora-ejecucion" class="form-label">Hora de ejecución</label>
                        <input type="time" class="form-control" id="hora-ejecucion" name="hora_ejecucion" 
                               value="08:00" required>
                        <div class="invalid-feedback">Seleccione una hora</div>
                    </div>

                    <!-- Fecha de inicio -->
                    <div class="col-md-6">
                        <label for="fecha-inicio" class="form-label">Fecha de inicio</label>
                        <input type="date" class="form-control" id="fecha-inicio" name="fecha_inicio" 
                               value="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback">Seleccione una fecha</div>
                    </div>

                    <!-- Fecha de fin (opcional) -->
                    <div class="col-md-6">
                        <label for="fecha-fin" class="form-label">Fecha de fin (opcional)</label>
                        <input type="date" class="form-control" id="fecha-fin" name="fecha_fin">
                        <small class="text-muted">Dejar vacío si no tiene fecha de finalización</small>
                    </div>
                </div>

                <div class="modal-footer mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                </div>
            </form>
        </div>
    </div>
</div>

