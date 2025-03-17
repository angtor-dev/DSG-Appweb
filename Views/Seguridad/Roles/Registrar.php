<div class="page-inner">
    <div class="d-flex mb-4">
        <a href="<?= LOCAL_DIR ?>/Seguridad/Roles" class="btn btn-primary rounded-pill">
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </a>
        <nav aria-label="breadcrumb" class="d-flex align-items-center border-start ms-4 ps-4">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item">
                    <a href="<?= LOCAL_DIR ?>/"><i class="fa-solid fa-house-chimney"></i></a>
                </li>
                <li class="breadcrumb-item"><a href="<?= LOCAL_DIR ?>/Seguridad/Roles">Seguridad</a></li>
                <li class="breadcrumb-item"><a href="<?= LOCAL_DIR ?>/Seguridad/Roles">Roles</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registrar</li>
            </ol>
        </nav>
    </div>
    <div class="card" style="max-width: 650px;">
        <div class="card-header bg-white">
            <h5 class="card-title my-2">
                Registrar nuevo rol
            </h5>
        </div>
        <div class="card-body">
            <form method="post" id="form-rol">
                <div class="row gy-3">
                    <div class="col-md-12">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input class="form-control" type="text" id="nombre" name="nombre">
                        <div class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label">Descripcion <small class="text-secondary">(opcional)</small></label>
                        <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                        <div class="form-text invalid-feedback"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between gap-3">
                <a href="<?= LOCAL_DIR ?>/Seguridad/Roles" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" form="form-rol" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>

<?php agregarScript("validaciones/rol.js") ?>