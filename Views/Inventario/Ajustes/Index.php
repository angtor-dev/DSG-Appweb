<?php /** @var Ajuste[] $ajustes */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Correcciones</h3>
                <span class="opacity-75 mb-2">Consulta y realiza correcciones de inventario</span>
            </div>
            <?php if (tienePermiso(modulo::AJUSTES, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Inventario/Ajustes/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nueva Corrección
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
                topStart: {},
                bottom1Start: {
                    pageLength: true
                },
                bottom1End: {
                    buttons: [
                        {
                            // Elemento de texto personalizado
                            text: 'Exportar: ',
                            // Puedes añadir una clase para estilizarlo si es necesario
                            className: 'dt-export-button',
                            // Esto evita que se comporte como un botón real
                            action: function ( e, dt, node, config ) {
                                // No hacer nada al hacer clic
                                e.preventDefault();
                            }
                        },
                        'excel', 'pdf', 'print'
                    ]
                }
            }
        })
    })
</script>


<?php // agregarScript("ajuste.js") ?>
<?php agregarScript("validaciones/ajuste.js") ?>
