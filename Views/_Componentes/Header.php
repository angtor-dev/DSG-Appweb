<header class="main-header">
    <div class="row w-100">
        <div class="col">
            <div class="sidebar-header">
                <a href="<?= LOCAL_DIR ?>/" class="d-flex align-items-center gap-2">
                    <img src="<?= LOCAL_DIR ?>/public/img/logo-white-2.png" alt="">
                    DISEGIS
                </a>
                <div class="sidebar-toggle" id="sidebar-toggle">
                    <i class="fa-solid fa-bars"></i>
                </div>
            </div>
        </div>
        <div class="col header-middle">
            <a href="<?= LOCAL_DIR ?>/" class="d-flex align-items-center gap-2">
                <img src="<?= LOCAL_DIR ?>/public/img/logo-white-2.png" alt="">
                DISEGIS
            </a>
        </div>
        <div class="col d-flex align-items-center justify-content-end">
            <?php renderComponent('Notificaciones') ?>
        </div>
    </div>
</header>