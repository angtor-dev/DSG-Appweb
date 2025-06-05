<?php /** @var Trabajador[] $trabajadores */ ?>

<div class="panel-header">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Trabajadores</h3>
                <span class="opacity-75 mb-2">Gestiona a los trabajadores de la Dirección de Servicios Generales</span>
            </div>
            <?php if (tienePermiso(Modulo::TRABAJADORES, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Trabajadores/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nuevo Trabajador
                    </button>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <div class="table-responsive table-dsg">
                <table class="datatable table table-striped table-hover" id="tabla-trabajadores">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nombre y apellido</th>
                            <th>Cedula</th>
                            <th>Teléfono</th>
                            <th>Turno</th>
                            <th>Cargo</th>
                            <th>Departamento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trabajadores as $trabajador): ?>
                            <tr>
                                <td><?= $trabajador->id ?></td>
                                <td><?= $trabajador->getNombreCompleto() ?></td>
                                <td><?= $trabajador->getCedula() ?></td>
                                <td><?= $trabajador->getTelefono() ?></td>
                                <td><?= ucfirst($trabajador->getTurno()) ?></td>
                                <td><?= ucfirst($trabajador->getCargo()) ?></td>
                                <td><?= $trabajador->departamento->getNombre() ?></td>
                                <td>
                                    <div class="d-flex justify-content-evenly w-100 gap-3">
                                        <?php if (tienePermiso(Modulo::TRABAJADORES, Permiso::ACTUALIZAR)): ?>
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Editar">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Trabajadores/Actualizar?cedula=<?= $trabajador->getCedula() ?>" >
                                                    <i class="fa-solid fa-fw fa-pen-to-square"></i>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (tienePermiso(Modulo::TRABAJADORES, Permiso::ELIMINAR)): ?>
                                            <div class="accion pointer accion-eliminar" data-bs-toggle="tooltip" data-bs-title="Eliminar" data-trabajador ="<?= $trabajador->getCedula() ?>" data-nombre="<?= $trabajador->getNombreCompleto() ?>">
                                                <div>
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

<?php renderComponent('modalEliminarPromise') ?>
<?php renderComponent('ModalGenerico') ?>

<script>
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
</script>

<?php // agregarScript("trabajador.js") ?>
<?php agregarScript("validaciones/trabajador.js") ?>