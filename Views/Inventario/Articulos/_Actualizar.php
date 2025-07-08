<?php /** @var Categoria[] $categorias */ ?>
<?php /** @var Medida[] $medidas */ ?>
<?php /** @var Articulo $articulo */ ?>

<div class="modal-dialog modal-lg">
<div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Registrar nuevo artículo
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-articulo">
                <input type="hidden" name="id" value="<?= $articulo->id ?>">
                <div class="row gy-3">
                    <div class="col-md-12">
                        <label for="nombre" class="form-label">Nombre del artículo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $articulo->getNombre() ?>">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label">Descripción (opcional)</label>
                        <textarea class="form-control" id="descripcion" name="descripcion"><?= $articulo->getDescripcion() ?></textarea>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="idCategoria" class="form-label">Categoría</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-fw fa-layer-group"></i></span>
                            <select class="form-select rounded-end" name="idCategoria" id="idCategoria">
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria->id ?>" <?= $articulo->idCategoria == $categoria->id ? "selected" : "" ?>><?= $categoria->getNombre() ?></option>
                                <?php endforeach ?>
                            </select>
                            <div class="form-text invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="idMedida" class="form-label">Medida</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-fw fa-ruler"></i></span>
                            <select class="form-select rounded-end" name="idMedida" id="idMedida">
                                <option value="0"><i>Seleccionar</i></option>
                                <?php foreach ($medidas as $medida): ?>
                                    <option value="<?= $medida->id ?>" <?= $articulo->idMedida == $medida->id ? "selected" : "" ?>><?= $medida->getUnidad() ?> (<?= $medida->getSubUnidad() ?>)</option>
                                <?php endforeach ?>
                            </select>
                            <div class="form-text invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-label">Propiedades</div>
                        <div class="d-flex gap-3">
                            <div class="p-2 border rounded-2">
                                <div class="form-check form-check-inline me-1">
                                    <input class="form-check-input" type="checkbox" id="esConsumible" name="esConsumible" value="true" <?= $articulo->getEsConsumible() ? "checked" : "" ?>/>
                                    <label class="form-check-label" for="esConsumible">Consumible</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                <button type="submit" form="form-articulo" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>