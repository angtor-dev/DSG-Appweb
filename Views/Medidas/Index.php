<?php /** @var Medida[] $medidas */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Medidas</h3>
                <span class="opacity-75 mb-2">Gestiona las diferentes medidas y zonas donde se realizan servicios</span>
            </div>
            <?php if (tienePermiso(modulo::MEDIDAS, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Medidas/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nueva Medida
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
                <table class="datatable table table-striped table-hover" id="tabla-medidas">
                    <thead>
                        <tr>
                            <th>Unidad</th>
                            <th>Siglas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medidas as $medida): ?>
                            <tr>
                                <td><?= $medida->getUnidad() ?></td>
                                <td><?= $medida->getSubUnidad() ?></td>
                                <td>
                                    <div class="d-flex justify-content-evenly w-100 gap-3">
                                        <?php if (tienePermiso(Modulo::MEDIDAS, Permiso::ACTUALIZAR)): ?>
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Editar">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Medidas/Actualizar?id=<?= $medida->id ?>">
                                                    <i class="fa-solid fa-fw fa-pen-to-square"></i>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (tienePermiso(Modulo::MEDIDAS, Permiso::ELIMINAR)): ?>
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-eliminar"
                                                    data-bs-modelo="a la medida" 
                                                    data-bs-nombre="<?= $medida->getUnidad() ?>"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Medidas/Eliminar?id=<?= $medida->id ?>">
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

<?php renderComponent('ModalEliminar') ?>
<?php renderComponent('ModalGenerico') ?>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        tablaMedidas = new DataTable('#tabla-medidas', {
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


<?php // agregarScript("medida.js") ?>
<?php // agregarScript("validaciones/medida.js") ?>
