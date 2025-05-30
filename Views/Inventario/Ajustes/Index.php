<?php /** @var Ajuste[] $ajustes */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Ajustes</h3>
                <span class="opacity-75 mb-2">Consulta y realiza ajustes de inventario</span>
            </div>
            <?php if (tienePermiso(modulo::AJUSTES, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Ajustes/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nuevo Ajuste
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
                <table class="datatable table table-striped table-hover" id="tabla-ajustes">
                    <thead>
                        <tr>
                            <th>Artículo</th>
                            <th>Cant.</th>
                            <th>Motivo</th>
                            <th>Fecha Incidente</th>
                            <th>Registrado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ajustes as $ajuste): ?>
                            <tr>
                                <td><?= $ajuste->articulo->getNombre() ?></td>
                                <td><?= $ajuste->getCantidad()." ".$ajuste->articulo->medida->getSubUnidad() ?></td>
                                <td><?= $ajuste->getDescripcion() ?></td>
                                <td><?= $ajuste->getFechaIncidenteLegible() ?></td>
                                <td><?= $ajuste->getFechaCreacionLegible() ?></td>
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
        tablaAjustes = new DataTable('#tabla-ajustes', {
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


<?php // agregarScript("ajuste.js") ?>
<?php // agregarScript("validaciones/ajuste.js") ?>
