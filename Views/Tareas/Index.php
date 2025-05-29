<?php

/** @var Tareas $tarea */ ?>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Tareas</h3>
                <span class="opacity-75 mb-2">Gestiona a las Tareas de la Dirección de Servicios Generales</span>
            </div>
            <?php if (tienePermiso(Modulo::TAREAS, Permiso::REGISTRAR)): ?>
                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-tareas" data-backdrop="static"
                        data-bs-url="<?= LOCAL_DIR ?>/Tareas/Registrar">
                        <i class="fa-solid fa-plus me-2"></i>
                        Nueva tarea
                    </button>
                </div>

                <div>
                    <button style="padding: .65rem 1.4rem;"
                        class="btn btn-outline-light rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modal-generico" 
                        data-bs-url="<?= LOCAL_DIR ?>/Tareas/Reporte">
                        <i class="fa-solid fa-file-alt me-2"></i>
                        Reporte de tareas
                    </button>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class=" row card-body p-4">
            <div class="col">
                <div class="card border">
                    <div class="card-body justify-content-center align-items-center d-flex flex-column">
                        <h4 class="card-title">2</h4>
                        <p class="card-text">Tareas activas</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border">
                    <div class="card-body justify-content-center align-items-center d-flex flex-column">
                        <h4 class="card-title">7</h4>
                        <p class="card-text">Tareas Vencidas</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border">
                    <div class="card-body justify-content-center align-items-center d-flex flex-column">
                        <h4 class="card-title">2</h4>
                        <p class="card-text">Tareas Canceladas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Activas</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Vencidas</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Cancelado</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="evaluada-tab" data-bs-toggle="tab" data-bs-target="#evaluada-tab-pane" type="button" role="tab" aria-controls="evaluada-tab-pane" aria-selected="false">Evaluadas</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="comun-tab" data-bs-toggle="tab" data-bs-target="#comun-tab-pane" type="button" role="tab" aria-controls="comun-tab-pane" aria-selected="false">Comunes</button>
                </li>

            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                    <div class="table-responsive table-dsg">
                        <table class="datatable table table-striped table-hover" id="tabla-activas"> 
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Area</th>
                                    <th>Departamento</th>
                                    <th>Descripcion</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                              

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <div class="table-responsive table-dsg">
                        <table class="datatable table table-striped table-hover" id="tabla-vencidas"> 
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Area</th>
                                    <th>Departamento</th>
                                    <th>Descripcion</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                    <div class="table-responsive table-dsg">
                        <table class="datatable table table-striped table-hover" id="tabla-cancelada"> 
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Area</th>
                                    <th>Departamento</th>
                                    <th>Descripcion</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="evaluada-tab-pane" role="tabpanel" aria-labelledby="evaluada-tab" tabindex="0">
                    <div class="table-responsive table-dsg">
                        <table class="datatable table table-striped table-hover" id="tabla-evaluada"> 
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Area</th>
                                    <th>Departamento</th>
                                    <th>Descripcion</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="comun-tab-pane" role="tabpanel" aria-labelledby="comun-tab" tabindex="0">
                    <div class="table-responsive table-dsg">
                        <table class="datatable table table-striped table-hover" id="tabla-comun"> 
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Area</th>
                                    <th>Departamento</th>
                                    <th>Descripcion</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php renderComponent('ModalCancelar') ?>
<?php renderComponent('ModalGenerico') ?>
<?php renderComponent('ModalTareas') ?>
<?php renderComponent('ModalOrden') ?>
<?php renderComponent('ModalEvaluar') ?>
<?php renderComponent('ModalDetalles') ?>


<script src="public/js/tareas.js"></script>

  <script>




    function addMaterial() {
        let container = document.getElementById('materiales-container');
        let index = container.children.length;
        let html = `
            <div class="input-group mb-3">
                <select class="form-select" id="materiales-${index}" name="materiales[${index}][id]">
                    <option value="" selected disabled>Seleccione un material</option>
                    <option value="1">Tornillos Milimetrico 2"</option>
                    <option value="2">Tuercas 10mm  </option>
                    <option value="3">Madera Liston 2Mtrs</option>
                    <option value="4">Pintura amarilla aceite galón</option>
                    <option value="5">Herramientas</option>
                </select>
                <input type="number" class="form-control" id="materiales-${index}-cantidad" name="materiales[${index}][cantidad]" min="1" value="1">
                <button type="button" class="btn btn-outline-secondary" onclick="removeMaterial(${index})">
                    <i class="fa-solid fa-minus"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function removeMaterial(index) {
        let container = document.getElementById('materiales-container');
        container.children[index].remove();
    }


</script> 
