<!-- Modal para el orden de trabajo -->
<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Resumen de la Orden de Trabajo</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <!-- Contenedor principal para impresión -->
    <div id="formato-orden-trabajo" class="p-4 border">
        <!-- Encabezado institucional -->
        <div class="text-center mb-4">
            <h3 class="mb-1">DIRECCIÓN DE SERVICIOS GENERALES</h3>
            <h4 class="mb-1">RESUMEN DE ASIGNACIÓN DE ORDEN DE TRABAJO</h4>
            <p class="mb-0">Fecha: <span id="orden-fecha"></span> | Hora: <span id="orden-hora"></span></p>
        </div>

        <!-- Datos generales compactos -->
        <div class="d-flex justify-content-center mb-4">
            <div class="col-md-3 mx-2">
                <div class="mb-3 text-center">
                    <label class="form-label"><strong>Departamento:</strong></label>
                    <div class="border-bottom pb-1" id="orden-departamento"></div>
                </div>
            </div>
            <div class="col-md-3 mx-2">
                <div class="mb-3 text-center">
                    <label class="form-label"><strong>Área:</strong></label>
                    <div class="border-bottom pb-1" id="orden-area"></div>
                </div>
            </div>
            <div class="col-md-2 mx-2">
                <div class="mb-3 text-center">
                    <label class="form-label"><strong>Fecha Inicio:</strong></label>
                    <div class="border-bottom pb-1" id="orden-inicio"></div>
                </div>
            </div>
        </div>

        <!-- Personal asignado -->
        <div class="mb-4">
            <label class="form-label"><strong>Personal Asignado:</strong></label>
            <div class="border p-2" id="orden-personal">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th width="10%">#</th>
                            <th width="45%">Nombre</th>
                            <th width="45%">Cargo</th>
                        </tr>
                    </thead>
                    <tbody id="personal-lista">
                        <!-- Las filas se agregarán dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tareas a realizar -->
        <div class="mb-4">
            <label class="form-label"><strong>Tareas a Realizar:</strong></label>
            <div class="border p-2">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="60%" style="word-break: break-word;">Descripción de la Tarea</th>
                            <th width="35%">Materiales</th>
                        </tr>
                    </thead>
                    <tbody id="tareas-lista">
                        <!-- Las filas se agregarán dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Observaciones y firmas -->
        <div class="row mb-4">
            <div id="orden-observaciones-div" class="col-md-8">
                <div class="mb-3">
                    <label class="form-label"><strong>Observaciones:</strong></label>
                    <textarea class="form-control" id="orden-observaciones" rows="3" disabled></textarea>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center border p-2 mt-4">
                    <div class="mb-3 border-bottom pb-4">
                        <p class="mb-1"><strong>Responsable de Asignación</strong></p>
                        <p class="mb-0" id="orden-responsable"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Datos para impresión -->
        <div class="d-none d-print-block text-center mt-4">
            <p class="mb-0">Documento generado el: <span id="fecha-generacion"></span></p>
        </div>
    </div>
</div>
