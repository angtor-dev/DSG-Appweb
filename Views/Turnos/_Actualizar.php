<?php
/** @var $turnoObj Turno */
?>
<div class="modal-dialog modal-md">
<div class="modal-content">
        <div class="modal-header panel-header text-white">
            <h5 class="modal-title my-2">
                Registrar Nuevo Cargo
            </h5>
        </div>
        <div class="modal-body">
            <form action="<?= LOCAL_DIR ?>/Turnos/registrar" method="post" id="form-turno" onsubmit="return false">
                <input type="hidden" id="form-id" name="id" value="<?= $turnoObj->id ?>">
                <div class="row">
                    <div class="col-12">
                        <label for="form-nombre" class="form-label">Nombre del Turno</label>
                        <input value="<?= $turnoObj->get_nombre() ?>" required maxlength="50" type="text" class="form-control" id="form-nombre" name="form-nombre" data-formText="form-text-form-nombre">
                        <div id="form-text-form-nombre" class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="form-horaIn" class="form-label">Hora de Entrada</label>
                        <input value="<?= $turnoObj->get_horario_entrada() ?>" type="time" class="form-control" id="form-horaIn" name="horario_entrada" data-formText="form-text-form-horaIn">
                        <div id="form-text-form-horaIn" class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="form-horaOut" class="form-label">Hora de Salida</label>
                        <input value="<?= $turnoObj->get_horario_salida() ?>" type="time" class="form-control" id="form-horaOut" name="horario_salida" data-formText="form-text-form-horaOut">
                        <div id="form-text-form-horaOut" class="form-text invalid-feedback"></div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <table class="ms-auto me-auto table table-hover">
                        <tr>
                            <td><label class="pointer d-block" for="form-lunes">Lunes</label></td>
                            <td><label class="switch"><input <?= $turnoObj->get_lunes() == "1"? "checked" :"" ?>  type="checkbox" value="1" name="lunes" id="form-lunes"><span class="slider round"></span></label> </td>
                        </tr>
                        <tr>
                            <td><label class="pointer d-block" for="form-martes">Martes</label></td>
                            <td><label class="switch"><input <?= $turnoObj->get_martes() == "1"? "checked" :"" ?>  type="checkbox" value="1" name="martes" id="form-martes"><span class="slider round"></span></label> </td>
                        </tr>
                        <tr>
                            <td><label class="pointer d-block" for="form-miercoles">Miércoles</label></td>
                            <td><label class="switch"><input <?= $turnoObj->get_miercoles() == "1"? "checked" :"" ?>  type="checkbox" value="1" name="miercoles" id="form-miercoles"><span class="slider round"></span></label> </td>
                        </tr>
                        <tr>
                            <td><label class="pointer d-block" for="form-jueves">Jueves</label></td>
                            <td><label class="switch"><input <?= $turnoObj->get_jueves() == "1"? "checked" :"" ?>  type="checkbox" value="1" name="jueves" id="form-jueves"><span class="slider round"></span></label> </td>
                        </tr>
                        <tr>
                            <td><label class="pointer d-block" for="form-viernes">Viernes</label></td>
                            <td><label class="switch"><input <?= $turnoObj->get_viernes() == "1"? "checked" :"" ?>  type="checkbox" value="1" name="viernes" id="form-viernes"><span class="slider round"></span></label> </td>
                        </tr>
                        <tr>
                            <td><label class="pointer d-block" for="form-sabado">Sábado</label></td>
                            <td><label class="switch"><input <?= $turnoObj->get_sabado() == "1"? "checked" :"" ?>  type="checkbox" value="1" name="sabado" id="form-sabado"><span class="slider round"></span></label> </td>
                        </tr>
                        <tr>
                            <td><label class="pointer d-block" for="form-domingo">Domingo</label></td>
                            <td><label class="switch"><input <?= $turnoObj->get_domingo() == "1"? "checked" :"" ?>  type="checkbox" value="1" name="domingo" id="form-domingo"><span class="slider round"></span></label> </td>
                        </tr>
                        </table>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                <button type="submit" form="form-turno" class="btn btn-primary">Modificar</button>
            </div>
        </div>
    </div>