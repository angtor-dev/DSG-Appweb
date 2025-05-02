<?php /** @var Asistencia[] $asistencias */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Asistencias</h3>
                <span class="opacity-75 mb-2">Gestiona a las asistencias de los trabajadores de los diferentes departamentos de la Dirección de Servicios Generales</span>
            </div>
            <?php if (tienePermiso(Modulo::ASISTENCIAS, Permiso::REGISTRAR)): ?>
                <div class="d-none">
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="<?= LOCAL_DIR ?>/Asistencias/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nueva Asistencia
                    </button>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <label for="departamento">Departamento </label>
                        <select name="departamento" class="form-select" id="departamento">
                            <option value=""></option>
                            <?php foreach ($departamentos as $departamento): ?>
                                <option value="<?= $departamento->id ?>"><?= $departamento->getNombre() ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col">
                        <label for="turno">Turno </label>
                        <select name="turno" class="form-select" id="turno">
                            <option value=""></option>
                            <?php foreach (Turno::cases() as $turno): ?>
                                <option value="<?= $turno->value ?>"><?= ucfirst($turno->value) ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col">
                        <label for="fecha">Fecha </label>
                        <input type="date" name="fecha" class="form-control" id="fecha">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col d-flex justify-content-lg-start align-items-center">
                        <button class="btn btn-primary" onclick="cargarDepartamentos()">Ver Asistencias</button>
                    </div>
                    <script>
                        function cargarDepartamentos() {
                            if(document.getElementById("tabla-asistencias").classList.contains("d-none")) {
                                document.getElementById("tabla-asistencias").classList.remove("d-none");
                            }
                            else {
                                document.getElementById("tabla-asistencias").classList.add("d-none");
                            }
                        }

                    </script>
                </div>
                <hr>
                <div class="container">
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
                            width: 10px;
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
                    <table class="table table-responsive table-striped d-none" id="tabla-asistencias">
                        <tbody>
                            <tr>
                                <td class="align-content-center nombre">Xavier David Sanchez Suares</td>
                                <td class="cell-inasistencia align-content-center">

                                    <label class="text-nowrap no-select cursor-pointer">
                                        <span>Inasistencia</span>
                                        <input type="checkbox" class="inasistencia-check">
                                        <div class="check-feedback"></div>
                                    </label>
                                </td>
                                <td class="cell-horas w-100">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col">
                                                <label for="hora_entrada" class="form-label">Hora de Entrada</label>
                                                <input type="time" class="form-control" id="hora_entrada" name="hora_entrada" data-span="invalid-span-hora_entrada">
                                                <div id="invalid-span-hora_entrada" class="form-text invalid-feedback"></div>
                                            </div>
                                            <div class="col">
                                                <label for="hora_salida" class="form-label">Hora de salida</label>
                                                <input type="time" class="form-control" id="hora_salida" name="hora_salida" data-span="invalid-span-hora_salida">
                                                <div id="invalid-span-hora_salida" class="form-text invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="cell-justificacion w-100">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col">
                                                <label for="justificacion" class="form-label">Justificación</label>
                                                <select name="justificacion" class="form-select">
                                                    <option value="1">Medico</option>
                                                    <option value="2">Emergencia</option>
                                                    <option value="3">Enfermedad</option>
                                                    <option value="4">Vacaciones</option>
                                                    <option value="5">Otro</option>
                                                </select>
                                                <div id="invalid-span-justificacion" class="form-text invalid-feedback"></div>
                                            </div>
                                            <div class="col justificacion-col">
                                                <label for="justificacion" class="form-label">Descripción</label>
                                                <input type="text" class="form-control" id="justificacion" name="justificacion" data-span="invalid-span-justificacion">
                                                <div id="invalid-span-justificacion" class="form-text invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-content-center nombre">Xavier David Sanchez Suares</td>
                                <td class="cell-inasistencia align-content-center">

                                    <label class="text-nowrap no-select cursor-pointer">
                                        <span>Inasistencia</span>
                                        <input type="checkbox" class="inasistencia-check">
                                        <div class="check-feedback"></div>
                                    </label>
                                </td>
                                <td class="cell-horas w-100">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col">
                                                <label for="hora_entrada" class="form-label">Hora de Entrada</label>
                                                <input type="time" class="form-control" id="hora_entrada" name="hora_entrada" data-span="invalid-span-hora_entrada">
                                                <div id="invalid-span-hora_entrada" class="form-text invalid-feedback"></div>
                                            </div>
                                            <div class="col">
                                                <label for="hora_salida" class="form-label">Hora de salida</label>
                                                <input type="time" class="form-control" id="hora_salida" name="hora_salida" data-span="invalid-span-hora_salida">
                                                <div id="invalid-span-hora_salida" class="form-text invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="cell-justificacion w-100">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col">
                                                <label for="justificacion" class="form-label">Justificación</label>
                                                <select name="justificacion" class="form-select">
                                                    <option value="1">Medico</option>
                                                    <option value="2">Emergencia</option>
                                                    <option value="3">Enfermedad</option>
                                                    <option value="4">Vacaciones</option>
                                                    <option value="5">Otro</option>
                                                </select>
                                                <div id="invalid-span-justificacion" class="form-text invalid-feedback"></div>
                                            </div>
                                            <div class="col justificacion-col">
                                                <label for="justificacion" class="form-label">Descripción</label>
                                                <input type="text" class="form-control" id="justificacion" name="justificacion" data-span="invalid-span-justificacion">
                                                <div id="invalid-span-justificacion" class="form-text invalid-feedback"></div>
                                            </div>
                                            <div class="col d-flex align-items-center">
                                                <button class="btn btn-primary"><span class="fa fa-gears"></span> ajuste</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="col"></div>
                    <script>
                        let checkList = document.querySelectorAll("input.inasistencia-check");
                        checkList.forEach(check => {
                            check.addEventListener("change", function(){
                                let check = this;
                                let td = check.closest("td");
                                if (check.checked) {
                                    td.classList.add("inasistencia-true");
                                } else {
                                    td.classList.remove("inasistencia-true");
                                }
                            })
                        })
                    </script>
                </div>


                
            </div>

        </div>
    </div>
</div>

<?php renderComponent('ModalGenerico') ?>

<script type="text/javascript">


    function agregarValidaciones(){
        
    }
    /**
     * 
     */
    

</script>

<?php // agregarScript("trabajador.js") ?>
<?php // agregarScript("validaciones/trabajador.js") ?>