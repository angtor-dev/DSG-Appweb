<div class="panel-header">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Reportes de Tareas</h3>
                <span class="opacity-75 mb-2">Genere reportes detallados de tareas</span>
            </div>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="row">
        <!-- Panel de Configuración -->
        <div class="col-md-4">
            <div class="card border-0 box-shadow-alt">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">Configurar Reporte</h4>
                </div>
                <div class="card-body">
                    <form id="formReporte">
                        <!-- Tipo de Reporte -->
                        <div class="mb-3">
                            <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
                            <select name="tipo_reporte" id="tipo_reporte" class="form-select" required>
                                <option value="">Seleccione un tipo</option>
                                <option value="productividad_trabajador">Productividad por Trabajador</option>
                                <option value="rendimiento_division">Rendimiento por División</option>
                                <option value="general_extenso">Reporte General Extenso</option>
                            </select>
                        </div>

                        <!-- Rango de Fechas -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="fechaInicio" class="form-label">Desde</label>
                                <input type="date" class="form-control" id="fechaInicio" name="fechaInicio">
                            </div>
                            <div class="col-6">
                                <label for="fechaFin" class="form-label">Hasta</label>
                                <input type="date" class="form-control" id="fechaFin" name="fechaFin">
                            </div>
                        </div>

                        <!-- Filtros para Reporte por Trabajador -->
                        <div class="mb-3" id="filtroTrabajadorContainer" style="display:none;">
                            <label for="filtroTrabajador" class="form-label">Trabajador Específico</label>
                            <select name="filtroTrabajador" id="filtroTrabajador" class="form-select">
                                <option value="">Todos los trabajadores</option>
                                <!-- Aquí se cargarían los trabajadores desde la BD -->
                            </select>
                        </div>

                        <!-- Filtros para Reporte por División -->
                        <div class="mb-3" id="filtroDivisionContainer" style="display:none;">
                            <label for="filtroDivision" class="form-label">División Específica</label>
                            <select name="filtroDivision" id="filtroDivision" class="form-select">
                                <option value="">Todas las divisiones</option>
                                <!-- Aquí se cargarían las divisiones desde la BD -->
                            </select>
                        </div>

                        <button type="button" class="btn btn-success w-100" id="btnGenerarPDF">
                            <i class="fas fa-file-pdf mr-2"></i>Generar PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Área de Descripción -->
        <div class="col-md-8">
            <div class="card border-0 box-shadow-alt">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0" id="tituloReporte">Descripción del Reporte</h4>
                </div>
                <div class="card-body">
                    <div id="descripcionReporte">
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-info-circle fa-4x mb-3"></i>
                            <h4>Seleccione un tipo de reporte</h4>
                            <p>Elija entre las opciones disponibles para ver la descripción</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Carga -->
<div class="modal fade" id="modalCarga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Generando reporte...</span>
                </div>
                <h5>Generando PDF</h5>
                <p class="mb-0">Por favor espere...</p>
            </div>
        </div>
    </div>
</div>


<script>
// En tu vista de reportes
document.addEventListener('DOMContentLoaded', function() {
    const tipoReporte = document.getElementById('tipo_reporte');
    const btnGenerarPDF = document.getElementById('btnGenerarPDF');
    const filtroTrabajadorContainer = document.getElementById('filtroTrabajadorContainer');
    const filtroDivisionContainer = document.getElementById('filtroDivisionContainer');
    const descripcionReporte = document.getElementById('descripcionReporte');

    
    // Cargar datos iniciales
    cargarDatosIniciales();
    
    // Mostrar/ocultar filtros
    tipoReporte.addEventListener('change', function() {
        filtroTrabajadorContainer.style.display = 'none';
        filtroDivisionContainer.style.display = 'none';
        
        if (this.value === 'productividad_trabajador') {
            filtroTrabajadorContainer.style.display = 'block';
            mostrarDescripcionProductividad();
        } else if (this.value === 'rendimiento_division') {
            filtroDivisionContainer.style.display = 'block';
            mostrarDescripcionRendimiento();
        } else if (this.value === 'general_extenso') {
            mostrarDescripcionProductividad1();
        }

    });

    function mostrarDescripcionProductividad() {
        descripcionReporte.innerHTML = `
            <h5>Reporte de Productividad por Trabajador</h5>
            <p>Este reporte mostrará:</p>
            <ul>
                <li>Tareas asignadas a cada trabajador en el período seleccionado</li>
                <li>Comparación entre tareas completadas y pendientes</li>
                <li>Eficiencia y rendimiento individual</li>
                <li>Promedio de tiempo por tarea</li>
                <li>Recursos utilizados por cada trabajador</li>
            </ul>
            <div class="alert alert-info">
                <small><i class="fas fa-info-circle mr-2"></i>Este reporte ayuda a identificar a los trabajadores más productivos y aquellos que pueden necesitar apoyo adicional.</small>
            </div>
        `;
    }

    function mostrarDescripcionProductividad1() {
        descripcionReporte.innerHTML = `
            <h5>Reporte de General</h5>
            <p>Este reporte mostrará:</p>
            <ul>
                <li>Resumen ejecutivo</li>
                <li>Distribucion de tareas</li>
                <li>Detalle completo de tareas</li>
                
            </ul>
            <div class="alert alert-info">
                <small><i class="fas fa-info-circle mr-2"></i>Este reporte ayuda a identificar tareas.</small>
            </div>
        `;
    }

    function mostrarDescripcionRendimiento() {
        descripcionReporte.innerHTML = `
            <h5>Reporte de Rendimiento por División</h5>
            <p>Este reporte mostrará:</p>
            <ul>
                <li>Total de tareas asignadas por división/área</li>
                <li>Tareas completadas vs pendientes por área</li>
                <li>Recursos utilizados por cada división</li>
                <li>Evaluaciones de calidad por área</li>
                <li>Comparación de eficiencia entre divisiones</li>
            </ul>
            <div class="alert alert-info">
                <small><i class="fas fa-info-circle mr-2"></i>Este reporte permite evaluar el desempeño de cada área y distribuir mejor los recursos.</small>
            </div>
        `;
    }

    function mostrarDescripcionGeneral() {
        descripcionReporte.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="fas fa-info-circle fa-4x mb-3"></i>
                <h4>Seleccione un tipo de reporte</h4>
                <p>Elija entre las opciones disponibles para ver la descripción</p>
            </div>
        `;
    }
    
    // Generar PDF
    btnGenerarPDF.addEventListener('click', function() {
        generarReporte();
    });
    
    function cargarDatosIniciales() {
        $.ajax({
            url: 'Tareas?ajax=cargar_datos',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    llenarSelectTrabajadores(response.data.trabajadores);
                    llenarSelectDivisiones(response.data.divisiones);
                }
            },
            error: function() {
                console.error('Error al cargar datos iniciales');
            }
        });
    }
    
    function llenarSelectTrabajadores(trabajadores) {
        const select = document.getElementById('filtroTrabajador');
        select.innerHTML = '<option value="">Todos los trabajadores</option>';
        
        trabajadores.forEach(trabajador => {
            const option = document.createElement('option');
            option.value = trabajador.id;
            option.textContent = `${trabajador.nombre_completo} - ${trabajador.cargo} (${trabajador.division})`;
            select.appendChild(option);
        });
    }
    
    function llenarSelectDivisiones(divisiones) {
        const select = document.getElementById('filtroDivision');
        select.innerHTML = '<option value="">Todas las divisiones</option>';
        
        divisiones.forEach(division => {
            const option = document.createElement('option');
            option.value = division.id;
            option.textContent = division.nombre;
            select.appendChild(option);
        });
    }
    
    function generarPDF(datos, filtros) {
    

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Configuración inicial
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 15;
    let yPosition = margin;
    
    // Función para agregar nueva página si es necesario
    function checkPageHeight(extraSpace = 10) {
        if (yPosition + extraSpace > doc.internal.pageSize.getHeight() - margin) {
            doc.addPage();
            yPosition = margin;
            return true;
        }
        return false;
    }
    
    // Encabezado del reporte
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('REPORTE DE GESTIÓN DE TAREAS', pageWidth / 2, yPosition, { align: 'center' });
    yPosition += 10;
    
    doc.setFontSize(12);
    doc.setFont('helvetica', 'normal');
    doc.text(`Tipo: ${getTipoReporteNombre(filtros.tipo)}`, margin, yPosition);
    yPosition += 6;
    doc.text(`Período: ${formatearFecha(filtros.fechaInicio)} - ${formatearFecha(filtros.fechaFin)}`, margin, yPosition);
    yPosition += 6;
    doc.text(`Generado: ${new Date().toLocaleDateString()}`, margin, yPosition);
    yPosition += 15;
    
    // Línea separadora
    doc.setDrawColor(200, 200, 200);
    doc.line(margin, yPosition, pageWidth - margin, yPosition);
    yPosition += 10;
    
    // Contenido según el tipo de reporte
    if (filtros.tipo === 'general_extenso') {
        generarPDFGeneralExtenso(doc, { ...datos, filtros }, yPosition, pageWidth, margin);
    } else if (filtros.tipo === 'productividad_trabajador') {
        generarPDFProductividadTrabajador(doc, datos, yPosition, pageWidth, margin);
    } else if (filtros.tipo === 'rendimiento_division') {
        generarPDFRendimientoDivision(doc, datos, yPosition, pageWidth, margin);
    }
    
    // Guardar el PDF
    const fileName = `reporte_tareas_${filtros.tipo}_${filtros.fechaInicio}_${filtros.fechaFin}.pdf`;
    doc.save(fileName);
}

function generarPDFGeneralExtenso(doc, datos, yPosition, pageWidth, margin) {
    const { tareas, estadisticas, total_registros } = datos;
    
    // PORTADA
    doc.setFontSize(20);
    doc.setFont('helvetica', 'bold');
    doc.text('REPORTE GENERAL DE GESTIÓN DE TAREAS', pageWidth / 2, 80, { align: 'center' });
    
    doc.setFontSize(14);
    doc.setFont('helvetica', 'normal');
    doc.text(`Período: ${formatearFecha(datos.filtros.fechaInicio)} - ${formatearFecha(datos.filtros.fechaFin)}`, pageWidth / 2, 100, { align: 'center' });
    doc.text(`Generado: ${new Date().toLocaleDateString('es-ES')}`, pageWidth / 2, 110, { align: 'center' });
    doc.text(`Total de registros: ${total_registros}`, pageWidth / 2, 120, { align: 'center' });
    
    doc.addPage();
    yPosition = margin;

    // SECCIÓN 1: RESUMEN EJECUTIVO
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('RESUMEN EJECUTIVO', margin, yPosition);
    yPosition += 15;
    
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    
    const resumenData = [
        ['Total de Tareas', estadisticas.total_tareas],
        ['Tareas Completadas', estadisticas.completadas],
        ['Tareas en Progreso', estadisticas.en_progreso],
        ['Tareas Canceladas', estadisticas.canceladas],
        ['Tareas Vencidas', estadisticas.vencidas],
        ['Departamentos Involucrados', estadisticas.departamentos_involucrados],
        ['Áreas Atendidas', estadisticas.areas_atendidas],
        ['Trabajadores Involucrados', estadisticas.trabajadores_involucrados],
        ['Tipos de Recursos Utilizados', estadisticas.tipos_recursos_utilizados],
        ['Promedio Días Completación', formatearNumero(estadisticas.promedio_dias_completacion)]
    ];
    
    doc.autoTable({
        startY: yPosition,
        body: resumenData,
        margin: { left: margin, right: margin },
        styles: {
            fontSize: 10,
            cellPadding: 5,
        },
        theme: 'grid',
        columnStyles: {
            0: { fontStyle: 'bold', cellWidth: 60 },
            1: { cellWidth: 40 }
        }
    });
    
    yPosition = doc.lastAutoTable.finalY + 15;

    // SECCIÓN 2: DISTRIBUCIÓN POR ESTADO (Gráfico de torta simulado)
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('DISTRIBUCIÓN DE TAREAS POR ESTADO', margin, yPosition);
    yPosition += 10;
    
    const estadosData = [
        ['Completadas', estadisticas.completadas, '#28a745'],
        ['En Progreso', estadisticas.en_progreso, '#ffc107'],
        ['Canceladas', estadisticas.canceladas, '#dc3545'],
        ['Vencidas', estadisticas.vencidas, '#6c757d']
    ].filter(item => item[1] > 0);
    
    estadosData.forEach((estado, index) => {
        const porcentaje = ((estado[1] / estadisticas.total_tareas) * 100).toFixed(1);
        doc.setFontSize(9);
        doc.setTextColor(0, 0, 0);
        doc.text(`• ${estado[0]}: ${estado[1]} tareas (${porcentaje}%)`, margin + 5, yPosition + (index * 5));
    });
    
    yPosition += (estadosData.length * 5) + 15;

    // SECCIÓN 3: DETALLE COMPLETO DE TAREAS (máximo 100)
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('DETALLE COMPLETO DE TAREAS', margin, yPosition);
    yPosition += 10;
    
    doc.setFontSize(8);
    doc.setTextColor(100, 100, 100);
    doc.text(`Mostrando las ${Math.min(total_registros, 100)} tareas más recientes`, margin, yPosition);
    yPosition += 8;

    // Preparar datos para la tabla detallada
    const tableData = tareas.map((tarea, index) => [
        (index + 1).toString(),
        tarea.descripcion?.substring(0, 50) + (tarea.descripcion?.length > 50 ? '...' : '') || 'Sin descripción',
        tarea.area_nombre || 'N/A',
        tarea.departamento_nombre || 'N/A',
        tarea.personal_asignado?.substring(0, 30) + (tarea.personal_asignado?.length > 30 ? '...' : '') || 'Sin asignar',
        tarea.recursos_utilizados?.substring(0, 30) + (tarea.recursos_utilizados?.length > 30 ? '...' : '') || 'Sin recursos',
        tarea.estado_detallado || tarea.estado_tarea,
        tarea.evaluacion_supervisor || 'Pendiente',
        tarea.dias_completacion ? tarea.dias_completacion + ' días' : 'N/A',
        formatearFecha(tarea.fechaCreacion?.split(' ')[0])
    ]);

    // Configuración de la tabla detallada
    doc.autoTable({
        startY: yPosition,
        head: [
            ['#', 'Descripción', 'Área', 'Departamento', 'Personal', 'Recursos', 'Estado', 'Evaluación', 'Tiempo', 'Fecha Creación']
        ],
        body: tableData,
        margin: { left: margin, right: margin },
        styles: {
            fontSize: 6,
            cellPadding: 2,
            lineColor: [200, 200, 200],
            lineWidth: 0.1
        },
        headStyles: {
            fillColor: [41, 128, 185],
            textColor: 255,
            fontStyle: 'bold',
            fontSize: 6
        },
        alternateRowStyles: {
            fillColor: [250, 250, 250]
        },
        columnStyles: {
            0: { cellWidth: 8, fontStyle: 'bold' },
            1: { cellWidth: 35 },
            2: { cellWidth: 20 },
            3: { cellWidth: 25 },
            4: { cellWidth: 25 },
            5: { cellWidth: 20 },
            6: { cellWidth: 15 },
            7: { cellWidth: 15 },
            8: { cellWidth: 12 },
            9: { cellWidth: 15 }
        },
        pageBreak: 'auto',
        didDrawPage: function(data) {
            // Footer personalizado
            doc.setFontSize(8);
            doc.setTextColor(128, 128, 128);
            doc.text(
                `Página ${data.pageNumber} de ${doc.internal.getNumberOfPages()} - Reporte General de Gestión de Tareas`,
                pageWidth / 2,
                doc.internal.pageSize.getHeight() - 10,
                { align: 'center' }
            );
            
            // Header en cada página después de la primera
            if (data.pageNumber > 1) {
                doc.setFontSize(10);
                doc.setFont('helvetica', 'bold');
                doc.setTextColor(0, 0, 0);
                doc.text('REPORTE GENERAL DE GESTIÓN DE TAREAS', margin, 10);
                
                doc.setFontSize(8);
                doc.setFont('helvetica', 'normal');
                doc.text(`Período: ${formatearFecha(datos.filtros.fechaInicio)} - ${formatearFecha(datos.filtros.fechaFin)}`, pageWidth - margin, 10, { align: 'right' });
            }
        }
    });

    // SECCIÓN 4: ANÁLISIS Y RECOMENDACIONES (última página)
    const finalY = doc.lastAutoTable.finalY + 10;
    
    if (finalY < doc.internal.pageSize.getHeight() - 50) {
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('ANÁLISIS Y RECOMENDACIONES', margin, finalY);
        
        doc.setFontSize(9);
        doc.setFont('helvetica', 'normal');
        
        let analisisY = finalY + 10;
        
        // Análisis automático basado en los datos
        const tasaCompletacion = (estadisticas.completadas / estadisticas.total_tareas) * 100;
        const tasaVencimiento = (estadisticas.vencidas / estadisticas.total_tareas) * 100;
        
        if (tasaCompletacion < 60) {
            doc.text('• 📉 Se recomienda mejorar la tasa de completación de tareas', margin, analisisY);
            analisisY += 5;
        }
        
        if (tasaVencimiento > 10) {
            doc.text('• ⚠️  Alta tasa de tareas vencidas, revisar procesos de seguimiento', margin, analisisY);
            analisisY += 5;
        }
        
        if (estadisticas.promedio_dias_completacion > 7) {
            doc.text('• 🕒 Tiempo promedio de completación elevado, optimizar asignaciones', margin, analisisY);
            analisisY += 5;
        }
        
        if (estadisticas.trabajadores_involucrados < 5 && estadisticas.total_tareas > 20) {
            doc.text('• 👥 Pocos trabajadores para el volumen de tareas, considerar redistribución', margin, analisisY);
            analisisY += 5;
        }
        
        // Recomendaciones generales
        doc.text('• 📊 Monitorear regularmente el progreso de tareas en curso', margin, analisisY);
        analisisY += 5;
        doc.text('• 🔍 Revisar tareas vencidas para identificar causas recurrentes', margin, analisisY);
        analisisY += 5;
        doc.text('• ✅ Establecer metas de eficiencia por departamento', margin, analisisY);
    }
}



function generarPDFProductividadTrabajador(doc, datos, yPosition, pageWidth, margin) {
    // Título de la sección
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('PRODUCTIVIDAD POR TRABAJADOR', margin, yPosition);
    yPosition += 15;
    
    if (datos.length === 0) {
        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text('No hay datos para mostrar en el período seleccionado', margin, yPosition);
        return;
    }
    
    // Preparar datos para la tabla - CORREGIDO
    const tableData = datos.map(trabajador => [
        `${trabajador.nombre || ''} ${trabajador.apellido || ''}`.trim() || 'Sin nombre',
        trabajador.total_tareas || 0,
        trabajador.tareas_completadas || 0,
        trabajador.tareas_activas || 0,
        trabajador.tareas_canceladas || 0,
        trabajador.tareas_vencidas || 0,
        `${trabajador.eficiencia_porcentaje || 0}%`,
        // CORRECCIÓN: Usar la función auxiliar
        formatearNumero(trabajador.promedio_dias_completar)
    ]);
    
    // Configurar y generar tabla
    doc.autoTable({
        startY: yPosition,
        head: [
            ['Trabajador', 'Total', 'Completadas', 'Activas', 'Canceladas', 'Vencidas', 'Eficiencia', 'Prom. Días']
        ],
        body: tableData,
        margin: { left: margin, right: margin },
        styles: {
            fontSize: 8,
            cellPadding: 3,
        },
        headStyles: {
            fillColor: [41, 128, 185],
            textColor: 255,
            fontStyle: 'bold'
        },
        alternateRowStyles: {
            fillColor: [245, 245, 245]
        },
        columnStyles: {
            0: { cellWidth: 35 },
            1: { cellWidth: 15 },
            2: { cellWidth: 20 },
            3: { cellWidth: 15 },
            4: { cellWidth: 20 },
            5: { cellWidth: 15 },
            6: { cellWidth: 20 },
            7: { cellWidth: 20 }
        },
        didDrawPage: function(data) {
            // Footer en cada página
            doc.setFontSize(8);
            doc.setTextColor(128, 128, 128);
            doc.text(
                `Página ${doc.internal.getNumberOfPages()}`,
                pageWidth / 2,
                doc.internal.pageSize.getHeight() - 10,
                { align: 'center' }
            );
        }
    });
    
    // Estadísticas resumen después de la tabla - CORREGIDO
    const finalY = doc.lastAutoTable.finalY + 10;
    
    if (finalY < doc.internal.pageSize.getHeight() - 50) {
        doc.setFontSize(12);
        doc.setFont('helvetica', 'bold');
        doc.text('RESUMEN ESTADÍSTICO', margin, finalY);
        
        doc.setFontSize(10);
        doc.setFont('helvetica', 'normal');
        
        const totalTrabajadores = datos.length;
        const totalTareas = datos.reduce((sum, t) => sum + parseInt(t.total_tareas || 0), 0);
        
        // CORRECCIÓN: Manejar eficiencia nula
        const eficienciasValidas = datos
            .map(t => parseFloat(t.eficiencia_porcentaje || 0))
            .filter(ef => !isNaN(ef));
        const promedioEficiencia = eficienciasValidas.length > 0 
            ? eficienciasValidas.reduce((sum, ef) => sum + ef, 0) / eficienciasValidas.length 
            : 0;
        
        // CORRECCIÓN: Manejar trabajador más eficiente
        const trabajadoresConEficiencia = datos.filter(t => t.eficiencia_porcentaje != null);
        const mejorTrabajador = trabajadoresConEficiencia.length > 0 
            ? trabajadoresConEficiencia.reduce((best, current) => 
                parseFloat(current.eficiencia_porcentaje) > parseFloat(best.eficiencia_porcentaje) ? current : best
            )
            : { nombre: 'N/A', apellido: '', eficiencia_porcentaje: 0 };
        
        // CORRECCIÓN: Manejar promedio de días
        const diasValidos = datos
            .map(t => parseFloat(t.promedio_dias_completar || 0))
            .filter(d => !isNaN(d) && d > 0);
        const promedioDias = diasValidos.length > 0 
            ? diasValidos.reduce((sum, d) => sum + d, 0) / diasValidos.length 
            : 0;
        
        doc.text(`• Total de trabajadores: ${totalTrabajadores}`, margin, finalY + 8);
        doc.text(`• Total de tareas asignadas: ${totalTareas}`, margin, finalY + 16);
        doc.text(`• Eficiencia promedio: ${promedioEficiencia.toFixed(2)}%`, margin, finalY + 24);
        doc.text(`• Promedio días completación: ${promedioDias.toFixed(1)} días`, margin, finalY + 32);
        doc.text(`• Trabajador más eficiente: ${mejorTrabajador.nombre} ${mejorTrabajador.apellido} (${mejorTrabajador.eficiencia_porcentaje}%)`, margin, finalY + 40);
    }
}
function generarPDFRendimientoDivision(doc, datos, yPosition, pageWidth, margin) {
    // Título de la sección
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('RENDIMIENTO POR DIVISIÓN', margin, yPosition);
    yPosition += 15;
    
    if (datos.length === 0) {
        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text('No hay datos para mostrar en el período seleccionado', margin, yPosition);
        return;
    }
    
    // Preparar datos para la tabla
   const tableData = datos.map(division => [
        division.division_nombre || 'Sin nombre',
        division.total_tareas || 0,
        division.tareas_completadas || 0,
        division.tareas_activas || 0,
        division.tareas_canceladas || 0,
        division.tareas_vencidas || 0,
        division.total_trabajadores_asignados || 0,
        division.total_recursos_utilizados || 0,
        `${division.eficiencia_porcentaje || 0}%`,
       
        (division.promedio_dias_completar && !isNaN(parseFloat(division.promedio_dias_completar))) 
            ? parseFloat(division.promedio_dias_completar).toFixed(1) 
            : 'N/A'
    ]);
    
    // Configurar y generar tabla
    doc.autoTable({
        startY: yPosition,
        head: [
            ['División', 'Total Tareas', 'Completadas', 'Activas', 'Canceladas', 'Vencidas', 'Trabajadores', 'Recursos', 'Eficiencia', 'Prom. Días']
        ],
        body: tableData,
        margin: { left: margin, right: margin },
        styles: {
            fontSize: 7,
            cellPadding: 2,
        },
        headStyles: {
            fillColor: [39, 174, 96],
            textColor: 255,
            fontStyle: 'bold'
        },
        alternateRowStyles: {
            fillColor: [245, 245, 245]
        },
        columnStyles: {
            0: { cellWidth: 30 },
            1: { cellWidth: 15 },
            2: { cellWidth: 15 },
            3: { cellWidth: 12 },
            4: { cellWidth: 15 },
            5: { cellWidth: 12 },
            6: { cellWidth: 15 },
            7: { cellWidth: 15 },
            8: { cellWidth: 15 },
            9: { cellWidth: 15 }
        },
        didDrawPage: function(data) {
            // Footer en cada página
            doc.setFontSize(8);
            doc.setTextColor(128, 128, 128);
            doc.text(
                `Página ${doc.internal.getNumberOfPages()}`,
                pageWidth / 2,
                doc.internal.pageSize.getHeight() - 10,
                { align: 'center' }
            );
        }
    });
    
    // Estadísticas resumen después de la tabla
    const finalY = doc.lastAutoTable.finalY + 10;
    
    if (finalY < doc.internal.pageSize.getHeight() - 50) {
        doc.setFontSize(12);
        doc.setFont('helvetica', 'bold');
        doc.text('RESUMEN ESTADÍSTICO', margin, finalY);
        
        doc.setFontSize(10);
        doc.setFont('helvetica', 'normal');
        
        const totalDivisiones = datos.length;
        const totalTareas = datos.reduce((sum, d) => sum + parseInt(d.total_tareas), 0);
        const promedioEficiencia = datos.reduce((sum, d) => sum + parseFloat(d.eficiencia_porcentaje), 0) / totalDivisiones;
        const mejorDivision = datos.reduce((best, current) => 
            parseFloat(current.eficiencia_porcentaje) > parseFloat(best.eficiencia_porcentaje) ? current : best
        );
        
        doc.text(`• Total de divisiones: ${totalDivisiones}`, margin, finalY + 8);
        doc.text(`• Total de tareas: ${totalTareas}`, margin, finalY + 16);
        doc.text(`• Eficiencia promedio: ${promedioEficiencia.toFixed(2)}%`, margin, finalY + 24);
        doc.text(`• División más eficiente: ${mejorDivision.division_nombre} (${mejorDivision.eficiencia_porcentaje}%)`, margin, finalY + 32);
    }
}

// Funciones auxiliares
function getTipoReporteNombre(tipo) {
    const tipos = {
        'productividad_trabajador': 'Productividad por Trabajador',
        'rendimiento_division': 'Rendimiento por División'
    };
    return tipos[tipo] || tipo;
}

function formatearFecha(fecha) {
    return new Date(fecha + 'T00:00:00').toLocaleDateString('es-ES');
}

// Función mejorada para generar el reporte con validación
function generarReporte() {
    const formData = new FormData(document.getElementById('formReporte'));
    
    // Validaciones
    if (!formData.get('tipo_reporte')) {
        mostrarError('Por favor seleccione un tipo de reporte');
        return;
    }
    
    if (!formData.get('fechaInicio') || !formData.get('fechaFin')) {
        mostrarError('Por favor seleccione el rango de fechas');
        return;
    }
    
    // Validar que fecha fin no sea menor que fecha inicio
    const fechaInicio = new Date(formData.get('fechaInicio'));
    const fechaFin = new Date(formData.get('fechaFin'));
    
    if (fechaFin < fechaInicio) {
        mostrarError('La fecha final no puede ser menor que la fecha inicial');
        return;
    }
    
    // Mostrar modal de carga
    $('#modalCarga').modal('show');
    
    $.ajax({
        url: 'Tareas',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#modalCarga').modal('hide');
            
            if (response.success) {
                generarPDF(response.data, response.filtros);
                mostrarExito('Reporte generado exitosamente');
            } else {
                mostrarError('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            $('#modalCarga').modal('hide');
            console.error('Error:', error);
            mostrarError('Error al generar el reporte: ' + (xhr.responseJSON?.message || error));
        }
    });
}

// Funciones para mostrar mensajes (asumiendo que existen en tu código)
function mostrarError(mensaje) {
    // Tu implementación existente para mostrar errores
    alert('Error: ' + mensaje);
}

// Agregar esta función al inicio de tu script
function formatearNumero(valor, decimales = 1) {
    if (valor === null || valor === undefined || valor === '' || isNaN(parseFloat(valor))) {
        return 'N/A';
    }
    return parseFloat(valor).toFixed(decimales);
}

function mostrarExito(mensaje) {
    // Tu implementación existente para mostrar éxitos
    console.log('Éxito: ' + mensaje);
}
});
    // Inicializar
    tipoReporte.dispatchEvent(new Event('change'));

</script>














