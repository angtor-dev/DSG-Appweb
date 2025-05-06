<!-- Modal para Evaluar Tarea -->

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Evaluación de Tarea</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
            

                <!-- Información general de la tarea -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Detalles de la Tarea</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nombre:</strong> <span id="tarea-nombre">Cargando...</span></p>
                                <p><strong>Descripción:</strong> <span id="tarea-descripcion">Cargando...</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Departamento:</strong> <span id="tarea-departamento">Cargando...</span></p>
                                <p><strong>Fecha asignación:</strong> <span id="tarea-fecha">Cargando...</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Estado de finalización -->
                <div class="mb-4">
                    <label class="form-label"><strong>Estado de la tarea:</strong></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="estadoTarea" id="tarea-completada" value="completada">
                        <label class="form-check-label" for="tarea-completada">
                            Completada satisfactoriamente
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="estadoTarea" id="tarea-incompleta" value="incompleta">
                        <label class="form-check-label" for="tarea-incompleta">
                            Incompleta o con problemas
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="estadoTarea" id="tarea-pendiente" value="pendiente">
                        <label class="form-check-label" for="tarea-pendiente">
                            Pendiente de finalizar
                        </label>
                    </div>
                </div>
                
                <!-- Evaluación por persona -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Evaluación del Personal</h6>
                    </div>
                    <div class="card-body">
                        <div id="evaluaciones-personal">
                            <!-- Ejemplo de una evaluación individual -->
                            <div class="evaluacion-persona mb-4 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Juan Pérez</h6>
                                    <span class="badge bg-info">Plomería</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Ponderación (1-5):</label>
                                    <div class="rating">
                                        <input type="radio" id="star5-juan" name="rating-juan" value="5" />
                                        <label for="star5-juan" title="Excelente">5</label>
                                        <input type="radio" id="star4-juan" name="rating-juan" value="4" />
                                        <label for="star4-juan" title="Muy bueno">4</label>
                                        <input type="radio" id="star3-juan" name="rating-juan" value="3" checked />
                                        <label for="star3-juan" title="Aceptable">3</label>
                                        <input type="radio" id="star2-juan" name="rating-juan" value="2" />
                                        <label for="star2-juan" title="Regular">2</label>
                                        <input type="radio" id="star1-juan" name="rating-juan" value="1" />
                                        <label for="star1-juan" title="Deficiente">1</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="comentarios-juan" class="form-label">Comentarios:</label>
                                    <textarea class="form-control" id="comentarios-juan" rows="2" placeholder="Describa el desempeño del trabajador"></textarea>
                                </div>
                            </div>
                            
                            <!-- Ejemplo de segunda persona -->
                            <div class="evaluacion-persona mb-4 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">María García</h6>
                                    <span class="badge bg-warning text-dark">Electricidad</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Ponderación (1-5):</label>
                                    <div class="rating">
                                        <input type="radio" id="star5-maria" name="rating-maria" value="5" />
                                        <label for="star5-maria" title="Excelente">5</label>
                                        <input type="radio" id="star4-maria" name="rating-maria" value="4" checked />
                                        <label for="star4-maria" title="Muy bueno">4</label>
                                        <input type="radio" id="star3-maria" name="rating-maria" value="3" />
                                        <label for="star3-maria" title="Aceptable">3</label>
                                        <input type="radio" id="star2-maria" name="rating-maria" value="2" />
                                        <label for="star2-maria" title="Regular">2</label>
                                        <input type="radio" id="star1-maria" name="rating-maria" value="1" />
                                        <label for="star1-maria" title="Deficiente">1</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="comentarios-maria" class="form-label">Comentarios:</label>
                                    <textarea class="form-control" id="comentarios-maria" rows="2" placeholder="Describa el desempeño del trabajador"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Comentarios generales -->
                <div class="mt-4">
                    <label for="comentarios-generales" class="form-label"><strong>Comentarios generales sobre la tarea:</strong></label>
                    <textarea class="form-control" id="comentarios-generales" rows="3" placeholder="Observaciones generales sobre el desarrollo de la tarea"></textarea>
                </div>
            </div>
            
            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-evaluacion">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Evaluación
                </button>
            </div>
        </div>
    </div>


<!-- Estilos para el sistema de rating -->
<style>
.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.rating input {
    display: none;
}
.rating label {
    color: #ddd;
    font-size: 1.5rem;
    padding: 0 0.2rem;
    cursor: pointer;
}
.rating input:checked ~ label {
    color: #ffc107;
}
.rating label:hover,
.rating label:hover ~ label {
    color: #ffc107;
}
</style>

