<?php /** @var Tareas $tarea */ ?>

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
            <div class= "col">
                <div class="card border">
                    <div class="card-body justify-content-center align-items-center d-flex flex-column">
                        <h4 class="card-title">2</h4>
                        <p class="card-text">Tareas activas</p>
                    </div>
                </div>
            </div>
            <div class= "col">
                <div class="card border">
                    <div class="card-body justify-content-center align-items-center d-flex flex-column">
                        <h4 class="card-title">7</h4>
                        <p class="card-text">Tareas Vencidas</p>
                    </div>
                </div>
            </div>
            <div class= "col">
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

        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                <div class="table-responsive table-dsg">
                    <table class="datatable table table-striped table-hover" id="tabla-trabajadores"> Tabla 1
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Area</th>
                                <th>Departamento</th>
                                <th>Descripcion</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tareas as $tarea): ?>

                                <tr>
                                <td><?= $tarea->id ?></td>
                                <td><?= $tarea->area->getNombre() ?></td>  <!-- Nombre del área -->
                                <td><?= $tarea->departamento->getNombre() ?></td>  <!-- Nombre del departamento -->
                                <td><?= htmlspecialchars($tarea->descripcion) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($tarea->fechaCreacion)) ?></td>

                                    <td>
                                        <div class="d-flex justify-content-evenly w-100 gap-3">
                                             <!-- Botón Ver Detalles -->
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Ver Detalles">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Tareas/Detalle?id=<?= $tarea->id ?>">
                                                    <i class="fa-solid fa-fw fa-eye"></i> <!-- Icono para ver -->
                                                </div>
                                            </div>
                                            <?php if (tienePermiso(Modulo::TAREAS, Permiso::ACTUALIZAR)): ?>
                                                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Editar">
                                                    <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                        data-bs-url="<?= LOCAL_DIR ?>/Trabajadores/Actualizar?id=">
                                                        <i class="fa-solid fa-fw fa-pen-to-square"></i>
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                            <!-- Botón Evaluar Tarea -->
                                           
                                            <?php if (tienePermiso(Modulo::TAREAS, Permiso::ELIMINAR)): ?>
                                                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                                                    <div data-bs-toggle="modal" data-bs-target="#modal-eliminar"

                                                        data-bs-url="<?= LOCAL_DIR ?>/Trabajadores/Eliminar?id=\">
                                                        <i class="fa-solid fa-fw fa-trash-can"></i>
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                <div class="table-responsive table-dsg">
                    <table class="datatable table table-striped table-hover" id="tabla-trabajadores"> Tabla 2
                        <thead>
                            <tr>
                            <th>Id</th>
                                <th>Area</th>
                                <th>Departamento</th>
                                <th>Descripcion</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tareas as $tarea): ?>
                                <tr>
                                <td><?= $tarea->id ?></td>
                                <td><?= $tarea->area->getNombre() ?></td>  <!-- Nombre del área -->
                                <td><?= $tarea->departamento->getNombre() ?></td>  <!-- Nombre del departamento -->
                                <td><?= htmlspecialchars($tarea->descripcion) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($tarea->fechaCreacion)) ?></td>
                                    <td>
                                        <div class="d-flex justify-content-evenly w-100 gap-3">

                                            <!-- Botón Ver Detalles -->
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Ver Detalles">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Tareas/Detalle?id=<?= $tarea->id ?>">
                                                    <i class="fa-solid fa-fw fa-eye"></i> <!-- Icono para ver -->
                                                </div>
                                            </div>
                                            
                                            <!-- Botón Evaluar Tarea -->
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Evaluar">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Tareas/Evaluar?id=<?= $tarea->id ?>">
                                                    <i class="fa-solid fa-fw fa-clipboard-check"></i> <!-- Icono para evaluar -->
                                                </div>
                                            </div>
                                            <?php if (tienePermiso(Modulo::TAREAS, Permiso::ELIMINAR)): ?>
                                                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                                                    <div data-bs-toggle="modal" data-bs-target="#modal-eliminar"

                                                        data-bs-url="<?= LOCAL_DIR ?>/Trabajadores/Eliminar?id=\">
                                                        <i class="fa-solid fa-fw fa-trash-can"></i>
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                <div class="table-responsive table-dsg">
                    <table class="datatable table table-striped table-hover" id="tabla-trabajadores"> Tabla 3
                        <thead>
                            <tr>
                            <th>Id</th>
                                <th>Area</th>
                                <th>Departamento</th>
                                <th>Descripcion</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tareas as $tarea): ?>
                                <tr>
                                <td><?= $tarea->id ?></td>
                                <td><?= $tarea->area->getNombre() ?></td>  <!-- Nombre del área -->
                                <td><?= $tarea->departamento->getNombre() ?></td>  <!-- Nombre del departamento -->
                                <td><?= htmlspecialchars($tarea->descripcion) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($tarea->fechaCreacion)) ?></td>
                                    <td>
                                        <div class="d-flex justify-content-evenly w-100 gap-3">

                                            <!-- Botón Ver Detalles -->
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Ver Detalles">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                        data-bs-url="<?= LOCAL_DIR ?>/Tareas/Detalle?id=<?= $tarea->id ?>">
                                                        <i class="fa-solid fa-fw fa-eye"></i> <!-- Icono para ver -->
                                                    </div>
                                            </div>
                                            <?php if (tienePermiso(Modulo::TAREAS, Permiso::ACTUALIZAR)): ?>
                                                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Editar">
                                                    <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                        data-bs-url="<?= LOCAL_DIR ?>/Trabajadores/Actualizar?id=\">
                                                        <i class="fa-solid fa-fw fa-pen-to-square"></i>
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                            
                                            <?php if (tienePermiso(Modulo::TAREAS, Permiso::ELIMINAR)): ?>
                                                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                                                    <div data-bs-toggle="modal" data-bs-target="#modal-eliminar"

                                                        data-bs-url="<?= LOCAL_DIR ?>/Trabajadores/Eliminar?id=\">
                                                        <i class="fa-solid fa-fw fa-trash-can"></i>
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        </div>
    </div>
</div>

<?php renderComponent('ModalEliminar') ?>
<?php renderComponent('ModalGenerico') ?>

<script> /*
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

<?php // agregarScript("trabajador.js") ?>
<?php //agregarScript("validaciones/trabajador.js") ?>
