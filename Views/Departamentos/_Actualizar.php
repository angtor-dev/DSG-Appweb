<?php /** @var Departamento $departamento */ ?>
<?php /** @var Departamento[] $departamentos */ ?>

<div class="modal-dialog modal-lg">
<div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Actualizar área
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-departamento">
                <input type="hidden" name="id" value="<?= $departamento->id ?>">
                <div class="row gy-3">
                    <div class="col-md-12">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $departamento->getNombre() ?>">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="idDepartamento" class="form-label">Pertenece a:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-regular fa-fw fa-map"></i></span>
                            <select class="form-select" name="idDepartamento" id="idDepartamento">
                                <option value=""><i>Ninguno</i></option>
                                <?php foreach ($departamentos as $departamentoPadre): ?>
                                    <?php /** @var Departamento $departamentoPadre */ ?>
                                    <option value="<?= $departamentoPadre->id ?>" <?= $departamento->idDepartamento == $departamentoPadre->id ? "selected" : "" ?>><?= $departamentoPadre->getNombre() ?></option>
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
                <button type="submit" form="form-departamento" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>