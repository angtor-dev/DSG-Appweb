<?php /** @var Departamento[] $departamentos */ ?>

<style>
    .cell-justificacion {
        display: none;
    }
    .cell-inasistencia.inasistencia-true + .cell-horas {
        display: none;
    }
    .cell-inasistencia.inasistencia-true ~ .cell-justificacion {
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

    .inasistencia-check:checked+.check-feedback::after{
        background-color: var(--color);
    }

</style>

<script>
    // imprime desde php un array asociativo de javascript con el enum Justificacion con el name y value del enum
    <?php 
        $enumTemp = [];
        foreach (Justificacion::cases() as $key => $value) {
            $enumTemp[] = $value->name;
            
        }
    ?>
    const justificacionesEnum = <?php echo json_encode($enumTemp); ?>;
</script>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Asistencias</h3>
                <span class="opacity-75 mb-2">Gestiona a las asistencias de los trabajadores de los diferentes departamentos de la Dirección de Servicios Generales</span>
            </div>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <div class="container w-100 overflow-auto">
                <form id="form-table-asistencias" class="d-table w-100">
                    <div style="display: table-row-group">
                        <div class="d-table-row">
                            <div class="d-table-cell">
                                <div class="row flex-nowrap w-100">
                                    <div class="col flex-fill">
                                        <label for="departamento">Departamento </label>
                                        <select required name="departamento" class="form-select" id="departamento">
                                            <option value=""></option>
                                            <?php foreach ($departamentos as $departamento): ?>
                                                <option value="<?= $departamento->id ?>"><?= $departamento->getNombre() ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <div class="form-text invalid-feedback"></div>
                                    </div>
                                    <div class="col flex-fill">
                                        <label for="turno">Turno </label>
                                        <select required name="turno" class="form-select" id="turno">
                                            <option value=""></option>
                                            <?php foreach (Turno::cases() as $turno): ?>
                                                <option value="<?= $turno->value ?>"><?= ucfirst($turno->value) ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <div class="form-text invalid-feedback"></div>
                                    </div>
                                    <div class="col flex-fill">
                                        <label for="fecha">Fecha </label>
                                        <input required type="date" name="fecha" class="form-control" id="fecha" min="2000-01-01">
                                        <div class="form-text invalid-feedback"></div>
                                    </div>
                                    <div class="col d-flex justify-content-lg-start">
                                        <label style="opacity: 0 ;" class="no-select">l</label>
                                        <button type="button" class="btn btn-primary text-nowrap" id="btn-cargar" onclick="cargarDepartamentos()">Ver Asistencias</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="container d-table-row">
                            
                                <div id="tabla-asistencias" class="d-none">

                                    <table class="table table-responsive table-striped">
                                        <tbody>
                                            <!-- dinamic content  -->



                                        </tbody>
                                    </table>
                                    <div class="container">
                                        <?php if (tienePermiso('asistencia', Permiso::REGISTRAR)): ?>
                                            <div class="row justify-content-end">
                                                <div class="col-3 text-center">
                                                    <button type="submit" class="btn btn-primary" id="submit-asistencias">Guardar</button>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="row justify-content-end">
                                                <div class="col-3 text-center">
                                                    No posee los permisos para realizar registros
                                                </div>
                                            </div>
                                            <div id="submit-asistencias" class="d-none"></div>
                                        <?php endif ?>
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



<?php agregarScript("asistencias.js") ?>
<?php // agregarScript("validaciones/trabajador.js") ?>