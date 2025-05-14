<?php /** @var Area $area */ ?>
<?php /** @var Area[] $areas */ ?>

<div class="modal-dialog modal-lg">
<div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Actualizar área
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-area">
                <input type="hidden" name="id" value="<?= $area->id ?>">
                <div class="row gy-3">
                    <div class="col-md-12">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $area->getNombre() ?>">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="idArea" class="form-label">Pertenece a:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-regular fa-fw fa-map"></i></span>
                            <select class="form-select" name="idArea" id="idArea">
                                <option value=""><i>Ninguno</i></option>
                                <?php foreach ($areas as $areaPadre): ?>
                                    <?php /** @var Area $areaPadre */ ?>
                                    <option value="<?= $areaPadre->id ?>" <?= $area->idArea == $areaPadre->id ? "selected" : "" ?>><?= $areaPadre->getNombre() ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                <button type="submit" form="form-area" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>