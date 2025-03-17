<header class="main-header">
    <div class="row w-100">
        <div class="col">
            <div class="sidebar-header">
                <a href="<?= LOCAL_DIR ?>/" class="d-flex align-items-center gap-2">
                    <img src="<?= LOCAL_DIR ?>/public/img/logo-white-2.png" alt="">
                    DSG
                </a>
                <div class="sidebar-toggle" id="sidebar-toggle">
                    <i class="fa-solid fa-bars"></i>
                </div>
            </div>
        </div>
        <div class="col header-middle">
            <a href="<?= LOCAL_DIR ?>/" class="d-flex align-items-center gap-2">
                <img src="<?= LOCAL_DIR ?>/public/img/logo-white-2.png" alt="">
                DSG
            </a>
        </div>
        <div class="col d-flex align-items-center justify-content-end">
            <div class="dropdown">
                <button type="button" class="btn btn-header" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-bell"></i>
                </button>
                <ul class="dropdown-menu" style="width: 280px;">
                    <div class="pb-2 px-3 border-bottom text-center">
                        <span>Tienes 2 Notificaciones</span>
                    </div>
                    <li>
                        <a class="dropdown-item d-flex p-0" href="#">
                            <div class="m-2 d-flex justify-content-center align-items-center"
                                style="width: 40px; height: 40px; background-color: var(--bs-primary);
                                    color: white; border-radius: 50px">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="d-flex flex-column gap-2 py-2">
                                <span>Notificacion de prueba</span>
                                <small class="light">Hace 5 minutos</small>
                            </div>
                        </a>
                        <a class="dropdown-item d-flex p-0" href="#">
                            <div class="m-2 d-flex justify-content-center align-items-center"
                                style="width: 40px; height: 40px; background-color: var(--bs-primary);
                                    color: white; border-radius: 50px">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="d-flex flex-column gap-2 py-2">
                                <span>Otra notificacion</span>
                                <small class="light">Hace 2 horas</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>