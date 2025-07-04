<?php /** @var Articulo[] $articulos */ ?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Registrar nueva nota de entrega
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-nota-entrega">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="fechaEntrada" class="form-label">Fecha de entrega</label>
                        <input type="date" class="form-control" id="fechaEntrada" name="fechaEntrada" required>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="numeroDocumento" class="form-label">Número de documento</label>
                        <input type="text" class="form-control" id="numeroDocumento" name="numeroDocumento" required>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Artículos a entregar</label>
                        <div class="input-group mb-2">
                            <select class="form-select select2" id="articulo-selector">
                                <option value="">Buscar artículo...</option>
                                <?php foreach ($articulos as $articulo): ?>
                                    <option value="<?= $articulo->id ?>">
                                        <?= htmlspecialchars($articulo->getNombre()) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <input type="number" min="1" class="form-control" id="articulo-cantidad" placeholder="Cantidad">
                            <button type="button" class="btn btn-outline-primary" id="agregar-articulo">Agregar</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tabla-articulos">
                                <thead>
                                    <tr>
                                        <th>Artículo</th>
                                        <th>Cantidad</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Artículos agregados aparecerán aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                <button type="submit" form="form-nota-entrega" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>