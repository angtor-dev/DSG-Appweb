<?php
$usuarioSesion = $_SESSION['usuario'];
$moduloActual = isset($uriParts[0]) ? strtolower($uriParts[0]) : '';
$subModuloActual = isset($uriParts[1]) ? strtolower($uriParts[1]) : '';
?>

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
                    <a href="#" class="link">Ajustes</a>
                    -->
                    <a href="<?= LOCAL_DIR ?>/Perfil" class="link d-none">Mi perfil</a>
                    <a href="<?= LOCAL_DIR ?>/Login/Logout" class="link d-flex justify-content-between">
                        Cerrar sesión
                        <i class="fa-solid fa-power-off"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <a href="<?= LOCAL_DIR ?>" class="sidebar-button mx-3 mt-3
        <?= empty($moduloActual) ? "active" : "" ?>">
        <i class="fa-solid fa-house-chimney"></i>
        Inicio
    </a>

    <?php if (tienePermiso(Modulo::AREAS, Permiso::CONSULTAR)
        || tienePermiso(Modulo::DEPARTAMENTOS, Permiso::CONSULTAR)
        || tienePermiso(Modulo::MEDIDAS, Permiso::CONSULTAR)
        || tienePermiso(Modulo::CATEGORIAS, Permiso::CONSULTAR)
        || tienePermiso(Modulo::CARGOS, Permiso::CONSULTAR)
        || tienePermiso(Modulo::TURNOS, Permiso::CONSULTAR)): ?>
        <h4>Definiciones</h4>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::AREAS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Areas" class="sidebar-button mx-3
            <?= $moduloActual == "areas" ? "active" : "" ?>">
            <i class="fa-solid fa-map-location"></i>
            Áreas
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::CARGOS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Cargos" class="sidebar-button mx-3
            <?= $moduloActual == "cargos" ? "active" : "" ?>">
            <i class="fa-solid fa-id-card"></i>
            Cargos
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::CATEGORIAS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Categorias" class="sidebar-button mx-3
            <?= $moduloActual == "categorias" ? "active" : "" ?>">
            <i class="fa-solid fa-layer-group"></i>
            Categorías
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::DEPARTAMENTOS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Departamentos" class="sidebar-button mx-3
            <?= $moduloActual == "departamentos" ? "active" : "" ?>">
            <i class="fa-solid fa-building"></i>
            Divisiones
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::MEDIDAS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Medidas" class="sidebar-button mx-3
            <?= $moduloActual == "medidas" ? "active" : "" ?>">
            <i class="fa-solid fa-ruler-vertical"></i>
            Medidas
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::TURNOS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Turnos" class="sidebar-button mx-3
            <?= $moduloActual == "turnos" ? "active" : "" ?>">
            <i class="fa-solid fa-calendar-week"></i>
            Turnos
        </a>
    <?php endif ?>

    <h4>Principal</h4>
    <?php if (tienePermiso(Modulo::TRABAJADORES, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Trabajadores" class="sidebar-button mx-3
            <?= $moduloActual == "trabajadores" ? "active" : "" ?>">
            <i class="fa-solid fa-users"></i>
            Trabajadores
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::ASISTENCIAS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Asistencias" class="sidebar-button mx-3
            <?= $moduloActual == "asistencias" ? "active" : "" ?>">
            <i class="fa-solid fa-calendar-check"></i>
            Asistencias
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::ARTICULOS, Permiso::CONSULTAR)
        || tienePermiso(Modulo::AJUSTES, Permiso::CONSULTAR)
        || tienePermiso(Modulo::MOVIMIENTOS, Permiso::CONSULTAR)): ?>
        <div class="mx-3 acordeon <?= $moduloActual == "inventario" ? "show" : "" ?>">
            <button class="acordeon-toggle sidebar-button
                <?= $moduloActual == "inventario" ? "active" : "" ?>">
                <i class="fa-solid fa-toolbox"></i>
                Inventario
            </button>
            <div class="acordeon-body">
                <div class="acordeon-items">
                    <?php if (tienePermiso(Modulo::ARTICULOS, Permiso::CONSULTAR)): ?>
                        <a href="<?= LOCAL_DIR ?>/Inventario/Articulos"
                            class="<?= ($subModuloActual == "articulos") ? "active" : "" ?>">
                            Artículos
                        </a>
                    <?php endif ?>
                    <?php if (tienePermiso(Modulo::AJUSTES, Permiso::CONSULTAR)): ?>
                        <a href="<?= LOCAL_DIR ?>/Inventario/Ajustes"
                            class="<?= ($subModuloActual == "ajustes") ? "active" : "" ?>">
                            Correcciones de Inventario
                        </a>
                    <?php endif ?>
                    <?php if (tienePermiso(Modulo::NOTASENTREGA, Permiso::CONSULTAR)): ?>
                        <a href="<?= LOCAL_DIR ?>/Inventario/NotasEntrega"
                            class="<?= ($subModuloActual == "notasentrega") ? "active" : "" ?>">
                            Notas de Entrega
                        </a>
                    <?php endif ?>
                    <?php if (tienePermiso(Modulo::MOVIMIENTOS, Permiso::CONSULTAR)): ?>
                        <a href="<?= LOCAL_DIR ?>/Inventario/Movimientos"
                            class="<?= ($subModuloActual == "movimientos") ? "active" : "" ?>">
                            Movimientos
                        </a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::TAREAS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Tareas" class="sidebar-button mx-3
            <?= $moduloActual == "tareas" ? "active" : "" ?>">
            <i class="fa-solid fa-list-check"></i>
            Tareas
        </a>
    <?php endif ?>

    <h4>Datos</h4>
    <?php if (tienePermiso("reporteasistencias", "consultar")): ?>
        <div class="mx-3 acordeon <?= $moduloActual == "reportes" ? "show" : "" ?>">
            <button class="acordeon-toggle sidebar-button
                <?= $moduloActual == "reportes" ? "active" : "" ?>">
                <i class="fa-solid fa-file-invoice"></i>
                Reportes
            </button>
            <div class="acordeon-body">
                <div class="acordeon-items">
                    <?php if (tienePermiso("reporteasistencias", "consultar")): ?>
                        <a href="<?= LOCAL_DIR ?>/Reportes/Asistencia"
                            class="<?= ($subModuloActual == "reporteasistencia") ? "active" : "" ?>">
                            de Asistencias
                        </a>
                    <?php endif ?>
                   <?php if (tienePermiso(Modulo::TAREAS, Permiso::CONSULTAR)): ?>
                        <a href="<?= LOCAL_DIR ?>/Reportes/Tareas"
                            class="<?= ($subModuloActual == "reportetarea") ? "active" : "" ?>">
                            de Tareas
                        </a>
                    <?php endif ?>
                    <?php if (tienePermiso(Modulo::TRABAJADORES, Permiso::CONSULTAR)): ?>
                        <a href="<?= LOCAL_DIR ?>/Reportes/Trabajadores"
                            class="<?= ($subModuloActual == "reportetrabajadores") ? "active" : "" ?>">
                            de Trabajadores
                        </a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endif ?>

    <?php if (tienePermiso("estadisticasasistencias", "consultar")): ?>
        <div class="mx-3 acordeon <?= $moduloActual == "estadisticas" ? "show" : "" ?>">
            <button class="acordeon-toggle sidebar-button
                <?= $moduloActual == "estadisticas" ? "active" : "" ?>">
                <i class="fa-solid fa-chart-line"></i>
                Estadísticas
            </button>
            <div class="acordeon-body">
                <div class="acordeon-items">
                    <?php if (tienePermiso("estadisticasasistencias", "consultar")): ?>
                        <a href="<?= LOCAL_DIR ?>/Estadisticas/Asistencias"
                            class="<?= ($subModuloActual == "estadisticaasistencia") ? "active" : "" ?>">
                            de Asistencias
                        </a>
                    <?php endif ?>
               <?php if (tienePermiso(Modulo::TAREAS, Permiso::CONSULTAR)): ?>
                        <a href="<?= LOCAL_DIR ?>/Estadisticas/Tareas"
                            class="<?= ($subModuloActual == "estadisticatarea") ? "active" : "" ?>">
                            de Tareas
                        </a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endif ?>

    <?php if (tienePermiso(Modulo::USUARIOS, Permiso::CONSULTAR)
        || tienePermiso(Modulo::ROLES, Permiso::CONSULTAR)
        || tienePermiso(Modulo::BITACORA, Permiso::CONSULTAR)): ?>
        <h4>Sistema</h4>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::USUARIOS, Permiso::CONSULTAR)): ?>
        <a href="<?= LOCAL_DIR ?>/Usuarios" class="sidebar-button mx-3
            <?= $moduloActual == "usuarios" ? "active" : "" ?>">
            <i class="fa-solid fa-user"></i>
            Usuarios
        </a>
    <?php endif ?>
    <?php if (tienePermiso(Modulo::ROLES, Permiso::CONSULTAR)
        || tienePermiso(Modulo::BITACORA, Permiso::CONSULTAR)): ?>
        <div class="mx-3 acordeon <?= $moduloActual == "seguridad" ? "show" : "" ?>">
            <button class="acordeon-toggle sidebar-button
                <?= $moduloActual == "seguridad" ? "active" : "" ?>">
                <i class="fa-solid fa-lock"></i>
                Seguridad
            </button>
            <div class="acordeon-body">
                <div class="acordeon-items">
                    <?php if (tienePermiso(Modulo::ROLES, Permiso::CONSULTAR)): ?>
                        <a href="<?= LOCAL_DIR ?>/Seguridad/Roles"
                            class="<?= ($subModuloActual == "roles") ? "active" : "" ?>">
                            Roles y permisos
                        </a>
                    <?php endif ?>
                    <?php if (tienePermiso(Modulo::BITACORA, Permiso::CONSULTAR)): ?>
                        <a href="<?= LOCAL_DIR ?>/Seguridad/Bitacora"
                            class="<?= ($subModuloActual == "bitacora") ? "active" : "" ?>">
                            Bitácora
                        </a>
                    <?php endif ?>
                    <!--  colocar permisos -->
                    <?php if (true/*tienePermiso(Modulo::BITACORA, Permiso::CONSULTAR)*/): ?>
                        <a href="<?= LOCAL_DIR ?>/Seguridad/Respaldo"
                            class="<?= ($subModuloActual == "respaldo") ? "active" : "" ?>">
                            Respaldo BD
                        </a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endif ?>
    <a href="<?= LOCAL_DIR ?>/Ayuda" class="sidebar-button mx-3
        <?= $moduloActual == "ayuda" ? "active" : "" ?>">
        <i class="fa-solid fa-book"></i>
        Manual
    </a>
</div>