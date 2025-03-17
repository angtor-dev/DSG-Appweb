<?php /** @var Rol[] $roles */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner py-5">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Roles y permisos</h3>
                <span class="opacity-75 mb-2">Gestiona los roles y permisos de los usuarios</span>
            </div>
            <div>
                <a href="<?= LOCAL_DIR ?>/Seguridad/Roles/Registrar" style="padding: .65rem 1.4rem;"
                    class="btn btn-outline-light rounded-pill">
                    <i class="fa-solid fa-plus me-2"></i>
                    Nuevo Rol
                </a>
            </div>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <div class="table-responsive table-dsg">
                <table class="datatable table table-striped table-hover" id="tabla-roles">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Rol</th>
                            <th>Descripcion</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $rol): ?>
                            <tr>
                                <td><?= $rol->id ?></td>
                                <td><?= $rol->getNombre() ?></td>
                                <td><?= $rol->getDescripcion() ?></td>
                                <td>
                                    <?php if ($rol->getNombre() != "Superusuario"): ?>
                                        <div class="d-flex justify-content-evenly w-100 gap-3">
                                            <?php if (tienePermiso('roles', 'actualizar')): ?>
                                                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Permisos">
                                                    <div data-bs-toggle="modal" data-bs-target="#modal-permisos"
                                                        data-bs-id="<?= $rol->id ?>">
                                                        <i class="fa-solid fa-fw fa-key"></i>
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                            <?php if (tienePermiso('roles', 'actualizar')): ?>
                                                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Editar">
                                                    <a href="<?= LOCAL_DIR ?>/Seguridad/Roles/Actualizar?id=<?= $rol->id ?>">
                                                        <i class="fa-solid fa-fw fa-pen-to-square"></i>
                                                    </a>
                                                </div>
                                            <?php endif ?>
                                            <?php if (tienePermiso('roles', 'eliminar')): ?>
                                                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                                                    <div data-bs-toggle="modal" data-bs-target="#modal-eliminar"
                                                        data-bs-modelo="el rol"
                                                        data-bs-nombre="<?= $rol->getNombre() ?>"
                                                        data-bs-url="<?= LOCAL_DIR ?>/Seguridad/Roles/Eliminar?id=<?= $rol->id ?>">
                                                        <i class="fa-solid fa-fw fa-trash-can"></i>
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php renderComponent("ModalEliminar") ?>
<?php renderComponent("ModalPermisos") ?>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        tablaRoles = new DataTable('#tabla-roles', {
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