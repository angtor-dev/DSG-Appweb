<div class="modal-dialog modal-lg">
    <div class="modal-content">
            <div class="modal-header bg-white">
                <h5 class="modal-title my-2">
                    Modificar Trabajador
                </h5>
            </div>
            <div class="modal-body">

            <?php if($mensaje!=''): ?>
            <div class="alert alert-danger fade show" role="alert">
                <strong>¡Error!</strong> <?php echo $mensaje; ?>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-between gap-3">
                    <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cerrar</button>
                </div>
            </div>

            <?php else: ?>
            
                
                <form method="post" id="form-trabajador">
                    <input type="hidden" id="modificar" value="<?php echo $Trabajador->id ?>">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="cedula" class="form-label">Cedula </label>
                            <input required value="<?php echo $Trabajador->getCedula() ?>" type="text" class="form-control" id="cedula" name="cedula" data-span="invalid-span-cedula">
                            <div id="invalid-span-cedula" class="form-text invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 m-0"></div>

                        <div class="col-md-4">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input required value="<?php echo $Trabajador->getNombre() ?>" class="form-control"  type="text" id="nombre" name="nombre">
                            <div class="form-text invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input required value="<?php echo $Trabajador->getApellido() ?>" class="form-control" type="text" id="apellido" name="apellido">
                            <div class="form-text invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input required value="<?php echo $Trabajador->getTelefono() ?>" type="tel" class="form-control" id="telefono" name="telefono" data-span="invalid-span-telefono">
                            <div id="invalid-span-telefono" class="form-text invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="cargo" class="form-label">Cargo</label>
                            <select required name="cargo" id="cargo" class="form-select">
                                <option value=""></option>
                                <?php foreach (Cargo::cases() as $cargo): ?>
                                    <option <?php if ($Trabajador->getCargo()->value == $cargo->value) echo "selected" ?> value="<?= $cargo->value ?>"><?= $cargo->name ?></option>
                                <?php endforeach ?>
                            </select>
                            <div id="invalid-span-cargo" class="form-text invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="turno" class="form-label">Turno</label>
                            <select required name="turno" id="turno" class="form-select">
                                <option value=""></option>
                                <?php foreach (Turno::cases() as $turno): ?>
                                    <option <?php if ($Trabajador->getTurno()->value == $turno->value) echo "selected" ?> value="<?= $turno->value ?>"><?= ucfirst($turno->value) ?></option>
                                <?php endforeach ?>
                            </select>
                            <div id="invalid-span-turno" class="form-text invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="departamento" class="form-label">Departamento</label>
                            <select required name="departamento" id="departamento" class="form-select">
                                <option value=""></option>
                                <?php foreach ($departamentos as $departamento): ?>
                                    <option <?php if ($Trabajador->getIdDepartamento() == $departamento->id) echo "selected" ?> value="<?= $departamento->id ?>"><?= $departamento->getNombre() ?></option>
                                <?php endforeach ?>
                                
                            </select>
                            <div id="invalid-span-departamento" class="form-text invalid-feedback"></div>           
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                            <input required value="<?php echo $Trabajador->getFechaIngreso() ?>" type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" data-span="invalid-span-fecha_ingreso">
                            <div id="invalid-span-fecha_ingreso" class="form-text invalid-feedback"></div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between gap-3">
                            <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                            <button id="btn-submit-registrar" type="submit" form="form-trabajador" class="btn btn-primary">Modificar</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
            </div>
            
    </div>
</div>


