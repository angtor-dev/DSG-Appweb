<?php /** @var Division[] $departamentos */ ?>
<div class="modal-dialog modal-lg">
<div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Registrar nuevo Trabajador
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-trabajador" onsubmit="return false">
                <input type="hidden" id="modificar" value="">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="cedula" class="form-label">Cedula </label>
                        <input maxlength="8" required value="" type="text" class="form-control" id="cedula" name="cedula" data-span="invalid-span-cedula">
                        <div id="invalid-span-cedula" class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-6 m-0"></div>

                    <div class="col-md-4">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input required disabled class="form-control" type="text" id="nombre" name="nombre">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input required disabled class="form-control" type="text" id="apellido" name="apellido">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input maxlength="11" required disabled type="tel" class="form-control" id="telefono" name="telefono" data-span="invalid-span-telefono">
                        <div id="invalid-span-telefono" class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="departamento" class="form-label">División</label>
                        <select required disabled name="departamento" id="departamento" class="form-select">
                            <option value=""></option>
                            <?php foreach ($departamentos as $departamento): ?>
                                <option value="<?= $departamento->id ?>"><?= $departamento->getNombre() ?></option>
                            <?php endforeach ?>
                            
                        </select>
                        <div id="invalid-span-departamento" class="form-text invalid-feedback"></div>           
                    </div>
                    <div class="col-md-4">
                        <label for="cargo" class="form-label">Cargo</label>
                        <select required disabled name="cargo" id="cargo" class="form-select">
                            <option value=""></option>
                            <?= $cargosOptions  ?>
                        </select>
                        <div id="invalid-span-cargo" class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="turno" class="form-label">Turno</label>
                        <select required disabled name="turno" id="turno" class="form-select">
                            <option value=""></option>
                            <?= $turnosOptions ?>
                        </select>
                        <div id="invalid-span-turno" class="form-text invalid-feedback"></div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                        <input disabled required type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" data-span="invalid-span-fecha_ingreso">
                        <div id="invalid-span-fecha_ingreso" class="form-text invalid-feedback"></div>
                    </div>
                    
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                <button disabled id="btn-submit-registrar" type="submit" form="form-trabajador" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>