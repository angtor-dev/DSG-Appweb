<?php /** @var Division[] $departamentos */ ?>

<style>
    .cell-justificacion,
    .cell-inasistencia.inasistencia-true + .cell-horas,
    .no-aplica-cell {
        display: none;
    }
    .cell-inasistencia.inasistencia-true ~ .cell-justificacion {
        display: table-cell;
    }
    .cell-inasistencia.no-aplica + .cell-horas,
    .cell-inasistencia.no-aplica > .inasistencia-label,
    .cell-inasistencia.no-aplica ~ .cell-justificacion,
    .cell-inasistencia.no-aplica ~ .cell-ajuste {
        display: none !important;
    }
    .cell-inasistencia.no-aplica ~ .no-aplica-cell {
        display: table-cell;
    }
    .cursor-pointer{
        cursor: pointer !important;
    }
    .no-select/*Evita seleccion (sombreado azul)*/
    {
        -moz-user-select: -moz-none;
        -khtml-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .nombre{
        white-space: nowrap;
        max-width: 50%;
    }

    .inasistencia-check{
        display: none;
    }

    .check-feedback{
        --size:1rem;
        --color:#0d6efd;
        width: var(--size) !important;
        height: var(--size) !important;
        border: 1px solid var(--color);
        border-radius: 100%;
        display: inline-block;
        padding: 2px;
        margin-bottom: -3px;

    }
    .check-feedback::after{
        content: "";
        width: 100%;
        height: 100%;
        background-color: transparent;
        display: block;
        border-radius: 100%;
    }

    label.check-radio-like > input[type="checkbox"]{
        display: none;
    }

    label.check-radio-like > input[type="checkbox"]:checked + .check-feedback::after{
        background-color: var(--color);
        
    }


    .inasistencia-check:checked+.check-feedback::after{
        background-color: var(--color);
    }

    .ajuste-btn{
        display: none;
    }

    .cell-inasistencia.inasistencia-true ~ .cell-ajuste > .ajuste-btn.ajuste-btn-active{
        display: block;
    }

</style>

<script>

    /**
     * @typedef {Object} EnumJustificacion
     * @property {number} Injustificado
     * @property {number} Vacaciones
     * @property {number} Medico
     * @property {number} Emergencia
     * @property {number} Judicial
     * @property {number} Enfermedad
     * @property {number} Muerte_De_Un_Familiar
     * @property {number} Otro
    */


    /** @type {EnumJustificacion} */
    const justificacionesEnum = <?php echo getJustificacionJson(); ?>; // objeto de javascript con el enum Justificacion
</script>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Asistencias</h3>
                <span class="opacity-75 mb-2">Gestiona a las asistencias e inasistencias de los trabajadores de las diferentes divisiones del departamento de Servicios Generales</span>
            </div>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <div class="container w-100 overflow-auto">
                <input type="hidden" name="fecha" id="fechaAsistencia">
                <form id="form-table-asistencias" class="d-table w-100" onsubmit="return false">
                    <div style="display: table-row-group">
                        <div class="d-table-row">
                            <div class="d-table-cell">
                                <div class="row flex-nowrap w-100">
                                    <div class="col flex-fill">
                                        <label for="departamento">División </label>
                                        <select required name="departamento" class="form-select" id="departamento">
                                            <option value="">- Seleccione <?= DEP_NAME ?> -</option>
                                            <?php foreach ($departamentos as $departamento): ?>
                                                <option value="<?= $departamento->id ?>"><?= $departamento->getNombre() ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <div class="form-text invalid-feedback"></div>
                                    </div>
                                    <div class="col flex-fill">
                                        <label for="turno">Turno </label>
                                        <select required name="turno" class="form-select" id="turno">
                                            <option value="">- Seleccione Turno -</option>
                                            <?= Turno::getTurnosOptions(null,true); ?>
                                        </select>
                                        <div class="form-text invalid-feedback"></div>
                                    </div>
                                    <div class="col flex-fill">
                                        <label for="fecha">Fecha </label>
                                        <input required type="date" name="fecha" class="form-control" id="fecha" min="2000-01-01">
                                        <div class="form-text invalid-feedback"></div>
                                    </div>
                                    <div class="col">
                                        <label style="opacity: 0 ;" class="no-select">l</label>
                                        <button type="button" class="btn btn-primary text-nowrap no-select" id="btn-cargar" onclick="cargarDepartamentos()">Ver Asistencias</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="container d-table-row">
                            <?php if(ASISTENCIAS_SEMANALES):?>
                                <div id="tabla-asistencias-semanales" class="d-none">

                                <?php $diasTh=[ 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa', 'Do' ]; ?>

                                <style>
                                    .laborable-info{
                                        display: block;
                                        font-size: .8rem;
                                        font-weight: normal;
                                        margin-top: 5px;
                                        white-space: nowrap;
                                    }
                                    .asistencia-checkbox + button{
                                        transition: all .5s ease;
                                        overflow: hidden;
                                        width: 34.83px;
                                        opacity: 1;
                                    }
                                    .asistencia-checkbox:disabled + button,
                                    .asistencia-checkbox:checked + button{
                                        /* display: none; */
                                        
                                        flex-basis: 0%;
                                        padding: 0;
                                        opacity: 0;
                                    }
                                    .celda-dia{
                                        display: flex;
                                        align-items: center;
                                        align-content: center;
                                        flex-wrap: nowrap;
                                        min-height: 47px;
                                        justify-content: space-between;
                                    }
                                    .celda-dia input,
                                    .celda-dia button{
                                        flex-grow: 0;
                                        flex-shrink: 0;
                                        flex-basis: 50%;
                                    }
                                    .asistencia-checkbox{
                                        margin: 0;
                                        transition: all .5s ease;
                                    }
                                    .asistencia-checkbox:disabled,
                                    .asistencia-checkbox:checked{
                                        margin: 0 25%;
                                        
                                    }

                                    table.no-records-found label.switch,
                                    table.no-records-found label.switch+small{
                                        display: none;
                                    }
                                    
                                    
                                    

                                </style>
                            <?php else: // phpcs:disable ?>
                                
                                <div id="tabla-asistencias" class="d-none"></div>
                            <?php endif;// phpcs:enable ?>

                                <div class="container">
                                    <div class="row justify-content-end">
                                        <table class="table table-responsive table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Cedula</th>
                                                    <th>Nombre</th>
                                                    <?php if(ASISTENCIAS_SEMANALES): ?>
                                                        <?php foreach ($diasTh as $dia): ?>
                                                            <th>
                                                                <div class="text-center">

                                                                    <label class="switch switch-small">
                                                                        <input type="checkbox" class="laborable_check" value="true" name="chekcbox_<?= $dia ?>" id="chekbox_<?= $dia ?>">
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                    <small class="laborable-info text-danger">No Laborable</small>


                                                                </div>
                                                                <div class="text-center">
                                                                    <?= $dia ?>
                                                                </div>
                                                                <div class="text-center">
                                                                    <small id="dia_th_<?= $dia ?>">2022-12-12</small>
                                                                </div>
                                                            </th>    
                                                        <?php endforeach ?>
                                                    <?php endif; ?>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- dinamic content  -->
                                            </tbody>
                                        </table>

                                            <?php if (tienePermiso('asistencias', Permiso::ELIMINAR)): ?>
                                                <div class="col-auto text-center d-none btn-eliminar-asistencias">
                                                    <button type="button" class="btn btn-danger" id="eliminar-asistencias">Eliminar</button>
                                                </div>
                                            <?php endif ?>

                                        
                                            <?php if (tienePermiso('asistencias', Permiso::REGISTRAR)): ?>
                                                    <div class="col-auto text-center d-none" id="guardar-asistencias-info">
                                                        <small>*Los Registros de asistencias aun no han sido guardados</small>
                                                    </div>
                                                    <div class="col-auto text-center">
                                                        <button type="submit" class="btn btn-primary" id="submit-asistencias">Guardar</button>
                                                    </div>
                                            <?php else: ?>
                                                    <div class="col-auto text-center">
                                                        No posee los permisos para realizar registros
                                                    </div>
                                                <div id="submit-asistencias" class="d-none"></div>
                                            <?php endif ?>

                                        </div>
                                </div>

                                </div>

                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php renderComponent('ModalGenerico') ?>
<?php renderComponent('modalEliminarPromise') ?>

<style>
    #modalTrabajadorInfo{
        font-weight: normal;
        font-size: 1rem;
    }
</style>
<div class="modal fade" id="modalInasistencia" tabindex="-1" aria-labelledby="modalInasistenciaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formInasistencia" onsubmit="return false">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalInasistenciaLabel">Registrar Inasistencia <br> <small id="modalTrabajadorInfo"></small> </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="modalTrabajadorId">
          <input type="hidden" id="modalDia">
          <div class="mb-3">
            <label for="justificacion" class="form-label">Tipo de Justificación</label>
            <select class="form-select" id="justificacion" required>
                <?php echo getJustificacionOptions(); ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="observacion" class="form-label">Observación (opcional)</label>
            <textarea class="form-control" id="observacion" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="cancelarModal" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>


    
</script>



<?php 
if(ASISTENCIAS_SEMANALES){
    agregarScript("asistencias-semanales.js");
}
else{
    // phpcs:disable
    agregarScript("asistencias.js");
    // phpcs:enable
}
?>
