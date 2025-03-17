<?php /** @var Usuario $usuario */ ?>
<?php /** @var Rol[] $roles */ ?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Actualizar usuario
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-usuario">
                <input type="hidden" name="id" value="<?= $usuario->id ?>">
                <div class="row gy-3 pb-2">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input class="form-control" type="text" id="nombre" name="nombre" value="<?= $usuario->getNombre() ?>">
                        <div class="form-text"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input class="form-control" type="text" id="apellido" name="apellido" value="<?= $usuario->getApellido() ?>">
                        <div class="form-text"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="correo" class="form-label">Correo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-fw fa-at"></i></span>
                            <input type="email" class="form-control" id="correo" name="correo" value="<?= $usuario->getCorreo() ?>">
                        </div>
                        <div class="form-text"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="idRol" class="form-label">Rol</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-regular fa-fw fa-user-circle"></i></span>
                            <select class="form-select" name="idRol" id="idRol">
                                <option value=""></option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol->id ?>" <?= $rol->id == $usuario->idRol ? "selected" : "" ?>>
                                        <?= $rol->getNombre() ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-text"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                <button type="submit" form="form-usuario" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>