<?php

/** @var Tareas $tarea */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Tareas</h3>
                <span class="opacity-75 mb-2">Gestiona a las Tareas de la Dirección de Servicios Generales</span>
            </div>
            <?php if (tienePermiso(Modulo::TAREAS, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-tareas" data-backdrop="static"
                        data-bs-url="<?= LOCAL_DIR ?>/Tareas/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nueva tarea
                    </button>
                </div>
            <?php endif ?>
             <?php if (tienePermiso(Modulo::TAREAS, Permiso::CONSULTAR)): ?>    
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-ordenes" data-backdrop="static"
                        data-bs-url="<?= LOCAL_DIR ?>/Tareas/Ordenes?modal=1">
                        <i class="fa-solid fa-print me-2"></i>
                        Ordenes de trabajo
                    </button>
                </div>

            <?php endif ?>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
            <div class="row card-body p-4">
        <div class="col">
            <div class="card border">
                <div class="card-body justify-content-center align-items-center d-flex flex-column">
                    <h4 class="card-title" id="tareas-activas"><?= $conteoTareas['activo'] ?></h4>
                    <p class="card-text">Tareas activas</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border">
                <div class="card-body justify-content-center align-items-center d-flex flex-column">
                    <h4 class="card-title" id="tareas-vencidas"><?= $conteoTareas['vencida'] ?></h4>
                    <p class="card-text">Tareas Terminadas</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border">
                <div class="card-body justify-content-center align-items-center d-flex flex-column">
                    <h4 class="card-title" id="tareas-canceladas"><?= $conteoTareas['cancelado'] ?></h4>
                    <p class="card-text">Tareas Canceladas</p>
                </div>
            </div>
        </div>
    </div>
 </div>
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Activas</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Terminadas</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Cancelado</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="evaluada-tab" data-bs-toggle="tab" data-bs-target="#evaluada-tab-pane" type="button" role="tab" aria-controls="evaluada-tab-pane" aria-selected="false">Evaluadas</button>
                </li>
               
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                    <div class="table-responsive table-dsg">
                        <table class="datatable table table-striped table-hover" id="tabla-activas">
                            <thead>
                                <tr>
                                    <th style="display: none;">Id</th>
                                    <th>Area</th>
                                    <th>División</th>
                                    <th>Descripcion</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <div class="table-responsive table-dsg">
                        <table class="datatable table table-striped table-hover" id="tabla-vencidas">
                            <thead>
                                <tr>
                                    <th style="display: none;">Id</th>
                                    <th>Area</th>
                                    <th>División</th>
                                    <th>Descripcion</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                    <div class="table-responsive table-dsg">
                        <table class="datatable table table-striped table-hover" id="tabla-cancelada">
                            <thead>
                                <tr>
                                    <th style="display: none;">Id</th>
                                    <th>Area</th>
                                    <th>División</th>
                                    <th>Descripcion</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="evaluada-tab-pane" role="tabpanel" aria-labelledby="evaluada-tab" tabindex="0">
                    <div class="table-responsive table-dsg">
                        <table class="datatable table table-striped table-hover" id="tabla-evaluada">
                            <thead>
                                <tr>
                                     <th style="display: none;">Id</th>
                                    <th>Area</th>
                                    <th>División</th>
                                    <th>Descripcion</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="comun-tab-pane" role="tabpanel" aria-labelledby="comun-tab" tabindex="0">
               

            </div>

        </div>
    </div>

</div>

<?php renderComponent('ModalCancelar') ?>
<?php renderComponent('ModalTerminar') ?>
<?php renderComponent('ModalGenerico') ?>
<?php renderComponent('ModalTareas') ?>
<?php renderComponent('ModalOrden') ?>
<?php renderComponent('ModalEvaluar') ?>
<?php renderComponent('ModalDetalles') ?>
<?php renderComponent('ModalOrdenes') ?>
<?php renderComponent('ModalReporteA') ?>



<script src="public/js/tareas.js"></script>
<script src="public/lib/chart.js"></script>
<script src="public/lib/jspdf.umd.min.js"></script>
<script src="public/lib/jspdf.plugin.autotable.min.js"></script>
<script src="public/lib/html2canvas.min.js"></script>


<script>

    async function generarPDF() {
    try {
        
        if (!window.jspdf || !window.html2canvas) {
            throw new Error("Las librerías necesarias no están cargadas");
        }
        
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'pt', 'a4');
        
       
        const modal = document.querySelector('#modal-estadistica');
        if (!modal) {
            throw new Error("El modal no está presente en el DOM");
        }
        
        // Mostrar el modal si está oculto
        if (window.getComputedStyle(modal).display === 'none') {
            $(modal).modal('show');
            await new Promise(resolve => setTimeout(resolve, 300));
        }
        
        const element = modal.querySelector('.contenidoMostrar');
                     
        
        if (!element) {
            throw new Error("No se encontró el contenido a exportar");
        }
        
        const canvas = await html2canvas(element, {
            scale: 1.5,
            logging: true,
            useCORS: true,
            scrollX: 0,
            scrollY: 0,
            backgroundColor: '#FFFFFF',
            ignoreElements: (el) => {
                return el.classList.contains('btn') || 
                       el.classList.contains('modal-footer');
            }
        });
        
        const imgData = canvas.toDataURL('image/png');
        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth() - 20;
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
        
        pdf.addImage(imgData, 'PNG', 10, 10, pdfWidth, pdfHeight);
        
        pdf.save('reporte_tareas_' + new Date().toLocaleDateString() + '.pdf');
        
    } catch (error) {
        console.error("Error detallado:", error);
        alert("No se pudo generar el PDF: " + error.message);
        
        if (error.stack) {
            console.error("Stack trace:", error.stack);
        }
    }
}
    async function generarEstadistica() {
        const tipoEstadistica = document.getElementById('tipoEstadistica').value;
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;
        const departamento = "1";

        if (!tipoEstadistica || !fechaInicio || !fechaFin) {
            alert('Por favor complete todos los campos obligatorios');
            return;
        }

        try {
            const datos = await obtenerDatosEstadisticos(tipoEstadistica, fechaInicio, fechaFin, departamento);
            console.log(tipoEstadistica);
            document.getElementById('tituloGrafico').textContent = obtenerTituloEstadistica(tipoEstadistica);

            document.getElementById('explicacionEstadistica').innerHTML = generarExplicacion(tipoEstadistica, datos);

            generarGrafico(tipoEstadistica, datos);

            generarTablaDetalle(tipoEstadistica, datos.detalle || []);

        } catch (error) {
            console.error('Error al generar estadística:', error);
            alert('Error al generar la estadística: ' + error.message);
        }
    }

async function obtenerDatosEstadisticos(tipo, fechaInicio, fechaFin, departamento = null) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'Tareas/ReporteA', // 🔁 Cambiar esta URL a la ruta real de tu backend
            method: 'POST',
            dataType: 'json',
            data: {
                tipo: tipo,
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin,
                departamento: departamento
            },
            success: function (response) {
                if (response && response.success) {
                    resolve(response.data);
                } else {
                    reject(new Error(response.message || 'Datos inválidos desde el servidor'));
                }
            },
            error: function (xhr, status, error) {
                reject(new Error('Error de conexión: ' + error));
            }
        });
    });
}



    async function cargarDepartamentos() {
        const select = document.getElementById('departamentoSeleccionado');

        try {
            const response = await $.ajax({
                url: 'Tareas/ReporteA?ajax=1',
                method: 'GET',
                dataType: 'json'
            });

            if (response.success) {
                const $select = $(select);
                $select.empty().append('<option value="">Todos los departamentos</option>');
                response.data.forEach(function (depto) {
                    const $option = $('<option>', {
                        value: depto.id,
                        text: depto.nombre
                    });
                    $select.append($option);
                });
            } else {
                console.error('Error al cargar los departamentos:', response.message);
            }
        } catch (error) {
            console.error('Error al cargar los departamentos:', error);
        }
    }

function obtenerTituloEstadistica(tipo) {
            const titulos = {
                'recurso_consumible': 'Recurso Consumible Más Utilizado',
                'mes_mas_tareas': 'Mes con Más Tareas Realizadas',
                'departamento_mas_tareas': 'Departamento con Más Tareas',
                'trabajador_mas_tareas': 'Persona con Más Tareas en el Departamento'
            };
            return titulos[tipo] || 'Estadística Seleccionada';
        }

        function generarExplicacion(tipo, datos) {
            switch (tipo) {
                case 'recurso_consumible':
                    return `<strong>Resultado:</strong> El recurso consumible más utilizado en el período seleccionado 
                    fue <strong>${datos.recurso}</strong> con un total de <strong>${datos.cantidad} ${datos.unidades}</strong> 
                    utilizadas en las tareas.`;
                case 'mes_mas_tareas':
                    return `<strong>Resultado:</strong> El mes con mayor cantidad de tareas realizadas fue 
                    <strong>${datos.mes}</strong> con un total de <strong>${datos.cantidad} tareas</strong>. 
                    La distribución por departamentos se muestra en el gráfico.`;
                case 'departamento_mas_tareas':
                    return `<strong>Resultado:</strong> El departamento con mayor cantidad de tareas realizadas fue 
                    <strong>${datos.departamento}</strong> con un total de <strong>${datos.cantidad} tareas</strong>. 
                    La distribución mensual se muestra en el gráfico.`;
                case 'trabajador_mas_tareas':
                    return `<strong>Resultado:</strong> La persona con mayor cantidad de tareas realizadas en el departamento 
                    de <strong>${datos.departamento}</strong> fue <strong>${datos.trabajador}</strong> con un total de 
                    <strong>${datos.cantidad} tareas</strong>.`;
                default:
                    return 'Explicación no disponible para esta estadística.';
            }
        }

        function generarGrafico(tipo, datos) {
            
            const ctx = document.getElementById('graficoEstadistica').getContext('2d');

            // Destruir el gráfico anterior si existe
            if (window.estadisticaChart) {
                window.estadisticaChart.destroy();
            }

            let config = {};

            switch (tipo) {
                case 'recurso_consumible':
                    config = {
                        type: 'bar',
                        data: {
                            labels: ['Recurso más usado'],
                            datasets: [{
                                label: datos.recurso,
                                data: [datos.cantidad],
                                backgroundColor: '#4CAF50'
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    };
                    break;
                case 'mes_mas_tareas':
                    config = {
                        type: 'pie',
                        data: {
                            labels: datos.detalle.map(item => item.departamento),
                            datasets: [{
                                data: datos.detalle.map(item => item.cantidad),
                                backgroundColor: ['#2196F3', '#4CAF50', '#FFC107', '#9C27B0']
                            }]
                        }
                    };
                    break;
                case 'departamento_mas_tareas':
                    config = {
                        type: 'line',
                        data: {
                            labels: datos.detalle.map(item => item.mes),
                            datasets: [{
                                label: 'Tareas realizadas',
                                data: datos.detalle.map(item => item.cantidad),
                                borderColor: '#3F51B5',
                                backgroundColor: 'rgba(63, 81, 181, 0.1)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    };
                    break;
                case 'trabajador_mas_tareas':
                    // En este caso podríamos mostrar las tareas por fecha
                    config = {
                        type: 'bar',
                        data: {
                            labels: datos.detalle.map(item => item.tarea),
                            datasets: [{
                                label: 'Tareas realizadas',
                                data: datos.detalle.map(() => 1), // Cada tarea cuenta como 1
                                backgroundColor: '#607D8B'
                            }]
                        }
                    };
                    break;
            }

            window.estadisticaChart = new Chart(ctx, config);
        }

        function generarTablaDetalle(tipo, detalle) {
            const tabla = document.getElementById('tablaDetalleEstadisticas');
            const thead = tabla.querySelector('thead');
            const tbody = tabla.querySelector('tbody');

            // Limpiar la tabla
            thead.innerHTML = '';
            tbody.innerHTML = '';

            // Crear encabezados según el tipo de estadística
            let headers = [];
            switch (tipo) {
                case 'recurso_consumible':
                    headers = ['Tarea', 'Cantidad Usada', 'Fecha'];
                    break;
                case 'mes_mas_tareas':
                    headers = ['Departamento', 'Cantidad de Tareas'];
                    break;
                case 'departamento_mas_tareas':
                    headers = ['Mes', 'Cantidad de Tareas'];
                    break;
                case 'trabajador_mas_tareas':
                    headers = ['Tarea', 'Fecha', 'Evaluación'];
                    break;
            }

            // Crear fila de encabezado
            const headerRow = document.createElement('tr');
            headers.forEach(header => {
                const th = document.createElement('th');
                th.textContent = header;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);

            // Llenar con datos
            detalle.forEach(item => {
                const row = document.createElement('tr');

                switch (tipo) {
                    case 'recurso_consumible':
                        row.innerHTML = `
                    <td>${item.tarea}</td>
                    <td>${item.cantidad}</td>
                    <td>${item.fecha}</td>
                `;
                        break;
                    case 'mes_mas_tareas':
                        row.innerHTML = `
                    <td>${item.departamento}</td>
                    <td>${item.cantidad}</td>
                `;
                        break;
                    case 'departamento_mas_tareas':
                        row.innerHTML = `
                    <td>${item.mes}</td>
                    <td>${item.cantidad}</td>
                `;
                        break;
                    case 'trabajador_mas_tareas':
                        row.innerHTML = `
                    <td>${item.tarea}</td>
                    <td>${item.fecha}</td>
                    <td>${item.evaluacion || 'Sin evaluar'}</td>
                `;
                        break;
                }

                tbody.appendChild(row);
            });
        }


    $(document).ready(function() {

        



$(document).on("show.bs.modal", "#modal-estadistica", function (e) {
    const modal = $(this);
    const button = $(e.relatedTarget);
    url = button.data("bs-url");
    const valorId = button.data("valor");

    console.log(valorId);
    console.log(url);

    if (typeof url === "undefined") {
        // Cargar el contenido del modal
    } else {
        $.ajax({
            url: url,
            method: "GET",
            success: function (data) {
                modal.find(".modal-content").html(data);
                cargarDatosEjemplo();

              
               

            },
            error: function () {
                console.error('Error al cargar contenido del modal.');
            }
        });
    }
});



    });

    function cargarDatosEjemplo() {
        document.getElementById('tipoEstadistica').addEventListener('change', function() {
            console.log("adasd2");
            const filtroDepartamento = document.getElementById('filtroDepartamento');
            if (this.value === 'trabajador_mas_tareas') {
                filtroDepartamento.style.display = 'block';
                cargarDepartamentos(); // Función para cargar los departamentos desde la BD
            } else {
                filtroDepartamento.style.display = 'none';
            }
        });


        

        // Función para cargar departamentos (simulada)

    }

    function removeMaterial(index) {
        let container = document.getElementById('materiales-container');
        container.children[index].remove();
    }
</script>