<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="<?= LOCAL_DIR ?>/public/lib/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            font-family: sans-serif;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .background-icon {
            position: fixed; /* Fija el icono al fondo */
            top: 50%;
            left: 50%;
            width: 70%;
            height: 70%;
            transform: translate(-50%, -50%);
            z-index: -1; /* Coloca el icono detrás del contenido */
            fill: #f2f2f2; /* Color de relleno del icono (gris muy claro) */
            opacity: 1; /* Opacidad del icono */
        }
        .container {
            text-align: center;
        }
        .icon-background {
            margin: 12px auto;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 900;
        }
        .error-code {
            font-size: 100px;
            color: #666; /* Gris más oscuro */
        }
        h1 {
            font-size: 48px;
            color: #333; /* Gris oscuro */
        }
        p {
            font-size: 20px;
            color: #666;
        }
        .trace-info {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            text-align: center;
            font-size: 12px;
            opacity: .3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-background">
            <span class="error-code">404</span>
        </div>
        <h1>Página no encontrada</h1>
        <p>
            Parace que intentas acceder a un recurso que no existe. <br>
            Valida la dirección e intenta de nuevo.
        </p>
        <a href="<?= LOCAL_DIR ?>/" class="btn btn-primary">Volver al Inicio</a>
    </div>

    <div class="trace-info">C: <?= $controllerPathCopy ?> &nbsp;&nbsp;&nbsp;&nbsp; A: <?= $controllerNameCopy ?></div>
</body>
</html>