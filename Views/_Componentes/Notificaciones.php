<?php
/** @var Notificacion[] */
$notificaciones = $_SESSION['notificaciones'] ?? [];
$notificacionesNuevas = array_filter(
    $notificaciones, fn($notificacion) => $notificacion->getEstado() === EstadoNotif::Pendiente);
$totalNotificaciones = count($notificaciones);
$totalNotificacionesNuevas = count($notificacionesNuevas);
?>

<div class="dropdown" id="btn-notificaciones">
    <button type="button position-relative" class="btn btn-header" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-bell"></i>
        <?php if ($totalNotificacionesNuevas > 0): ?>
            <span id="notif-nuevas-cont" class="position-absolute top-0 start-100 badge rounded-pill bg-danger"
                style="transform: translate(-50%, -20%) !important;">
                <?= $totalNotificacionesNuevas > 99 ? "99+" : $totalNotificacionesNuevas ?>
            </span>
        <?php endif ?>
    </button>
    <ul class="dropdown-menu <?= $totalNotificaciones == 0 ? "pb-0" : "" ?>" style="width: 380px; max-height: 500px; overflow-y: auto; overflow-x: hidden;">
        <div class="position-relative pb-2 px-3 text-center <?= $totalNotificaciones > 0 ? "border-bottom" : "" ?>">
            <span>Tienes <span id="notif-nuevas-total"><?= $totalNotificacionesNuevas ?></span> notificaciones nuevas</span>
            <div class="position-absolute" style="top: 0; right: 8px; cursor: pointer;" onclick="marcarTodasNotificacionesLeidas()">
                <i class="fa-solid fa-check-double" style="color: gray;"></i>
            </div>
        </div>
        <li>
            <?php foreach ($notificaciones as $notificacion): ?>
                <button class="dropdown-item d-flex p-0 position-relative" onclick="marcarNotificacionLeida(<?= $notificacion->id ?>, this)"
                        style="border-radius: 0; border-bottom: 1px solid var(--bs-gray-300);">
                    <div class="m-2 d-flex justify-content-center align-items-center flex-shrink-0"
                        style="width: 40px!important; height: 40px!important; background-color: var(--bs-primary);
                            color: white; border-radius: 50px">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="d-flex flex-column gap-2 py-2 pe-2">
                        <span style="white-space: normal;"><?= $notificacion->getDescripcion() ?></span>
                        <small class="light"><?= $notificacion->tiempoTranscurrido() ?></small>
                    </div>
                    <?php if ($notificacion->getEstado() == EstadoNotif::Pendiente): ?>
                        <div class="notif-dot" style="position: absolute; top: 8px; right: 8px; width: 10px; height: 10px; background-color: red; border-radius: 50%;"></div>
                    <?php endif ?>
                </button>
            <?php endforeach ?>
        </li>
    </ul>
</div>