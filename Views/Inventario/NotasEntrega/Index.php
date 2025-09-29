<?php /** @var Entrada[] $entradas */ ?>
<?php /** @var Articulo[] $articulos */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Notas de entrega</h3>
                <span class="opacity-75 mb-2">Consulta las entradas de inventario</span>
            </div>
            <?php if (tienePermiso(modulo::NOTASENTREGA, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Inventario/NotasEntrega/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nueva Nota
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
                <table class="datatable table table-striped table-hover" id="tabla-entradas">
                    <thead>
                        <tr>
                            <th>Nro. Doc.</th>
                            <th>Responsable</th>
                            <th>F. Ingreso</th>
                            <th>Observaciones</th>
                            <th>Items</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entradas as $entrada): ?>
                            <tr>
                                <td><?= $entrada->getNumeroDocumento() ?></td>
                                <td><?= $entrada->usuario?->getNombreCompleto() ?? $entrada->idUsuario ?></td>
                                <td><?= $entrada->getFechaEntradaLegible() ?></td>
                                <td><?= $entrada->getObservaciones() ?></td>
                                <td><?= count($entrada->getDetalles()) ?></td>
                                <td>
                                    <div class="d-flex justify-content-evenly w-100 gap-3">
                                        
                                        <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Ver detalles">
                                            <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                data-bs-url="<?= LOCAL_DIR ?>/Inventario/NotasEntrega/Detalles?id=<?= $entrada->id ?>">
                                                <i class="fa-solid fa-fw fa-eye"></i>
                                            </div>
                                        </div>
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
        tablaArticulos = new DataTable('#tabla-entradas', {
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

<script>
    // Mapeo de id => nombre para mostrar el nombre del artículo seleccionado
    const articulosMap = {
        <?php foreach ($articulos as $articulo): ?>
            "<?= $articulo->id ?>": "<?= htmlspecialchars($articulo->getNombre(), ENT_QUOTES) ?>",
        <?php endforeach ?>
    };
</script>
<?php agregarScript("notasEntrega.js") ?>
<?php // agregarScript("validaciones/entrada.js") ?>