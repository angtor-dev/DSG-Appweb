<head>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-background">
            <span class="error-code">403</span>
        </div>
        <h1>Acceso denegado</h1>
        <p>No posees permiso para acceder a este recurso.</p>
    </div>
</body>