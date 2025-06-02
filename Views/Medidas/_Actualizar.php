<?php /** @var Medida $medida */ ?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Actualizar medida
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-medida">
                <input type="hidden" name="id" id="id" value="<?= $medida->id ?>">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="unidad" class="form-label">Unidad</label>
                        <input type="text" class="form-control" id="unidad" name="unidad"
                        placeholder="Ej. Kilogramos" value="<?= $medida->getUnidad() ?>">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="subUnidad" class="form-label">Sub-unidad</label>
                        <input type="text" class="form-control" id="subUnidad" name="subUnidad"
                        placeholder=" Ej. Kg" value="<?= $medida->getSubUnidad() ?>">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                <button type="submit" form="form-medida" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>