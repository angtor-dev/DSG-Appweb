<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Asignar nueva tarea
            </h5>
        </div>
        <div class="modal-body">
            <div class="row">
                <!-- Navegación vertical -->
                <div class="col-md-3">
                    <ul class="nav flex-column nav-pills" id="tabs-tarea">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="pill" href="#datos-basicos">
                                <i class="fa-solid fa-user me-2"></i> Datos Básicos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="pill" href="#detalles">
                                <i class="fa-solid fa-list-check me-2"></i> Detalles
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contenido del formulario -->
                <div class="col-md-9">
                    <form method="post" id="form-tarea" class="tab-content">
                        <!-- Paso 1: Datos Básicos -->
                        <div class="tab-pane fade show active" id="datos-basicos">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fa-solid fa-user me-2"></i>Datos Básicos</h4>
        </div>
        <div class="card-body">
            <!-- Sección 1: Información general -->
            <div class="mb-4">
                <h5 class="text-primary mb-3"><i class="fa-solid fa-info-circle me-2"></i>Información General</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nombre-tarea" class="form-label fw-semibold">Nombre de la Tarea</label>
                        <input type="text" class="form-control" id="nombre-tarea" name="nombre" required placeholder="Ej: Reparación de tubería principal">
                        <div class="invalid-feedback">Por favor ingrese el nombre de la tarea</div>
                    </div>

                    <div class="col-md-6">
                        <label for="departamento" class="form-label fw-semibold">Departamento/Área</label>
                        <select class="form-select " id="departamento" name="idDepartamento" required>
                            <option value="" selected disabled>Seleccione un departamento</option>
                          <?php foreach ($departamentos as $departamento): ?>
                                <option value="<?= $departamento->id ?>">
                                    <?= $departamento->getNombre() ?>
                                    <?php if ($departamento->departamentoPadre !== null): ?>
                                        (<?= $departamento->departamentoPadre->getNombre() ?? '' ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Seleccione un departamento</div>
                    </div>

                    <div class="col-12">
                        <label for="descripcion" class="form-label fw-semibold">Descripción detallada</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required
                            placeholder="Describa la tarea a realizar con todos los detalles necesarios..."></textarea>
                        <small class="text-muted">Máximo 500 caracteres</small>
                        <div class="invalid-feedback">Por favor ingrese una descripción</div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Sección 2: Ubicación y horario -->
            <div class="mb-4">
                <h5 class="text-primary mb-3"><i class="fa-solid fa-location-dot me-2"></i>Ubicación y Horario</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="area" class="form-label fw-semibold">Área específica</label>
                        <select class="form-select" id="area" name="idArea" required>
                            <option value="" selected disabled>Seleccione un área</option>
                            <?php foreach ($areas as $area): ?>
                                <option value="<?= $area->id ?>">
                                    <?= $area->getNombre() ?>
                                    <?php if ($area->areaPadre !== null): ?>
                                        (<?= $area->areaPadre->getNombre() ?? '' ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                           
                        </select>
                        <div class="invalid-feedback">Seleccione un área</div>
                    </div>
                    <div class="col-md-6">
                        <label for="turno" class="form-label fw-semibold">Turno</label>
                        <select class="form-select" id="turno" name="turno" required>
                            <option value="" selected disabled>Seleccione un turno</option>
                            <option value="matutino">Matutino (6:00 - 14:00)</option>
                            <option value="vespertino">Vespertino (14:00 - 22:00)</option>
                            <option value="nocturno">Nocturno (22:00 - 6:00)</option>
                        </select>
                        <div class="invalid-feedback">Seleccione un turno</div>
                    </div>
                </div>
            </div>

<!-- Script para inicializar select2 -->


            <hr class="my-4">

            <!-- Sección 3: Asignación -->
            <div class="mb-4">
                <h5 class="text-primary mb-3"><i class="fa-solid fa-users me-2"></i>Asignación</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="tipo-tarea" class="form-label fw-semibold">Tipo de Tarea</label>
                        <select class="form-select" id="tipo-tarea" name="tipoTarea" required>
                            <option value="normal">Normal</option>
                            <option value="comun">Común (Plantilla)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha-inicio" class="form-label fw-semibold">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        <div class="invalid-feedback">Seleccione una fecha</div>
                    </div>
                    <div class="col-12">
                        <label for="personal" class="form-label fw-semibold">Personal asignado</label>
                        <select class="form-select select2-multiple" id="personal" name="personal[]" multiple="multiple" 
                            data-placeholder="Busque y seleccione personal">
                              <?php foreach ($trabajadores as $trabajador): ?>
                                <option value="<?= $trabajador->id ?>">
                                    <?= $trabajador->getNombreCompleto() ?> - <?= $trabajador->getCedula() ?>
                                    (<?= $trabajador->departamento->getNombre() ?? 'Sin departamento' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Puede seleccionar múltiples personas</small>
                        <div class="invalid-feedback">Seleccione al menos un trabajador</div>
                    </div>
                </div>
            </div>

            <!-- Botón de siguiente -->
            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-primary px-4 siguiente" data-next="detalles">
                    Siguiente <i class="fa-solid fa-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>
                         <!-- Paso 4: Detalles -->
                        <div class="tab-pane fade" id="detalles">
                            <h4 class="mb-4"><i class="fa-solid fa-list-check me-2"></i>Detalles y Materiales</h4>
                            
                            <!-- Filtros de búsqueda -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label for="buscar-material" class="form-label">Buscar Material</label>
                                            <input type="text" class="form-control" id="buscar-material" 
                                                   placeholder="Nombre, código o categoría">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="categoria" class="form-label">Categoría</label>
                                            <select class="form-select" id="categoria">
                                                <option value="">Todas las categorías</option>
                                                <option value="herramientas">Herramientas</option>
                                                <option value="tornilleria">Tornillería</option>
                                                <option value="electricos">Materiales Eléctricos</option>
                                                <option value="plomeria">Plomería</option>
                                                <option value="pintura">Pintura</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de materiales disponibles con DataTables -->
                            <div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fa-solid fa-boxes-stacked me-2"></i> Materiales Disponibles</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-hover mb-0" id="tabla-materiales" style="width: 100%;">
                <thead class="table-light position-sticky top-0">
                    <tr>
                        <th width="50px">#</th>
                        <th width="280px">Material</th>
                        <th>Categoría</th>
                        <th>Unidad</th>
                        <th>Disponible</th>
                        <th width="80px">Cantidad</th>
                        <th width="50px">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán dinámicamente via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

                            <!-- Materiales seleccionados -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fa-solid fa-cart-shopping me-2"></i> Materiales Seleccionados</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0" id="tabla-seleccionados">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Material</th>
                                                    <th>Cantidad</th>
                                                    <th>Unidad</th>
                                                    <th width="80px">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Aquí se agregarán dinámicamente los materiales seleccionados -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary anterior" data-prev="datos-basicos">
                                    <i class="fa-solid fa-arrow-left me-2"></i> Anterior
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check me-2"></i> Guardar Tarea
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

       