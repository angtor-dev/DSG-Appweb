<?php 
$colorHex = sprintf("%02x%02x%02x", rand(0, 255), rand(0, 255), rand(0, 255));
?>

<div class="modal-dialog modal-lg">
<div class="modal-content">
        <div class="modal-header bg-white">
            <h5 class="modal-title my-2">
                Registrar nueva categoría
            </h5>
        </div>
        <div class="modal-body">
            <form method="post" id="form-area">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="color" class="form-label">Color</label>
                        <input type="color" class="form-control" value="#<?= $colorHex ?>"
                            id="color" name="color" style="height: 38px;">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label">Descripción (opcional)</label>
                        <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="d-flex justify-content-between gap-3">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancelar</button>
                <button type="submit" form="form-area" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>