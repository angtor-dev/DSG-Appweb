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
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Tareas/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nueva tarea
                    </button>
                </div>

                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Tareas/Orden">
                        <i class="fa-solid fa-plus me-2"></i>
                        Mostrar Orden
                    </button>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class=" row card-body p-4">
            <div class="col">
                <div class="card border">
                    <div class="card-body justify-content-center align-items-center d-flex flex-column">
                        <h4 class="card-title">2</h4>
                        <p class="card-text">Tareas activas</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border">
                    <div class="card-body justify-content-center align-items-center d-flex flex-column">
                        <h4 class="card-title">7</h4>
                        <p class="card-text">Tareas Vencidas</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border">
                    <div class="card-body justify-content-center align-items-center d-flex flex-column">
                        <h4 class="card-title">2</h4>
                        <p class="card-text">Tareas Comunes</p>
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
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Vencidas</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Comunes</button>
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
                                    <th>Id</th>
                                    <th>Area</th>
                                    <th>Departamento</th>
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
                                    <th>Id</th>
                                    <th>Area</th>
                                    <th>Departamento</th>
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
                        <table class="datatable table table-striped table-hover" id="tabla-comunes"> 
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Area</th>
                                    <th>Departamento</th>
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
                                    <th>Id</th>
                                    <th>Area</th>
                                    <th>Departamento</th>
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

            </div>

        </div>
    </div>
</div>

<?php renderComponent('ModalCancelar') ?>
<?php renderComponent('ModalGenerico') ?>


 <!-- Scripts necesarios -->
 <script>
    $(document).ready(function() {

       cargarDatos();

    function cargarDatos() {
        $.get('<?= LOCAL_DIR ?>/Tareas?ajax=true', function(data) {
            // Destruir DataTables si ya existen
            if ($.fn.DataTable.isDataTable('#tabla-activas')) {
                $('.datatable').DataTable().destroy();
            }
            
            // Poblar tablas
            poblarTabla('#tabla-activas', data.activo);
            poblarTabla('#tabla-vencidas', data.vencida);
            poblarTabla('#tabla-comunes', data.comun);
            poblarTabla('#tabla-evaluada', data.evaluada);
            
            // Inicializar DataTables
            $('.datatable').DataTable({
                pagingType: 'simple_numbers',
                language: {
                    url: '<?= LOCAL_DIR ?>/public/lib/DataTables/datatables-spanish.json'
                }
            });
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Error al cargar datos:", textStatus, errorThrown);
        });
    }

    function poblarTabla(selector, datos) {
        const $tabla = $(selector);
        const tbody = $tabla.find('tbody');
        tbody.empty();
        
        datos.forEach(tarea => {
            const fila = `
                <tr>
                    <td>${tarea.id}</td>
                    <td>${tarea.area}</td>
                    <td>${tarea.departamento}</td>
                    <td>${tarea.descripcion}</td>
                    <td>${tarea.fecha}</td>
                    <td>${tarea.estado}</td>
                    <td>
                        <div class="d-flex justify-content-evenly w-100 gap-3">
                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Ver Detalles">
                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                    data-bs-url="<?= LOCAL_DIR ?>/Tareas/Detalle?id=${tarea.id}">
                                    <i class="fa-solid fa-fw fa-eye"></i>
                                </div>
                            </div>
                            <?php if (tienePermiso(Modulo::TAREAS, Permiso::ACTUALIZAR)): ?>
                                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Editar">
                                    <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                        data-bs-url="<?= LOCAL_DIR ?>/Tarea/Actualizar?id=${tarea.id}">
                                        <i class="fa-solid fa-fw fa-pen-to-square"></i>
                                    </div>
                                </div>
                            <?php endif ?>
                            <?php if (tienePermiso(Modulo::TAREAS, Permiso::ELIMINAR)): ?>
                                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Cancelar">
                                    <div data-bs-toggle="modal" data-bs-target="#modal-cancelar"
                                        data-bs-url="<?= LOCAL_DIR ?>/Tarea/Cancelar?id=${tarea.id}">
                                        <i class="fa-solid fa-ban"></i>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(fila);
        });
    }

    window.recargarDatos = function() {
        cargarDatos();
    };

    function inicializarDataTables() {
        $('.datatable').DataTable({
            pagingType: 'simple_numbers',
            language: {
                url: '<?= LOCAL_DIR ?>/public/lib/DataTables/datatables-spanish.json'
            }
        });
    }

    // Función para recargar todos los datos
    window.recargarTablas = function() {
        $.get('<?= LOCAL_DIR ?>/Tareas?ajax=true', function(data) {
            poblarTabla('#tabla-activas', data.activo);
            poblarTabla('#tabla-vencidas', data.vencida);
            poblarTabla('#tabla-comunes', data.comun);
            poblarTabla('#tabla-evaluada', data.evaluada);
            
            // Re-inicializar DataTables
            $('.datatable').DataTable().destroy();
            inicializarDataTables();
        });
    };

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
                                url: '<?= LOCAL_DIR ?>/Tareas/Registrar', // Ajusta la ruta según tu controlador
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
                                  recargarDatos();
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
</script>

<script>

    
    /*
    document.addEventListener('DOMContentLoaded', e => {
        tablaTrabajadores = new DataTable('#tabla-trabajadores', {
            pagingType: 'simple_numbers',
            language: {
                url: '<?= LOCAL_DIR ?>/public/lib/DataTables/datatables-spanish.json'
            },
            layout: {
                topStart: {
                    buttons: ['excel', 'pdf', 'print']
                },
                bottom1Start: {
                    pageLength: true
                }
            }
        })
    })
    */
</script>

<?php // agregarScript("trabajador.js") 
?>
<?php //agregarScript("validaciones/trabajador.js") 
?>