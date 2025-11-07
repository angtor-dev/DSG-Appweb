<?php /** @var Bitacora[] $bitacoras */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Bitácora</h3>
                <span class="opacity-75 mb-2">Consulta las acciones realizas en el sistema</span>
            </div>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <div class="table-responsive table-dsg">
                <table class="datatable table table-striped table-hover" id="tabla-bitacoras">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Registro</th>
                            <th>Módulo</th>
                            <th>Acción</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bitacoras as $biracora): ?>
                            <!-- Ignorar temporalmente usuarios inexistenes porque no se como desaparecieron -->
                            <?php if (!$biracora->getUsuario_correo()) continue; ?>
                            <tr>
                                <td><?= ($biracora->getUsuario_correo()) ? $biracora->getUsuario_correo() : "USER_DELETED" ?></td>
                                <td><?= $biracora->getRegistro() ?></td>
                                <td><?= $biracora->getModulo() ?></td>
                                <td><?= $biracora->getAccion() ?></td>
                                <td data-sort="<?= $biracora->getTimestamp() ?>"><?= $biracora->getFecha() ?></td>
                                <td> <?php echo $biracora->getTimestamp() ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        tablaBitacora = new DataTable('#tabla-bitacoras', {
            ordering: true,
            lengthChange: false,
            pageLength: 50,
            pagingType: 'simple_numbers',
            language: {
                url: '<?= LOCAL_DIR ?>/public/lib/DataTables/datatables-spanish.json'
            },
            order: [[4, 'desc']],
            columnDefs: [
                {"targets": [4], "orderData": [5] },
                {"targets": [5], "visible": false}
            ]
        })
    })
</script>