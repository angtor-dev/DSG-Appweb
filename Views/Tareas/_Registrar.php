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
                            <a class="nav-link" data-bs-toggle="pill" href="#area-turno">
                                <i class="fa-solid fa-building me-2"></i> Área/Turno
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="pill" href="#personal">
                                <i class="fa-solid fa-users me-2"></i> Personal
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
                            <h4 class="mb-4"><i class="fa-solid fa-user me-2"></i>Datos Básicos</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombre-tarea" class="form-label">Nombre de la Tarea</label>
                                    <input type="text" class="form-control" id="nombre-tarea" name="nombre" required>
                                    <div class="invalid-feedback">Por favor ingrese el nombre de la tarea</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="departamento" class="form-label">Departamento/Área</label>
                                    <div class="input-group">
                                        <select class="form-select" id="departamento" name="idDepartamento" required>
                                            <option value="" selected disabled>Seleccione un departamento</option>
                                            <option value="1">Plomería</option>
                                            <option value="2">Electricidad</option>
                                            <option value="3">Mecánica</option>
                                            <option value="4">Jardinería</option>
                                            <option value="5">Limpieza</option>
                                            <option value="6">Mantenimiento General</option>
                                        </select>
                                       
                                    </div>
                                    <div class="invalid-feedback">Seleccione un departamento</div>
                                </div>

                                <div class="col-md-12">
                                    <label for="descripcion" class="form-label">Descripción detallada</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required
                                        placeholder="Describa la tarea a realizar con todos los detalles necesarios"></textarea>
                                    <div class="invalid-feedback">Por favor ingrese una descripción</div>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary siguiente" data-next="area-turno">
                                        Siguiente <i class="fa-solid fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 2: Área/Turno -->
                        <div class="tab-pane fade" id="area-turno">
                            <h4 class="mb-4"><i class="fa-solid fa-building me-2"></i>Área y Turno</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="area" class="form-label">Área</label>
                                    <select class="form-select" id="area" name="idArea" required>
                                        <option value="" selected disabled>Seleccione un área</option>
                                        <option value="1">Producción</option>
                                        <option value="2">Mantenimiento</option>
                                        <option value="3">Almacén</option>
                                    </select>
                                    <div class="invalid-feedback">Seleccione un área</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="turno" class="form-label">Turno</label>
                                    <select class="form-select" id="turno" name="turno" required>
                                        <option value="" selected disabled>Seleccione un turno</option>
                                        <option value="matutino">Matutino</option>
                                        <option value="vespertino">Vespertino</option>
                                        <option value="nocturno">Nocturno</option>
                                    </select>
                                    <div class="invalid-feedback">Seleccione un turno</div>
                                </div>
                                <div class="col-12 d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary anterior" data-prev="datos-basicos">
                                        <i class="fa-solid fa-arrow-left me-2"></i> Anterior
                                    </button>
                                    <button type="button" class="btn btn-primary siguiente" data-next="personal">
                                        Siguiente <i class="fa-solid fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 3: Personal -->
                        <div class="tab-pane fade" id="personal">
                            <h4 class="mb-4"><i class="fa-solid fa-users me-2"></i>Personal Asignado</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="tipo-tarea" class="form-label">Tipo de Tarea</label>
                                    <select class="form-select" id="tipo-tarea" name="tipoTarea" required>
                                        <option value="normal">Normal</option>
                                        <option value="comun">Común (Plantilla)</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label for="personal" class="form-label">Seleccione personal</label>
                                  <select class="form-select select2-multiple" id="personal" name="personal[]" multiple="multiple" 
                                        data-placeholder="Seleccione personal">
                                    <option value="1">Juan Pérez</option>
                                    <option value="2">María García</option>
                                    <option value="3">Carlos López</option>
                                    <option value="4">Ana Martínez</option>
                                </select>
                                    <div class="invalid-feedback">Seleccione al menos un trabajador</div>
                                </div>


                                <div class="col-12 d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary anterior" data-prev="area-turno">
                                        <i class="fa-solid fa-arrow-left me-2"></i> Anterior
                                    </button>
                                    <button type="button" class="btn btn-primary siguiente" data-next="detalles">
                                        Siguiente <i class="fa-solid fa-arrow-right ms-2"></i>
                                    </button>
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
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="tabla-materiales">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50px">#</th>
                                                    <th>Material</th>
                                                    <th>Categoría</th>
                                                    <th>Unidad</th>
                                                    <th>Disponible</th>
                                                    <th width="150px">Cantidad</th>
                                                    <th width="80px">Acción</th>
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
                                <button type="button" class="btn btn-secondary anterior" data-prev="personal">
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

       