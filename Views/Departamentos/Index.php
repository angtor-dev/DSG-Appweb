<?php /** @var Departamento[] $departamentos */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Departamentos</h3>
                <span class="opacity-75 mb-2">Gestiona los departamentos de la Dirección de Servicios Generales</span>
            </div>
            <?php if (tienePermiso(modulo::DEPARTAMENTOS, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Departamentos/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nuevo Departamento
                    </button>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4 d-flex flex-column gap-3">
        <?= ImprimirAcordeonesAnidados($departamentos, null, Modulo::DEPARTAMENTOS) ?>
        </div>
    </div>
</div>

<?php renderComponent('ModalEliminar') ?>
<?php renderComponent('ModalGenerico') ?>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        tablaDepartamentos = new DataTable('#tabla-departamentos', {
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

<?php // agregarScript("departamento.js") ?>
<?php // agregarScript("validaciones/departamento.js") ?>