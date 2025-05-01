
<!-- Modal para el orden de trabajo -->

    <div class="modal-dialog modal-xl">
        <div class="modal-content">
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
                        <p class="mb-0">Fecha: <span id="orden-fecha"></span> | Hora: <span id="orden-hora"></span></p>
                    </div>
                    
                    <!-- Datos generales -->
                    <div class="row mb-4">
    <div class="col">
        <div class="mb-3">
            <label class="form-label"><strong>Departamento:</strong></label>
            <div class="border-bottom pb-1" id="orden-departamento">[Departamento]</div>
        </div>
    </div>
    <div class="col">
        <div class="mb-3">
            <label class="form-label"><strong>Área:</strong></label>
            <div class="border-bottom pb-1" id="orden-area">[Área]</div>
        </div>
    </div>
    <div class="col">
        <div class="mb-3">
            <label class="form-label"><strong>Turno:</strong></label>
            <div class="border-bottom pb-1" id="orden-turno">[Turno]</div>
        </div>
    </div>
</div>

    <div class="row mb-4">
        <div class="mb-3">
            <label class="form-label"><strong>Fecha de Inicio:</strong></label>
            <div class="border-bottom pb-1" id="orden-inicio">[Fecha de Inicio] 
                    
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
                                    <!-- Se llenará dinámicamente -->
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
                                        <th width="45%">Descripción de la Tarea</th>
                                        <th width="20%">Materiales</th>
                                        <th width="15%">Tiempo Estimado</th>
                                        <th width="15%">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="tareas-lista">
                                    <tr>
                                        <td>1</td>
                                        <td id="orden-descripcion">[Descripción de la tarea principal]</td>
                                        <td id="orden-materiales">[Materiales requeridos]</td>
                                        <td id="orden-tiempo">[Tiempo estimado]</td>
                                        <td>Pendiente</td>
                                    </tr>
                                    <!-- Se pueden agregar más tareas aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Observaciones y firmas -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label"><strong>Observaciones:</strong></label>
                                <textarea class="form-control" id="orden-observaciones" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center border p-2 mt-4">
                                <div class="mb-3 border-bottom pb-4">
                                    <p class="mb-1"><strong>Responsable de Asignación</strong></p>
                                    <div class="firma-placeholder" style="height: 50px;"></div>
                                    <p class="mb-0">Nombre y Firma</p>
                                </div>
                                <div>
                                    <p class="mb-1"><strong>Sello de la Dirección</strong></p>
                                    <div class="sello-placeholder" style="height: 50px;"></div>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="imprimirOrden()">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
                <button type="button" class="btn btn-primary" onclick="generarPDF()">
                    <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                </button>
            </div>
        </div>
    </div>


<!-- Script para manejar la orden de trabajo -->
<script>
// Esta función se llamaría al guardar una tarea o cuando se quiera generar la orden
function mostrarOrdenTrabajo(tareaData) {
    // Llenar los datos del formulario
    const ahora = new Date();
    $('#orden-fecha').text(ahora.toLocaleDateString());
    $('#orden-hora').text(ahora.toLocaleTimeString());
    $('#orden-departamento').text(tareaData.departamento);
    $('#orden-area').text(tareaData.area);
    $('#orden-turno').text(tareaData.turno);
    $('#orden-descripcion').text(tareaData.descripcion);
    $('#orden-materiales').text(tareaData.materiales.join(', '));
    $('#orden-tiempo').text(tareaData.tiempoEstimado);
    
    // Llenar personal asignado
    $('#personal-lista').empty();
    tareaData.personal.forEach((persona, index) => {
        $('#personal-lista').append(`
            <tr>
                <td>${index + 1}</td>
                <td>${persona.nombre}</td>
                <td>${persona.cargo || persona.departamento}</td>
                <td class="firma-placeholder" style="height: 30px;"></td>
            </tr>
        `);
    });
    
    // Mostrar el modal
    $('#modal-orden-trabajo').modal('show');
}

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
    // Aquí iría el código para generar PDF usando librerías como jsPDF o html2pdf
    // Ejemplo con html2pdf.js:
    /*
    const elemento = document.getElementById('formato-orden-trabajo');
    const opciones = {
        margin: 10,
        filename: `orden_trabajo_${$('#orden-departamento').text()}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    html2pdf().from(elemento).set(opciones).save();
    */
    
    alert("Función de generación de PDF se implementará con la librería correspondiente");
}


</script>

<style>
/* Estilos específicos para el formato de orden de trabajo */
#formato-orden-trabajo {
    background-color: white;
    font-family: Arial, sans-serif;
}

.firma-placeholder, .sello-placeholder {
    border: 1px dashed #ccc;
    margin: 0 auto;
    width: 80%;
}

.table th {
    background-color: #f8f9fa;
    text-align: center;
}

.table td {
    vertical-align: middle;
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
    }
}
</style>