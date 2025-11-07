<?php /** @var Categoria[] $categorias */ ?>

<style>
    .color-sample {
        height: 24px;
        width: 48px;
        border-radius: 8px;
        background-color: var(--color, black);
        border: 1px solid #ccc;
    }
</style>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Categorías</h3>
                <span class="opacity-75 mb-2">Gestiona las categorías para los artículos</span>
            </div>
            <?php if (tienePermiso(modulo::CATEGORIAS, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Categorias/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nueva Categoría
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
                <table class="datatable table table-striped table-hover" id="tabla-categorias">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Color</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><?= $categoria->getNombre() ?></td>
                                <td><?= $categoria->getDescripcion() ?></td>
                                <td>
                                    <div class="color-sample" style="--color: #<?= $categoria->getColor() ?>"></div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-evenly w-100 gap-3">
                                        <?php if (tienePermiso(Modulo::CATEGORIAS, Permiso::ACTUALIZAR)): ?>
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Editar">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Categorias/Actualizar?id=<?= $categoria->id ?>">
                                                    <i class="fa-solid fa-fw fa-pen-to-square"></i>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (tienePermiso(Modulo::CATEGORIAS, Permiso::ELIMINAR)): ?>
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-eliminar"
                                                    data-bs-modelo="la categoria" 
                                                    data-bs-nombre="<?= $categoria->getNombre() ?>"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Categorias/Eliminar?id=<?= $categoria->id ?>">
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
        tablaCategorias = new DataTable('#tabla-categorias', {
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


<?php // agregarScript("categoria.js") ?>
<?php // agregarScript("validaciones/categoria.js") ?>
