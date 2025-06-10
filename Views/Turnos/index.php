<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Cargos</h3>
                <span class="opacity-75 mb-2">Gestiona los Turnos de los trabajadores</span>
            </div>
            <?php if (tienePermiso(Modulo::TURNOS, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Turnos/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nuevo Turno
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
                <table class="datatable table table-striped table-hover" id="tabla-turnos">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Turno</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Días</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="d-none">
                            <td colspan="4">
                                <div style="min-height: 100px; position: relative;">
                                    <div class="loader">
    
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php renderComponent('modalEliminarPromise') ?>
<?php renderComponent('ModalGenerico') ?>

<?php agregarScript("turnos.js") ?>