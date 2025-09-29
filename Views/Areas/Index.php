<?php /** @var Area[] $areas */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Áreas</h3>
                <span class="opacity-75 mb-2">Gestiona las diferentes áreas y zonas donde se realizan servicios</span>
            </div>
            <?php if (tienePermiso(modulo::AREAS, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Areas/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nueva Área
                    </button>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="card border-0">
        <div class="card-body p-4 d-flex flex-column gap-3">
            <?= ImprimirAcordeonesAnidados($areas, null, Modulo::AREAS) ?>
        </div>
    </div>
</div>

<?php renderComponent('ModalEliminar') ?>
<?php renderComponent('ModalGenerico') ?>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        tablaAreas = new DataTable('#tabla-areas', {
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

<?php // agregarScript("area.js") ?>
<?php // agregarScript("validaciones/area.js") ?>