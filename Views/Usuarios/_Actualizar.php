
<?php /** @var Usuario $usuario */ ?>
<?php /** @var Rol[] $roles */ ?>

<div class="modal-dialog modal-lg">
<div class="modal-content">
        <div class="modal-header bg-white panel-header text-white">
            <h5 class="modal-title my-2">
                Modificar Usuario
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-usuario">
                <input type="hidden" id="id-user" name="id" value="<?= $usuario->id ?>">
                <div class="row gy-3">
                    <div class="col-md-6">
                    <div class="col-md-6">
                        <label for="cedula" class="form-label">Cedula </label>
                        <input value="<?= $usuario->getCedula() ?>" maxlength="8" required autocomplete="off" pattern="^[0-9]{7,8}$" value="" type="text" class="form-control" id="cedula" name="cedula" data-formText="invalid-span-cedula">
                        <div id="invalid-span-cedula" class="form-text invalid-feedback"></div>
                    </div>
                    </div>
                </div>
                <div class="row gy-3">
                <div class="col-md-6 ">
                        <!-- <div class="form-info-field" data-info="Nombre" id="nombre"></div> -->

                        <label for="nombre" class="form-label">Nombre</label>
                        <input value="<?= $usuario->getNombre() ?>" required maxlength="50" type="text" class="form-control" id="nombre" name="nombre" data-formText="form-text-nombre">
                        <div id="form-text-nombre" class="form-text invalid-feedback"></div>
                         
                    </div>
                    <div class="col-md-6">
                        <!-- <div class="form-info-field" data-info="Cordinacion/Direccion" id="departamento"></div> -->
                        <label for="apellido" class="form-label">Apellido</label>
                        <input value="<?= $usuario->getApellido() ?>" required type="text" class="form-control" id="apellido" name="apellido" data-formText="form-text-apellido">
                        <div id="form-text-apellido" class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="correo" class="form-label">Correo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-fw fa-at"></i></span>
                            <input required value="<?= $usuario->getCorreo() ?>" type="email" class="form-control" id="correo" name="correo" data-formText="form-text-correo">
                        </div>
                        <div class="form-text invalid-feedback" id="form-text-correo"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="idRol" class="form-label">Rol</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-regular fa-fw fa-user-circle"></i></span>
                            <select class="form-select" name="idRol" id="idRol" data-formText="form-text-rol">
                                <option value=""></option>
                                <?php foreach ($roles as $rol): ?>
                                    <option <?php if($rol->id == $usuario->idRol) echo "selected"; ?> value="<?= $rol->id ?>"><?= $rol->getNombre() ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-text invalid-feedback" id="form-text-rol"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="clave" class="form-label">Contraseña</label>
                        <div class="position-relative">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-fw fa-lock"></i></span>
                                <input class="form-control" type="password" id="clave" name="clave" data-formText="form-text-clave" placeholder="Sin Modificar">
                            </div>
                            <div class="toggle-password" onclick="alternarClave(event)">
                                <i class="fa-solid fa-eye"></i>
                                <i class="fa-solid fa-eye-slash"></i>
                            </div>
                        </div>
                        <div class="form-text invalid-feedback" id="form-text-clave"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">.</label>
                        <button type="button" class="btn btn-light w-100 border" onclick="generarClave()" id="generarClave-btn">
                            <i class="fa-solid fa-rotate me-1"></i>
                            Generar Contraseña
                        </button>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                <button type="submit" form="form-usuario" class="btn btn-primary" id="submit-modal">Modificar</button>
            </div>
        </div>
    </div>
</div>