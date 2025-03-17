<?php /** @var Servicio[] $servicios */ ?>
<?php /** @var Consulta $consulta */ ?>

<div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
        <div class="modal-header bg-light">
            <h5 class="modal-title">
                Asignar servicio
            </h5>
        </div>
        <div class="modal-body">
            <table class="table">
                <tr>
                    <th>Servicio</th>
                    <th>Descripción</th>
                    <th>Costo</th>
                    <th></th>
                </tr>
                <?php foreach ($servicios as $servicio): ?>
                    <tr>
                        <td><?= $servicio->getNombre() ?></td>
                        <td><?= $servicio->getDescripcion() ?></td>
                        <td><?= $servicio->getCosto() ?></td>
                        <td>
                            <a href="<?= LOCAL_DIR ?>/Citas/AgregarServicio?idConsulta=<?= $consulta->id ?>&idServicio=<?= $servicio->id ?>"
                                class="btn btn-primary">
                                <i class="fa-solid fa-plus"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
        <div class="modal-footer d-flex justify-content-end gap-2 bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
    </div>
</div>