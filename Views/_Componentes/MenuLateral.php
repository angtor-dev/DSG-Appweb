<?php $usuarioSesion = $_SESSION['usuario'] ?>

<div id="menu-lateral" class="sidebar">
    <div class="user acordeon" style="cursor: pointer;">
        <div class="acordeon-toggle d-flex align-items-center">
            <div class="avatar me-2 d-flex align-items-center justify-content-center fs-5">
                <i class="fa-solid fa-user"></i>
                <!-- Foto aqui -->
            </div>
            <div class="info gap-1">
                    <span style="font-size: 14px;"><?= $usuarioSesion->getNombre() ?></span>
                    <span style="font-size: 12px; font-weight: 500; color: #000;"><?= $usuarioSesion->rol->getNombre() ?></span>
            </div>
        </div>
        <div class="acordeon-body">
            <div class="acordeon-items ps-0">
                <div class="mt-3">
                    <!--                     
                    <a href="#" class="link">Mi perfil</a>
                    <a href="#" class="link">Ajustes</a>
                    -->
                    <a href="<?= LOCAL_DIR ?>/Login/Logout" class="link d-flex justify-content-between">
                        Cerrar sesión
                        <i class="fa-solid fa-power-off"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <a href="<?= LOCAL_DIR ?>" class="sidebar-button mx-3 mt-3
        <?= empty($uriParts[0]) ? "active" : "" ?>">
        <i class="fa-solid fa-house-chimney"></i>
        Dashboard
    </a>

    <h4>Principal</h4>
    <?php if (tienePermiso(Modulo::TAREAS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Tareas" class="sidebar-button mx-3
            <?= strtolower($uriParts[0]) == "tareas" ? "active" : "" ?>">
            <i class="fa-solid fa-list-check"></i>
            Tareas
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::TRABAJADORES, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Trabajadores" class="sidebar-button mx-3
            <?= strtolower($uriParts[0]) == "trabajadores" ? "active" : "" ?>">
            <i class="fa-solid fa-users"></i>
            Trabajadores
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::ASISTENCIAS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Asistencias" class="sidebar-button mx-3
            <?= strtolower($uriParts[0]) == "asistencias" ? "active" : "" ?>">
            <i class="fa-solid fa-calendar-check"></i>
            Asistencias
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::AREAS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Areas" class="sidebar-button mx-3
            <?= strtolower($uriParts[0]) == "areas" ? "active" : "" ?>">
            <i class="fa-solid fa-map-location"></i>
            Areas
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::INVENTARIO, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Inventario" class="sidebar-button mx-3
            <?= strtolower($uriParts[0]) == "inventario" ? "active" : "" ?>">
            <i class="fa-solid fa-warehouse"></i>
            Inventario
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::MEDIDAS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Medidas" class="sidebar-button mx-3
            <?= strtolower($uriParts[0]) == "medidas" ? "active" : "" ?>">
            <i class="fa-solid fa-ruler-vertical"></i>
            Medidas
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::DEPARTAMENTOS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Departamentos" class="sidebar-button mx-3
            <?= strtolower($uriParts[0]) == "departamentos" ? "active" : "" ?>">
            <i class="fa-solid fa-building"></i>
            Departamentos
        </a>
    <?php endif ?>

    <?php if (tienePermiso(Modulo::USUARIOS, Permiso::CONSULTAR)
        || tienePermiso(Modulo::ROLES, Permiso::CONSULTAR)
        || tienePermiso(Modulo::BITACORA, Permiso::CONSULTAR)): ?>
        <h4>Sistema</h4>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::USUARIOS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Usuarios" class="sidebar-button mx-3
            <?= strtolower($uriParts[0]) == "usuarios" ? "active" : "" ?>">
            <i class="fa-solid fa-user"></i>
            Usuarios
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::ROLES, Permiso::CONSULTAR)
        || tienePermiso(Modulo::BITACORA, Permiso::CONSULTAR)): ?>
        <div class="mx-3 acordeon <?= strtolower($uriParts[0]) == "seguridad" ? "show" : "" ?>">
            <button class="acordeon-toggle sidebar-button
                <?= strtolower($uriParts[0]) == "seguridad" ? "active" : "" ?>">
                <i class="fa-solid fa-lock"></i>
                Seguridad
            </button>
            <div class="acordeon-body">
                <div class="acordeon-items py-2">
                    <?php if (tienePermiso(Modulo::ROLES, Permiso::CONSULTAR)): ?>
                        <a href="<?= LOCAL_DIR ?>/Seguridad/Roles"
                            class="<?= strtolower($uriParts[1]) == "roles" ? "active" : "" ?>">
                            Roles y permisos
                        </a>
                    <?php endif ?>
                    <?php if (tienePermiso(Modulo::BITACORA, Permiso::CONSULTAR)): ?>
                        <a href="<?= LOCAL_DIR ?>/Seguridad/Bitacora"
                            class="<?= strtolower($uriParts[1]) == "bitacora" ? "active" : "" ?>">
                            Bitacora
                        </a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>