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

    <?php if (tienePermiso('usuarios', 'consultar')
        || tienePermiso('roles', 'consultar')
        || tienePermiso('bitacora', 'consultar')): ?>
        <h4>Sistema</h4>
    <?php endif ?>
    <?php if (tienePermiso('usuarios', 'consultar')): ?>
        <a href="<?= LOCAL_DIR ?>/Usuarios" class="sidebar-button mx-3
            <?= strtolower($uriParts[0]) == "usuarios" ? "active" : "" ?>">
            <i class="fa-solid fa-user"></i>
            Usuarios
        </a>
    <?php endif ?>
    <?php if (tienePermiso('roles', 'consultar')
        || tienePermiso('bitacora', 'consultar')): ?>
        <div class="mx-3 acordeon <?= strtolower($uriParts[0]) == "seguridad" ? "show" : "" ?>">
            <button class="acordeon-toggle sidebar-button
                <?= strtolower($uriParts[0]) == "seguridad" ? "active" : "" ?>">
                <i class="fa-solid fa-lock"></i>
                Seguridad
            </button>
            <div class="acordeon-body">
                <div class="acordeon-items py-2">
                    <?php if (tienePermiso('roles', 'consultar')): ?>
                        <a href="<?= LOCAL_DIR ?>/Seguridad/Roles"
                            class="<?= strtolower($uriParts[1]) == "roles" ? "active" : "" ?>">
                            Roles y permisos
                        </a>
                    <?php endif ?>
                    <?php if (tienePermiso('bitacora', 'consultar')): ?>
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