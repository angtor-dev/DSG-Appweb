<?php /** @var $trabajador Trabajador */ ?>
<style>
    .siglas{
        text-transform: uppercase;
        font-weight: bold;
        font-family: "Lucida Console", Monaco, monospace;
    }
</style>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header panel-header text-white">
            <h5 class="modal-title my-2">
                Datos del Trabajador (<span class="siglas" >C.I:</span> <?= $trabajador->getCedula(); ?>)
            </h5>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-info-field" data-info="Nombre" >
                        <?= $trabajador->getNombre(); ?>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-info-field" data-info="Apellido" >
                        <?= $trabajador->getApellido(); ?>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-info-field" data-info="Télefono" >
                        <?= $trabajador->getTelefono(); ?>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-info-field" data-info="Fecha de Ingreso" >
                        <?= getFecha($trabajador->getFechaIngreso()); ?>
                    </div>
                </div>
                <div class="col-12 col-md">
                    <div class="form-info-field" data-info="Antiguedad" >
                        <?= $trabajador->getAntiguedad(); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div>
                        <hr>
                    </div>
                    <div>
                        <span class="h3">Trayectoria</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Turno</th>
                                    <th>Cargo</th>
                                    <th>División</th>
                                    <th>Desde</th>
                                    <th>Hasta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $trayectoria = $trabajador->getTrayectoria(); ?>
                                <?php foreach ($trayectoria as $tray): ?>
                                    <tr>
                                        <td><?= $tray["turno"]; ?></td>
                                        <td><?= $tray["cargo"]; ?></td>
                                        <td><?= $tray["division"]; ?></td>
                                        <td><?= $tray["desde"]; ?></td>
                                        <td><?= $tray["hasta"]; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (count($trayectoria) == 0): ?>
                                    <tr>
                                        <td colspan="5">No hay trayectoria</td>
                                    </tr>
                                <?php endif; ?>
                                    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cerrar</button>
                
            </div>
        </div>
    </div>
</div>