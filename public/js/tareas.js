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
                           

                        <?php if (tienePermiso(Modulo::TAREAS, Permiso::ELIMINAR)): ?>
                             <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Cancelar" 
                                onclick="cancelarTarea(${row.id}, this)">
                                <i class="fa-solid fa-ban"></i>
                            </div>
                        <?php endif; ?>
                        
                        
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

        // Recargar tablas al cambiar pestaña
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('aria-controls');
            const tablas = {
                'home-tab-pane': tablaActivas,
                'profile-tab-pane': tablaVencidas,
                'contact-tab-pane': tablaComunes,
                'evaluada-tab-pane': tablaEvaluada
            };
            
            if (tablas[target]) {
                tablas[target].ajax.reload();
            }
        });

           

         $('#modal-generico').on('shown.bs.modal', function() {
            console.log("Modal se muestra");
        
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