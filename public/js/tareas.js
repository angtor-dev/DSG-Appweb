  function cancelarTarea(id, element) {
    if (!confirm('¿Estás seguro de cancelar esta tarea?')) return;

    const icon = $(element).find('i');
    icon.removeClass('fa-ban').addClass('fa-spinner fa-spin');

    $.ajax({
        url: 'Tareas/Cancelar', // Asegúrate que esta ruta coincida con tu enrutamiento
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('.datatable').DataTable().ajax.reload();
                toastr.success(response.message);
                
                
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            toastr.error('Error al contactar el servidor');
        },
        complete: function() {
            icon.removeClass('fa-spinner fa-spin').addClass('fa-ban');
        }
    });
}
    $(document).ready(function() {
        // Configuración base común
        const commonConfig = {
            serverSide: false,
            searching: true,
            ordering: true,
            paging: true,
            language: {
                url: 'public/lib/DataTables/datatables-spanish.json'
            },
            columns: [
                { data: 'id' },
                { data: 'area' },
                { data: 'departamento' },
                { data: 'descripcion' },
                { data: 'fecha' },
                { data: 'estado' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return renderButtons(row);
                    }
                }
            ]
        };

        // Función para renderizar botones según el estado
        function renderButtons(row) {
            let buttons = '<div class="d-flex gap-2">';
            
            
            
            // Botones específicos por estado
            switch(row.estado.toLowerCase()) {
                case 'activo':
                    buttons += `
                        <?php if (tienePermiso(Modulo::TAREAS, Permiso::ACTUALIZAR)): ?>
                    
                           <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Editar">
                                    <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                        data-bs-url="Tarea/Actualizar?id=${row.id}">
                                        <i class="fa-solid fa-fw fa-pen-to-square"></i>
                                    </div>
                                </div>
                        <?php endif; ?>

                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Ver Detalles">
                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                    data-bs-url="Tareas/Detalle?id=${row.id}">
                                    <i class="fa-solid fa-fw fa-eye"></i>
                                </div>
                            </div>
                           

                        
                             <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Cancelar" 
                                onclick="cancelarTarea(${row.id}, this)">
                                <i class="fa-solid fa-ban"></i>
                            </div>
                        
                        
                        
                    `;
                    break;
                    
                case 'vencida':
                    buttons += `
                         <?php if (tienePermiso(Modulo::TAREAS, Permiso::ACTUALIZAR)): ?>
                            <button class="btn btn-sm btn-outline-success" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modal-generico"
                                data-bs-url="Tareas/Evaluar?id=${row.id}">
                                <i class="fas fa-check"></i> Evaluar
                            </button>
                        <?php endif; ?>
                    `;
                    break;
                    
                case 'cancelado':
                    buttons += `
                        <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Ver Detalles">
                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                    data-bs-url="Tareas/Detalle?id=${row.id}">
                                    <i class="fa-solid fa-fw fa-eye"></i>
                                </div>
                            </div>
                    `;
                    break;
                    
                case 'evaluada':
                    buttons += `
                        <?php if (tienePermiso(Modulo::TAREAS, Permiso::CONSULTAR)): ?>
                            <button class="btn btn-sm btn-outline-secondary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modal-generico"
                                data-bs-url="Tareas/Detalle/${row.id}">
                                <i class="fas fa-eye"></i> Detalle
                            </button>
                        <?php endif; ?>
                    `;
                    break;

                    
                case 'comun':
                    buttons += `
                        <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Configurar">
                            <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                data-bs-url="Tareas/Configuracion?id=${row.id}">
                                <i class="fa-solid fa-fw fa-gear"></i>
                            </div>
                        </div>
                         <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Eliminar" 
                                onclick="eliminarTarea(${row.id}, this)">
                                <i class="fa-solid fa-trash"></i>
                            </div>
                        
                    `;
                    break;
                    
            }
            
            buttons += '</div>';
            return buttons;
        }

        // Inicialización de las tablas
        const tablaActivas = $('#tabla-activas').DataTable({
            ...commonConfig,
            ajax: {
                url: 'Tareas?ajax=1',
                dataSrc: 'activo'
            }
        });

        const tablaVencidas = $('#tabla-vencidas').DataTable({
            ...commonConfig,
            ajax: {
                url: 'Tareas?ajax=1',
                dataSrc: 'vencida'
            }
        });

        console.log(`Estás en ${window.location.pathname}`);
        

        const tablaComunes = $('#tabla-cancelada').DataTable({
            ...commonConfig,
            ajax: {
                url: 'Tareas?ajax=1',
                dataSrc: 'cancelado'
            }
        });

        const tablaEvaluada = $('#tabla-evaluada').DataTable({
            ...commonConfig,
            ajax: {
                url: 'Tareas?ajax=1',
                dataSrc: 'evaluada'
            }
        });

        const tablaComun = $('#tabla-comun').DataTable({
            ...commonConfig,
            ajax: {
                url: 'Tareas?ajax=1',
                dataSrc: 'comun'
            }
        });

        // Recargar tablas al cambiar pestaña
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('aria-controls');
            const tablas = {
                'home-tab-pane': tablaActivas,
                'profile-tab-pane': tablaVencidas,
                'contact-tab-pane': tablaComunes,
                'evaluada-tab-pane': tablaEvaluada,
                'comun-tab-pane': tablaComun
            };
            
            if (tablas[target]) {
                tablas[target].ajax.reload();
            }
        });

           

         $('#modal-generico').on('shown.bs.modal', function() {
            console.log("Modal se muestra");

        //------------------------------- PERIODICIDAD ---------------------------------------    
             $('#periodicidad').change(function() {
        const periodicidad = $(this).val();
        
        // Ocultar todos los contenedores primero
        $('#dias-semana-container, #dia-mes-container').hide();
        
        // Mostrar los contenedores relevantes
        if (periodicidad === 'semanal') {
            $('#dias-semana-container').show();
        } else if (['mensual', 'trimestral', 'anual'].includes(periodicidad)) {
            $('#dia-mes-container').show();
        }
    });
    
    // Cargar datos existentes si estamos editando
    function cargarConfiguracionExistente() {
        // Aquí iría una llamada AJAX para cargar la configuración existente
        // de la tarea con ID <?= $_GET['id'] ?? '' ?>
        // Por ahora es solo un esqueleto
        /*
        if (<?= !empty($_GET['id']) ? 'true' : 'false' ?>) {
            $.get('<?= LOCAL_DIR ?>/Tareas/ObtenerConfiguracion?id=<?= $_GET['id'] ?? '' ?>', function(data) {
                // Llenar los campos con los datos recibidos
                $('#tarea-activa').prop('checked', data.activa);
                $('#periodicidad').val(data.periodicidad).trigger('change');
                // ... resto de campos
            });
        }
        */
    }
    
    cargarConfiguracionExistente();
    
    // Validación del formulario
    $('#form-config-tarea').submit(function(e) {
        e.preventDefault();
        
        // Validación básica
        if (!$('#periodicidad').val()) {
            $('#periodicidad').addClass('is-invalid');
            return false;
        }
        
        // Enviar el formulario
        $.post($(this).attr('action'), $(this).serialize(), function(response) {
            if (response.success) {
                // Cerrar el modal y recargar la tabla
                $('#modal-generico').modal('hide');
                // Aquí podrías mostrar un mensaje de éxito o recargar la tabla
                alert('Configuración guardada correctamente');
                // location.reload(); // O recargar solo la tabla de tareas comunes
            } else {
                alert('Error: ' + response.message);
            }
        }).fail(function() {
            alert('Error al guardar la configuración');
        });
    });

    //---------------------------------------------------- FIN PERIODICIDAD -------------------------------------


    //------------------------------------------------------- MATERIALES ----------------------------------------
     // Variable para almacenar los materiales seleccionados
    let materialesSeleccionados = [];
    console.log("Estoy en tareas");
    // Inicializar DataTable para materiales disponibles
    const tablaMateriales = $('#tabla-materiales').DataTable({
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        ajax: {
            url: 'Tareas/Materiales',
            type: 'GET',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'categoria' },
            { data: 'unidad' },
            { data: 'disponible' },
            { 
                data: null,
                render: function(data, type, row) {
                    return `<input type="number" class="form-control form-control-sm cantidad-material" 
                            min="0.1" max="${row.disponible}" step="${row.unidad === 'Pieza' ? '1' : '0.1'}" value="1">`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `<button class="btn btn-sm btn-primary agregar-material" data-id="${row.id}">
                                <i class="fa-solid fa-plus"></i>
                            </button>`;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        responsive: true
    });

    // Filtrar materiales
    $('#buscar-material').on('keyup', function() {
        tablaMateriales.search(this.value).draw();
    });

    $('#categoria').on('change', function() {
        if (this.value === '') {
            tablaMateriales.columns(2).search('').draw();
        } else {
            tablaMateriales.columns(2).search(this.value).draw();
        }
    });

    // Agregar material a la selección
    $('#tabla-materiales tbody').on('click', '.agregar-material', function() {
        const rowData = tablaMateriales.row($(this).closest('tr')).data();
        const cantidad = parseFloat($(this).closest('tr').find('.cantidad-material').val());
        
        if (isNaN(cantidad) || cantidad <= 0) {
            alert('Ingrese una cantidad válida');
            return;
        }
        
        if (cantidad > rowData.disponible) {
            alert(`No hay suficiente stock. Disponible: ${rowData.disponible} ${rowData.unidad}`);
            return;
        }
        
        const materialExistente = materialesSeleccionados.find(m => m.id == rowData.id);
        
        if (materialExistente) {
            materialExistente.cantidad += cantidad;
        } else {
            materialesSeleccionados.push({
                id: rowData.id,
                nombre: rowData.nombre,
                cantidad: cantidad,
                unidad: rowData.unidad
            });
        }
        
        actualizarTablaSeleccionados();
    });

    // Quitar material de la selección
    $('#tabla-seleccionados').on('click', '.quitar-material', function() {
        const id = $(this).data('id');
        materialesSeleccionados = materialesSeleccionados.filter(m => m.id != id);
        actualizarTablaSeleccionados();
    });

    // Función para actualizar la tabla de seleccionados
    function actualizarTablaSeleccionados() {
        const tbody = $('#tabla-seleccionados tbody');
        tbody.empty();
        
        if (materialesSeleccionados.length === 0) {
            tbody.append('<tr><td colspan="4" class="text-center text-muted py-3">No hay materiales seleccionados</td></tr>');
            return;
        }
        
        materialesSeleccionados.forEach(material => {
            const row = `
                <tr data-id="${material.id}">
                    <td>${material.nombre}</td>
                    <td>${material.cantidad}</td>
                    <td>${material.unidad}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-danger quitar-material" data-id="${material.id}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
        
        // Actualizar campo oculto del formulario con los materiales seleccionados
        $('#materiales-seleccionados').val(JSON.stringify(materialesSeleccionados));
    }

    // Inicializar tabla de seleccionados vacía
    actualizarTablaSeleccionados();
    
    // Agregar campo oculto al formulario para enviar los materiales
    $('#form-tarea').append('<input type="hidden" name="materiales" id="materiales-seleccionados" value="">');

    //--------------------------------------------------------- FIN MATERIALES --------------------------------------------//
    
    
    //--------------------------------------------- INICIO EVALUACIÓN --------------------------------------------//

    // Simulación de datos - en producción esto vendría de una llamada AJAX
    function cargarDatosTarea() {
        // Datos de ejemplo
        const tarea = {
            nombre: "Reparación de tubería principal",
            descripcion: "Cambio de tubería rota en área de producción",
            departamento: "Plomería",
            fecha: "15/05/2023",
            personal: "Juan Pérez, María García",
            estado: "en_evaluacion",
            evaluaciones: {
                supervisor: null,
                director: null
            },
            materiales: [
                { nombre: "Tornillos 3/8\"", asignado: 50, utilizado: 45, devuelto: 5, estado: "buen_estado" },
                { nombre: "Cable eléctrico 14 AWG", asignado: 10, utilizado: 8.5, devuelto: 1.5, estado: "danado" }
            ]
        };
        
        // Llenar datos de la tarea
        $('#tarea-nombre').text(tarea.nombre);
        $('#tarea-descripcion').text(tarea.descripcion);
        $('#tarea-departamento').text(tarea.departamento);
        $('#tarea-fecha').text(tarea.fecha);
        $('#tarea-personal').text(tarea.personal);
        
        // Actualizar barra de progreso
        actualizarProgreso();
    }
    
    // Función para actualizar la barra de progreso
    function actualizarProgreso() {
        let progreso = 0;
        let mensaje = "";
        
        // Lógica para determinar el progreso
        if ($('#seccion-supervisor').find('.badge').text() === "Completado") {
            progreso = 50;
            mensaje = "Falta evaluación del director";
        } else if ($('#seccion-director').find('.badge').text() === "Completado") {
            progreso = 100;
            mensaje = "Evaluación completa";
        } else {
            mensaje = "Falta evaluación del supervisor";
        }
        
        $('#progreso-evaluacion').css('width', progreso + '%').text(progreso + '% completado');
        $('#estado-evaluacion').html('<small class="text-muted">' + mensaje + '</small>');
        
        // Cambiar color según progreso
        if (progreso < 50) {
            $('#progreso-evaluacion').removeClass('bg-success bg-warning').addClass('bg-danger');
        } else if (progreso < 100) {
            $('#progreso-evaluacion').removeClass('bg-success bg-danger').addClass('bg-warning');
        } else {
            $('#progreso-evaluacion').removeClass('bg-warning bg-danger').addClass('bg-success');
        }
    }
    
    // Habilitar/deshabilitar sección de director según aprobación del supervisor
    $('#aprobacion-supervisor').change(function() {
        if ($(this).is(':checked')) {
            $('#seccion-director .card-body').html(`
                <div class="mb-3">
                    <label class="form-label"><strong>Ponderación:</strong></label>
                    <select class="form-select" id="ponderacion-director">
                        <option value="" disabled selected>Seleccione una ponderación</option>
                        <option value="buenobueno">Bueno-Bueno</option>
                        <option value="buenomedio">Bueno-Medio</option>
                        <option value="buenomalo">Bueno-Malo</option>
                        <option value="mediomedio">Medio-Medio</option>
                        <option value="mediomalo">Medio-Malo</option>
                        <option value="malomalo">Malo-Malo</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="comentarios-director" class="form-label"><strong>Comentarios:</strong></label>
                    <textarea class="form-control" id="comentarios-director" rows="3" 
                              placeholder="Describa su evaluación de la tarea"></textarea>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="aprobacion-director">
                    <label class="form-check-label" for="aprobacion-director">
                        Aprobar finalización definitiva de tarea
                    </label>
                </div>
            `);
            $('#seccion-director .badge').removeClass('bg-secondary').addClass('bg-info').text('Pendiente');
        } else {
            $('#seccion-director .card-body').html(`
                <div class="alert alert-info">
                    Esta sección se habilitará después de la aprobación del supervisor.
                </div>
            `);
            $('#seccion-director .badge').removeClass('bg-info').addClass('bg-secondary').text('No disponible');
        }
        actualizarProgreso();
    });
    
    // Guardar evaluación
    $('#btn-guardar-evaluacion').click(function() {
        if (!$('#confirmacion-evaluacion').is(':checked')) {
            alert('Debe confirmar que la información es correcta');
            return;
        }
        
        // Validar evaluación del supervisor si está pendiente
        if ($('#seccion-supervisor .badge').text() === "Pendiente") {
            if (!$('#ponderacion-supervisor').val()) {
                alert('Seleccione una ponderación para la evaluación del supervisor');
                return;
            }
            
            if (!$('#aprobacion-supervisor').is(':checked')) {
                if (!confirm('La tarea no será aprobada. ¿Desea continuar?')) {
                    return;
                }
            }
        }
        
        // Validar evaluación del director si está habilitada
        if ($('#seccion-director .badge').text() === "Pendiente") {
            if (!$('#ponderacion-director').val()) {
                alert('Seleccione una ponderación para la evaluación del director');
                return;
            }
            
            if (!$('#aprobacion-director').is(':checked')) {
                if (!confirm('La tarea no será aprobada definitivamente. ¿Desea continuar?')) {
                    return;
                }
            }
        }
        
        // Aquí iría la lógica para guardar la evaluación
        alert('Evaluación guardada correctamente (simulación)');
        $('#modal-generico').modal('hide');
    });
    
    // Cargar datos al abrir el modal
    cargarDatosTarea();

    //--------------------------------------------------------- FIN EVALUACIÓN --------------------------------------------//
           $(this).find('.select2-multiple').each(function() {
                $(this).select2({
                    width: '100%',
                    placeholder: $(this).data('placeholder') || 'Seleccione opciones',
                    allowClear: true,
                    dropdownParent: $(this).closest('.modal-content') // Importante para que funcione en modales
                });
            });

            // Botones Siguiente - versión mejorada
            $(this).find('.siguiente').off('click').on('click', function() {
                
                const nextTab = $(this).data('next');
                const targetTabButton = $(this).closest('.modal-content').find(`#tabs-tarea .nav-link[href="#${nextTab}"]`);
                if (targetTabButton.length) {
                    targetTabButton.tab('show'); // Usar el método tab() de Bootstrap
                } else {
                    console.error(`Botón de pestaña destino con href="#${nextTab}" no encontrado en el modal.`);
                }
            });

            // Botones Anterior - versión mejorada
            $(this).find('.anterior').off('click').on('click', function() {
                const prevTab = $(this).data('prev');
                const targetTabButton = $(this).closest('.modal-content').find(`#tabs-tarea .nav-link[href="#${prevTab}"]`);
                if (targetTabButton.length) {
                    targetTabButton.tab('show'); // Usar el método tab() de Bootstrap
                } else {
                    console.error(`Botón de pestaña destino con href="#${prevTab}" no encontrado en el modal.`);
                }
            });

             // Añadir listener de submit para el formulario dentro del modal
             const formTarea = $(this).find('#form-tarea');
             if (formTarea.length) {
                       // Manejar el envío del formulario
                        $('#form-tarea').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            
                            // Obtener los datos del formulario
                            const formData = new FormData(this);
                            
                            // Obtener los selects múltiples
                            const personal = $('#personal').select2('data').map(item => item.id);
                            const materiales = $('#materiales').select2('data').map(item => item.id);
                            
                            // Agregar los arrays al formData
                            formData.delete('personal[]');
                            formData.delete('materiales[]');
                            personal.forEach(id => formData.append('personal[]', id));
                            materiales.forEach(id => formData.append('materiales[]', id));
                            
                            // Mostrar loader o feedback al usuario
                            const submitBtn = $(this).find('button[type="submit"]');
                            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');
                            console.log('Personal seleccionado:', $('#personal').select2('data').map(item => item.id));
                            console.log('Materiales seleccionados:', $('#materiales').select2('data').map(item => item.id));
                            // Enviar por AJAX
                            $.ajax({
                                url: 'Tareas/Registrar', // Ajusta la ruta según tu controlador
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    // Cerrar el modal si el envío fue exitoso
                                    $('#modal-generico').modal('hide');
                                    console.log("Tarea registrada correctamente");
                                    mostrarExito("Tarea registrada correctamente")
                                    // Recargar la tabla
                                    tablaActivas.ajax.reload();
                                    // Recargar datos si es necesario
                                    // location.reload(); o actualizar tabla con AJAX
                                },
                                error: function(xhr, status, error) {
                                    // Mostrar mensaje de error
                                    console.error("Error al registrar la tarea:", error);
                                    console.error(xhr.responseText);
                                    mostrarError('Error al registrar la tarea: ' + xhr.responseText);
                                    
                                    // Reactivar el botón
                                    submitBtn.prop('disabled', false).html('<i class="fa-solid fa-check me-2"></i> Guardar Tarea');
                                }
                            });
                        });
                     
                  
             } else {
                 console.warn("Formulario con ID 'form-tarea' no encontrado en el modal después de mostrarse.");
             }
        });

       
    });    