<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= LOCAL_DIR ?>/public/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= LOCAL_DIR ?>/public/lib/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= LOCAL_DIR ?>/public/lib/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= LOCAL_DIR ?>/public/lib/DataTables/datatables.min.css">
    <link rel="stylesheet" href="<?= LOCAL_DIR ?>/public/lib/toastify/toastify.min.css">
    <link rel="stylesheet" href="<?= LOCAL_DIR ?>/public/lib/select2/select2.min.css">
    <link rel="stylesheet" href="<?= LOCAL_DIR ?>/public/lib/select2/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="<?= LOCAL_DIR ?>/public/css/utilities.css">
    <link rel="stylesheet" href="<?= LOCAL_DIR ?>/public/css/main.css">
    <?php imprimirEstilos(); ?>
    <script>
        <?php require_once 'public/js/constantes.php'; ?>
    </script>
<script src="<?= LOCAL_DIR ?>/public/lib/jquery-3.7.1.min.js"></script>
<script src="<?= LOCAL_DIR ?>/public/js/head.js"></script>

<title><?= APP_NAME ?></title>
</head>
<body>
    <!-- Menu Lateral -->
    <?php require "Views/_Componentes/MenuLateral.php" ?>

    <!-- Header -->
    <?php require "Views/_Componentes/Header.php" ?>

    <!-- Contenido Principal -->
    <main class="main-content">
        <?= $GLOBALS['view'] ?>
    </main>
    <script src="<?= LOCAL_DIR ?>/public/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= LOCAL_DIR ?>/public/lib/DataTables/datatables.min.js"></script>
    <script src="<?= LOCAL_DIR ?>/public/lib/toastify/toastify.min.js"></script>
    <script src="<?= LOCAL_DIR ?>/public/lib/select2/select2.full.min.js"></script>
    <script src="<?= LOCAL_DIR ?>/public/js/main.js"></script>
    <script src="<?= LOCAL_DIR ?>/public/js/validaciones/validaciones.js"></script>
    <script src="<?= LOCAL_DIR ?>/public/js/utilities.js"></script>
    
    <script src="<?= LOCAL_DIR ?>/public/lib/jspdf.umd.min.js"></script>
    <script src="<?= LOCAL_DIR ?>/public/lib/jspdf.plugin.autotable.min.js"></script>
    <script src="<?= LOCAL_DIR ?>/public/lib/html2canvas.min.js"></script>

    <?php imprimirLibs(); ?>

    <!-- Agrega scripts adicionales -->
    <?php imprimirScripts(); ?>
    
    <!-- Alertas -->
    <?php if (!empty($_SESSION['exitos'])): ?>
        <?php foreach ($_SESSION['exitos'] as $mensaje): ?>
            <script>
                mostrarExito("<?= $mensaje ?>")
            </script>
        <?php endforeach ?>
        <?php unset($_SESSION['exitos']) ?>
    <?php endif ?>

    <?php if (!empty($_SESSION['errores'])): ?>
        <?php foreach ($_SESSION['errores'] as $mensaje): ?>
            <script>
                mostrarError("<?= $mensaje ?>")
            </script>
        <?php endforeach ?>
        <?php unset($_SESSION['errores']) ?>
    <?php endif ?>
    <?php if (!empty($_SESSION['consoleError'])): ?>
        <script>
            console.error("Error en consola");
            console.log("lista errores",['<?php echo implode(',', $_SESSION['consoleError']) ?>']);
        </script>
        <?php unset($_SESSION['consoleError']) ?>
    <?php endif ?>
</body>
</html>