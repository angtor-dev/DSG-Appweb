<?php /** @var Asistencia[] $asistencias */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner py-5">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Asistencias</h3>
                <span class="opacity-75 mb-2">Gestiona a las asistencias de los trabajadores de los diferentes departamentos de la Dirección de Servicios Generales</span>
            </div>
            <?php if (tienePermiso(Modulo::ASISTENCIAS, Permiso::REGISTRAR)): ?>
                <div>
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
            <div class="table-responsive table-dsg">
                <table class="datatable table table-striped table-hover" id="tabla-trabajadores">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Cedula</th>
                            <th>Departamento</th>
                            <th>Fecha</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asistencias as $asistencia): ?>
                            <tr>
                                <td><?= $asistencia->getNombreCompleto() ?></td>
                                <td><?= $asistencia->getCedula() ?></td>
                                <td><?= $asistencia->departamento->getNombre() ?></td>
                                <?php echo $asistencia->getEntrada(); ?>
                                <td>
                                    <div class="d-flex justify-content-evenly w-100 gap-3">
                                        <?php if (tienePermiso(Modulo::ASISTENCIAS, Permiso::ACTUALIZAR)): ?>
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Editar">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Trabajadores/Actualizar?id=<?= $asistencia->id ?>">
                                                    <i class="fa-solid fa-fw fa-pen-to-square"></i>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (tienePermiso(Modulo::ASISTENCIAS, Permiso::ELIMINAR)): ?>
                                            <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                                                <div data-bs-toggle="modal" data-bs-target="#modal-eliminar"
                                                    data-bs-modelo="al trabajador" 
                                                    data-bs-nombre="<?= $asistencia->getNombreCompleto() ?>"
                                                    data-bs-url="<?= LOCAL_DIR ?>/Trabajadores/Eliminar?id=<?= $asistencia->id ?>">
                                                    <i class="fa-solid fa-fw fa-trash-can"></i>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php renderComponent('ModalGenerico') ?>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        tablaTrabajadores = new DataTable('#tabla-trabajadores', {
            pagingType: 'simple_numbers',
            language: {
                url: '<?= LOCAL_DIR ?>/public/lib/DataTables/datatables-spanish.json'
            },
            layout: {
                topStart: {
                    buttons: ['excel', 'pdf', 'print']
                },
                bottom1Start: {
                    pageLength: true
                }
            }
        })
    })

    async function peticion (url){

        let response = await fetch("<?= LOCAL_DIR ?>"+url);

        let data = await response.text()

        if (!response.ok) {
            mostrarError("Error de solicitud");
            console.error(data)
            return false;
        }

        return data;

    }


    function agregarValidaciones(){
        document.getElementById('cedula').onkeyup= async function(e){

            if(/^[\d]{8}$/.test(this.value)){
                let data = await peticion(`/Asistencias/Registrar?cedula=${this.value}`);
                data = JSON.parse(data);
                console.log(data);
                if(data.id){
                    document.getElementById("nombre").innerHTML = data.nombre;
                    document.getElementById("departamento").innerHTML = data.departamento

                    document.getElementById('fecha').disabled = false;
                    document.getElementById('fechaIn').disabled = false;
                    document.getElementById('fechaOut').disabled = false;
                    return true;
                }
            }
            document.getElementById('fecha').disabled = true;
            document.getElementById('fechaIn').disabled = true;
            document.getElementById('fechaOut').disabled = true;
            document.getElementById("nombre").innerHTML ="";

            document.getElementById("departamento").innerHTML ="";




        }
    }
    

</script>

<?php // agregarScript("trabajador.js") ?>
<?php // agregarScript("validaciones/trabajador.js") ?>