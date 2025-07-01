<!-- Modal para el orden de trabajo -->
<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Orden de Trabajo Agrupada</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <!-- Contenedor principal para impresión -->
    <div id="formato-orden-trabajo" class="p-2">
        <!-- Encabezado institucional compacto -->
        <div class="text-center mb-3">
            <h4 class="mb-0">DIRECCIÓN DE SERVICIOS GENERALES</h4>
            <h5 class="mb-1">ORDEN DE TRABAJO</h5>
            <div class="d-flex justify-content-center gap-3">
                <span>Fecha: <span id="orden-fecha">${new Date().toLocaleDateString()}</span></span>
                <span>|</span>
                <span>Hora: <span id="orden-hora">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span></span>
            </div>
        </div>

        <!-- Datos generales en línea -->
        <div class="d-flex flex-wrap gap-3 mb-2">
            <div>
                <strong>Personal:</strong> <span id="orden-personal">Juan Pérez, Carlos López</span>
            </div>
            <div>
                <strong>Cargo:</strong> <span id="orden-cargo">Herrero, Ayudante</span>
            </div>
            <div>
                <strong>Departamento:</strong> <span id="orden-departamento">Herrería</span>
            </div>
            <div>
                <strong>Área:</strong> <span id="orden-area">Hilandera</span>
            </div>
            <div>
                <strong>Turno:</strong> <span id="orden-turno">Mañana</span>
            </div>
        </div>

        <!-- Tareas agrupadas -->
        <div class="mb-2">
            <h6 class="border-bottom pb-1"><strong>TAREAS ASIGNADAS</strong></h6>
            <table class="table table-sm mb-1">
                <thead>
                    <tr class="bg-light">
                        <th width="5%">#</th>
                        <th width="65%">Descripción</th>
                        <th width="15%">Firma</th>
                        <th width="15%">Fecha/Hora</th>
                    </tr>
                </thead>
                <tbody id="tareas-lista">
                    <!-- Se llenará dinámicamente -->
                </tbody>
            </table>
        </div>

        <!-- Responsable y observaciones -->
        <div class="d-flex justify-content-between border-top pt-2">
            <div style="width: 65%">
                <strong>Observaciones:</strong>
                <div style="border-bottom: 1px dashed #ccc; min-height: 40px;"></div>
            </div>
            <div style="width: 30%">
                <div class="text-center">
                    <strong>Responsable</strong>
                    <div style="border-bottom: 1px solid #000; width: 100%; margin: 10px 0;"></div>
                    <small class="text-muted">Nombre y firma</small>
                </div>
            </div>
        </div>

        <!-- Datos para impresión -->
        <div class="text-center mt-2" style="font-size: 0.8em;">
            <p class="mb-0">Documento generado el: ${new Date().toLocaleString()}</p>
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
    // Función para generar la vista previa con tareas agrupadas
    function generarVistaPrevia(tareasAgrupadas) {
        // Agrupar por personal
        const ordenesPorPersona = {};
        
        tareasAgrupadas.forEach(tarea => {
            tarea.personal.forEach(persona => {
                if (!ordenesPorPersona[persona.id]) {
                    ordenesPorPersona[persona.id] = {
                        persona: persona,
                        tareas: []
                    };
                }
                ordenesPorPersona[persona.id].tareas.push(tarea);
            });
        });

        // Generar HTML para cada orden
        const contenedor = document.getElementById('preview-agrupacion');
        contenedor.innerHTML = '';
        
        Object.values(ordenesPorPersona).forEach((grupo, index) => {
            const ordenHTML = `
                <div class="orden-trabajo mb-4" id="orden-${grupo.persona.id}">
                    <div class="text-center mb-2">
                        <h4 class="mb-0">DIRECCIÓN DE SERVICIOS GENERALES</h4>
                        <h5 class="mb-1">ORDEN DE TRABAJO</h5>
                        <div class="d-flex justify-content-center gap-3">
                            <span>Fecha: ${new Date().toLocaleDateString()}</span>
                            <span>|</span>
                            <span>Hora: ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mb-2">
                        <div><strong>Personal:</strong> ${grupo.persona.nombre_completo}</div>
                        <div><strong>Cargo:</strong> ${grupo.persona.cargo}</div>
                        ${grupo.tareas[0].departamento ? `<div><strong>Depto:</strong> ${grupo.tareas[0].departamento}</div>` : ''}
                        ${grupo.tareas[0].area ? `<div><strong>Área:</strong> ${grupo.tareas[0].area}</div>` : ''}
                    </div>

                    <div class="mb-2">
                        <h6 class="border-bottom pb-1"><strong>TAREAS ASIGNADAS</strong></h6>
                        <table class="table table-sm mb-1">
                            <thead>
                                <tr class="bg-light">
                                    <th width="5%">#</th>
                                    <th width="65%">Descripción</th>
                                    <th width="15%">Firma</th>
                                    <th width="15%">Fecha/Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${grupo.tareas.map((tarea, i) => `
                                    <tr>
                                        <td>${i + 1}</td>
                                        <td>${tarea.descripcion}</td>
                                        <td style="border-bottom: 1px dashed #ccc;"></td>
                                        <td style="border-bottom: 1px dashed #ccc;"></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between border-top pt-2">
                        <div style="width: 65%">
                            <strong>Observaciones:</strong>
                            <div style="border-bottom: 1px dashed #ccc; min-height: 40px;"></div>
                        </div>
                        <div style="width: 30%">
                            <div class="text-center">
                                <strong>Responsable</strong>
                                <div style="border-bottom: 1px solid #000; width: 100%; margin: 10px 0;"></div>
                                <small class="text-muted">Nombre y firma</small>
                            </div>
                        </div>
                    </div>
                </div>
                ${index < Object.keys(ordenesPorPersona).length - 1 ? '<div style="page-break-after: always;"></div>' : ''}
            `;
            
            contenedor.insertAdjacentHTML('beforeend', ordenHTML);
        });
    }

    // Función para imprimir
    function imprimirOrden() {
        const elemento = document.getElementById('formato-orden-trabajo');
        const ventanaImpresion = window.open('', '_blank');
        
        ventanaImpresion.document.write(`
            <html>
                <head>
                    <title>Orden de Trabajo</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { padding: 5mm; font-size: 12px; }
                        @page { size: A4 landscape; margin: 5mm; }
                        @media print {
                            .no-print { display: none !important; }
                            body { padding: 0; }
                            .orden-trabajo { margin-bottom: 5mm; }
                            table { width: 100%; }
                            .table th, .table td { padding: 3px; }
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
        // Implementación con librería como jsPDF o similar
        alert("Exportar a PDF se implementará con la librería correspondiente");
    }
</script>

<style>
    /* Estilos optimizados para impresión */
    #formato-orden-trabajo {
        font-family: Arial, sans-serif;
        font-size: 13px;
    }
    
    .table {
        margin-bottom: 5px;
    }
    
    .table th {
        background-color: #f8f9fa !important;
    }
    
    @media print {
        body {
            padding: 5mm;
            font-size: 11px;
        }
        
        .orden-trabajo {
            page-break-inside: avoid;
            margin-bottom: 5mm;
        }
        
        .no-print {
            display: none !important;
        }
    }
</style>