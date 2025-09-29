<?php /** @var Division[] $departamentos */ ?>

<div class="modal-dialog modal-lg">
<div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Registrar Nueva División
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-departamento">
                <div class="row gy-3">
                    <div class="col-md-12">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="idDepartamento" class="form-label">Pertenece a:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-fw fa-building"></i></span>
                            <select class="form-select" name="idDepartamento" id="idDepartamento">
                                <option value=""><i>Ninguno</i></option>
                                <?php foreach ($departamentos as $departamento): ?>
                                    <option value="<?= $departamento->id ?>"><?= $departamento->getNombre() ?></option>
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
                <button type="submit" form="form-departamento" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>