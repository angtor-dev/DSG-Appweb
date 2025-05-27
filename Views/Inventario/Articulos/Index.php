<?php /** @var Articulo[] $articulos */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Artículos</h3>
                <span class="opacity-75 mb-2">Gestiona los articulos registrados en el sistema</span>
            </div>
            <?php if (tienePermiso(modulo::MEDIDAS, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Inventario/Articulos/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nuevo Artículo
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
                <table class="datatable table table-striped table-hover" id="tabla-articulos">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articulos as $articulo): ?>
                            <tr>
                                <td><?= $articulo->getNombre() ?></td>
                                <td><?= $articulo->getDescripcion() ?></td>
                                <td>
                                    <span class="badge rounded-pill category-badge" style="--color: #<?= $articulo->categoria->getColor() ?>;">
                                        <?= $articulo->categoria->getNombre() ?>
                                    </span>
                                </td>
                                <td><?= $articulo->getCantidad() ?></td>
                                <td>
                                    <div class="d-flex justify-content-evenly w-100 gap-3">
                                        <?php if (tienePermiso(Modulo::ARTICULOS, Permiso::ACTUALIZAR)): ?>
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Editar">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Articulos/Actualizar?id=<?= $articulo->id ?>">
                                                    <i class="fa-solid fa-fw fa-pen-to-square"></i>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (tienePermiso(Modulo::ARTICULOS, Permiso::ELIMINAR)): ?>
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-eliminar"
                                                    data-bs-modelo="el artículo" 
                                                    data-bs-nombre="<?= $articulo->getNombre() ?>"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Articulos/Eliminar?id=<?= $articulo->id ?>">
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
        tablaArticulos = new DataTable('#tabla-articulos', {
            pagingType: 'simple_numbers',
            language: {
                url: '<?= LOCAL_DIR ?>/public/lib/DataTables/datatables-spanish.json'
            },
            layout: {
                topStart: {
                    
                },
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


<?php // agregarScript("articulo.js") ?>
<?php // agregarScript("validaciones/articulo.js") ?>
