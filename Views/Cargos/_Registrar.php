<div class="modal-dialog modal-lg">
<div class="modal-content">
        <div class="modal-header panel-header text-white">
            <h5 class="modal-title my-2">
                Registrar Nuevo Cargo
            </h5>
        </div>
        <div class="modal-body">
            <form action=" <?= LOCAL_DIR ?>/Cargos/registrar" method="post" id="form-cargo" onsubmit="return false">
                <div class="row">
                    <div class="col">
                        <label for="form-nombre" class="form-label">Nombre</label>
                        <input required maxlength="80" type="text" class="form-control" id="form-nombre" name="form-nombre-cargo" data-formText="form-text-form-nombre">
                        <div id="form-text-form-nombre" class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col"></div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="form-nivel" class="form-label">Nivel </label>
                        <input required type="number" step="1" min="1" class="form-control" id="form-nivel" name="form-nivel-cargo" data-formText="form-text-form-nivel">
                        <div id="form-text-form-nivel" class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col" style="color: var(--font-color-light);">
                        * se reservan los niveles de la siguiente forma <br>
                        - <b>nivel 1</b> para <b>Director</b><br>
                        - <b>nivel 2</b> para <b>Coordinador</b><br>
                        - <b>nivel 3</b> para <b>Supervisor</b><br>
                        A partir del <b>nivel 4</b> se puede utilizar para la jerarquía de los cargos de los trabajadores
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                <button type="submit" form="form-cargo" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>