<?php /** @var Rol $rol */ ?>
<?php /** @var array<Permiso> $permisos */ ?>
<?php /** @var array<Modulo> $modulos */ ?>
<?php $usuarioSesion ??= $_SESSION['usuario'] ?>

<div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
        <div class="modal-header bg-light">
            <h5 class="modal-title">
                Permisos del rol <em><?= $rol->getNombre() ?></em>
            </h5>
        </div>
        <div class="modal-body">
            <form action="<?= LOCAL_DIR ?>/Seguridad/Roles/ActualizarPermisos" method="post" id="form-permisos">
                <input type="hidden" name="idRol" value="<?= $rol->id ?>">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <td class="text-end" style="width: 10px;">Modulo</td>
                                <th class="text-center" style="width: 175px;">Consultar</th>
                                <th class="text-center" style="width: 175px;">Registrar</th>
                                <th class="text-center" style="width: 175px;">Actualizar</th>
                                <th class="text-center" style="width: 175px;">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modulos as $modulo): ?>
                                <tr>
                                    <td class="text-end text-nowrap"><b><?= $modulo->getNombre() == "roles" ? "roles y permisos" : $modulo->getNombre() ?></b></td>
                                    <td class="text-center">
                                        <label class="switch">
                                            <input type="checkbox" value="true"
                                            <?php if (($modulo->getNombre() == "permisos"
                                                || $modulo->getNombre() == "roles")
                                                && $usuarioSesion->rol->getNombre() == $rol->getNombre()): ?>
                                                disabled
                                            <?php endif ?>
                                                name="<?= $modulo->getNombre() ?>[consultar]"
                                                <?= $rol->tienePermiso($modulo->getNombre(), 'consultar') ? "checked" : "" ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <label class="switch">
                                            <input type="checkbox" value="true"
                                            <?php if (($modulo->getNombre() == "permisos"
                                                || $modulo->getNombre() == "roles")
                                                && $usuarioSesion->rol->getNombre() == $rol->getNombre()): ?>
                                                disabled
                                            <?php endif ?>
                                                name="<?= $modulo->getNombre() ?>[registrar]"
                                                <?= $rol->tienePermiso($modulo->getNombre(), 'registrar') ? "checked" : "" ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <label class="switch">
                                            <input type="checkbox" value="true"
                                            <?php if (($modulo->getNombre() == "permisos"
                                                || $modulo->getNombre() == "roles")
                                                && $usuarioSesion->rol->getNombre() == $rol->getNombre()): ?>
                                                disabled
                                            <?php endif ?>
                                                name="<?= $modulo->getNombre() ?>[actualizar]"
                                                <?= $rol->tienePermiso($modulo->getNombre(), 'actualizar') ? "checked" : "" ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <label class="switch">
                                            <input type="checkbox" value="true"
                                            <?php if (($modulo->getNombre() == "permisos"
                                                || $modulo->getNombre() == "roles")
                                                && $usuarioSesion->rol->getNombre() == $rol->getNombre()): ?>
                                                disabled
                                            <?php endif ?>
                                                name="<?= $modulo->getNombre() ?>[eliminar]"
                                                <?= $rol->tienePermiso($modulo->getNombre(), 'eliminar') ? "checked" : "" ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                </tr>
                                <?php if (($modulo->getNombre() == "permisos" || $modulo->getNombre() == "roles")
                                    && $usuarioSesion->rol->getNombre() == $rol->getNombre()): ?>
                                    <?php if ($rol->tienePermiso($modulo->getNombre(), 'consultar')): ?>
                                        <input type="hidden" name="<?= $modulo->getNombre() ?>[consultar]" value="true">
                                    <?php endif ?>
                                    <?php if ($rol->tienePermiso($modulo->getNombre(), 'registrar')): ?>
                                        <input type="hidden" name="<?= $modulo->getNombre() ?>[registrar]" value="true">
                                    <?php endif ?>
                                    <?php if ($rol->tienePermiso($modulo->getNombre(), 'actualizar')): ?>
                                        <input type="hidden" name="<?= $modulo->getNombre() ?>[actualizar]" value="true">
                                    <?php endif ?>
                                    <?php if ($rol->tienePermiso($modulo->getNombre(), 'eliminar')): ?>
                                        <input type="hidden" name="<?= $modulo->getNombre() ?>[eliminar]" value="true">
                                    <?php endif ?>
                                <?php endif ?>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        <div class="modal-footer d-flex justify-content-end gap-2 bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" form="form-permisos" class="btn btn-primary">Guardar</button>
        </div>
    </div>
</div>