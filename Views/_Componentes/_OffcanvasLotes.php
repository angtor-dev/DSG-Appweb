<?php /** @var Insumo $insumo */ ?>
<?php /** @var array<Lote> $lotes */ ?>
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Lotes de <?= $insumo->getDescripcion() ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>f. Ingreso</th>
                        <th>f. Vencimiento</th>
                        <th>cant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lotes as $lote): ?>
                        <tr>
                            <td><?= $lote->id ?></td>
                            <td><?= $lote->getFechaIngreso() ?></td>
                            <td><?= $lote->getFechaVencimiento() ?></td>
                            <td><?= $lote->getCantidad() ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>