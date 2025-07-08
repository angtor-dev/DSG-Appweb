<?php /** @var Entrada $entrada */ ?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Detalles de la nota de entrega
            </h5>
        </div>
        <div class="modal-body">
            <div class="row gy-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Fecha de entrega:</label>
                    <div><?= htmlspecialchars($entrada->getFechaEntradaLegible()) ?></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Número de documento:</label>
                    <div><?= htmlspecialchars($entrada->getNumeroDocumento()) ?></div>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Responsable:</label>
                    <div><?= isset($entrada->usuario) ? htmlspecialchars($entrada->usuario->getNombre()) : '' ?></div>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Observaciones:</label>
                    <div><?= nl2br(htmlspecialchars($entrada->getObservaciones())) ?></div>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Artículos entregados:</label>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Artículo</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($entrada->getDetalles() as $detalle): ?>
                                    <tr>
                                        <td>
                                            <?= isset($detalle->articulo) ? htmlspecialchars($detalle->articulo->getNombre()) : 'Artículo #'.$detalle->idArticulo ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($detalle->getCantidad()) ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cerrar</button>
        </div>
    </div>
</div>