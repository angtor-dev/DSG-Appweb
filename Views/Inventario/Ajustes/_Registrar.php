<?php /** @var Articulo[] $articulos */ ?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Registrar nuevo ajuste de inventario
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-ajuste">
                <div class="row gy-3">
                    <div class="col-md-12">
                        <label for="idInventario" class="form-label">Artículo</label>
                        <select class="form-select select2" id="idInventario" name="idInventario" required>
                            <option value="">Seleccionar artículo</option>
                            <?php foreach ($articulos as $articulo): ?>
                                <option value="<?= $articulo->id ?>">
                                    <?= htmlspecialchars($articulo->getNombre()) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" step="any" class="form-control" id="cantidad" name="cantidad" required>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="fechaIncidente" class="form-label">Fecha del incidente</label>
                        <input type="date" class="form-control" id="fechaIncidente" name="fechaIncidente" required>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label">Motivo</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                <button type="submit" form="form-ajuste" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>