<!-- Modal para el orden de trabajo -->

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Orden de Trabajo</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <!-- Contenedor principal para impresión -->
    <div id="formato-orden-trabajo" class="p-4 border">
        <!-- Encabezado institucional -->
        <div class="text-center mb-4">
            <h3 class="mb-1">DIRECCIÓN DE SERVICIOS GENERALES</h3>
            <h4 class="mb-1">ASIGNACIÓN DE ORDEN DE TRABAJO</h4>
            <p class="mb-0">Fecha: <span id="orden-fecha">10/06/2023</span> | Hora: <span id="orden-hora">09:30:45</span></p>
        </div>

        <!-- Datos generales compactos -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label"><strong>Departamento:</strong></label>
                    <div class="border-bottom pb-1" id="orden-departamento">Herrería</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label"><strong>Área:</strong></label>
                    <div class="border-bottom pb-1" id="orden-area">Hilandera</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label"><strong>Turno:</strong></label>
                    <div class="border-bottom pb-1" id="orden-turno">Mañana</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label"><strong>Fecha Inicio:</strong></label>
                    <div class="border-bottom pb-1" id="orden-inicio">10/06/2023</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label"><strong>Ubicación:</strong></label>
                    <div class="border-bottom pb-1" id="orden-ubicacion">Planta Baja</div>
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
                            <th width="5%">#</th>
                            <th width="35%">Nombre</th>
                            <th width="30%">Cargo</th>
                            <th width="30%">Firma</th>
                        </tr>
                    </thead>
                    <tbody id="personal-lista">
                        <tr>
                            <td>1</td>
                            <td>Juan Pérez</td>
                            <td>Herrero</td>
                            <td class="firma-placeholder" style="height: 30px;"></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Carlos López</td>
                            <td>Ayudante de Herrero</td>
                            <td class="firma-placeholder" style="height: 30px;"></td>
                        </tr>
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
                            <th width="60%">Descripción de la Tarea</th>
                            <th width="35%">Materiales</th>
                        </tr>
                    </thead>
                    <tbody id="tareas-lista">
                        <tr>
                            <td>1</td>
                            <td id="orden-descripcion">Reparación de estructura metálica en área de producción</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Observaciones y firmas -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label"><strong>Observaciones:</strong></label>
                    <textarea class="form-control" id="orden-observaciones" rows="3">Priorizar la reparación de la estructura antes del turno de la tarde. Verificar medidas exactas para los soportes de la nueva maquinaria.</textarea>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center border p-2 mt-4">
                    <div class="mb-3 border-bottom pb-4">
                        <p class="mb-1"><strong>Responsable de Asignación</strong></p>
                        <div class="firma-placeholder" style="height: 50px;"></div>
                        <p class="mb-0">Ing. Roberto Martínez</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Datos para impresión -->
        <div class="d-none d-print-block text-center mt-4">
            <p class="mb-0">Documento generado el: <span id="fecha-generacion">10/06/2023 09:30</span></p>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
    <button type="button" class="btn btn-success" onclick="imprimirOrden()">
        <i class="fas fa-print me-2"></i>Imprimir
    </button>
    <button type="button" class="btn btn-primary" onclick="generarPDF()">
        <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
    </button>
</div>


<script>
    // Esta función se llamaría al guardar una tarea o cuando se quiera generar la orden
    function imprimirOrden() {
        // Obtener el elemento a imprimir
        const elemento = document.getElementById('formato-orden-trabajo');

        // Configuración para imprimir
        const ventanaImpresion = window.open('', '_blank');
        ventanaImpresion.document.write(`
        <html>
            <head>
                <title>Orden de Trabajo - ${$('#orden-departamento').text()}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { padding: 20px; }
                    @page { size: auto; margin: 5mm; }
                    @media print {
                        .no-print { display: none !important; }
                        body { padding: 0; }
                        #formato-orden-trabajo { font-size: 12px; }
                        .table { margin-bottom: 5px; }
                        .table th, .table td { padding: 4px; }
                    }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                ${elemento.innerHTML}
            </body>
        </html>
    `);
        ventanaImpresion.document.close();
    }

    function generarPDF() {
        alert("Función de generación de PDF se implementará con la librería correspondiente");
    }
</script>

<style>
    /* Estilos específicos para el formato de orden de trabajo */
    #formato-orden-trabajo {
        background-color: white;
        font-family: Arial, sans-serif;
        font-size: 14px;
    }

    .firma-placeholder {
        border: 1px dashed #ccc;
        margin: 0 auto;
        width: 80%;
    }

    .table th {
        background-color: #f8f9fa;
        text-align: center;
        font-size: 13px;
    }

    .table td {
        vertical-align: middle;
        font-size: 13px;
    }

    .border-bottom {
        min-height: 24px;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        body {
            padding: 0;
            background: white;
        }

        #formato-orden-trabajo {
            border: none;
            padding: 0;
            font-size: 12px;
        }

        .table th,
        .table td {
            padding: 4px;
        }
    }
</style>