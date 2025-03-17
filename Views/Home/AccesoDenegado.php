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
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="background-icon"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M186.1 52.1C169.3 39.1 148.7 32 127.5 32C74.7 32 32 74.7 32 127.5l0 6.2c0 15.8 3.7 31.3 10.7 45.5l23.5 47.1c4.5 8.9 7.6 18.4 9.4 28.2l36.7 205.8c2 11.2 11.6 19.4 22.9 19.8s21.4-7.4 24-18.4l28.9-121.3C192.2 323.7 207 312 224 312s31.8 11.7 35.8 28.3l28.9 121.3c2.6 11.1 12.7 18.8 24 18.4s20.9-8.6 22.9-19.8l36.7-205.8c1.8-9.8 4.9-19.3 9.4-28.2l23.5-47.1c7.1-14.1 10.7-29.7 10.7-45.5l0-2.1c0-55-44.6-99.6-99.6-99.6c-24.1 0-47.4 8.8-65.6 24.6l-3.2 2.8 19.5 15.2c7 5.4 8.2 15.5 2.8 22.5s-15.5 8.2-22.5 2.8l-24.4-19-37-28.8z"/></svg>

    <div class="container">
        <div class="icon-background">
            <span class="error-code">403</span>
        </div>
        <h1>Acceso denegado</h1>
        <p>No posees permiso para acceder a este recurso.</p>
    </div>
</body>