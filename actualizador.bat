@echo off
REM Cambia el directorio actual al directorio donde se encuentra este script
pushd "%~dp0"

echo.
echo ==========================================================
echo   ACTUALIZANDO DIGESIS A LA ULTIMA VERSION
echo ==========================================================
echo.
echo   (No cierre esta ventana hasta que se le indique)
echo   Paso 1 de 2: Descargando la nueva version de la aplicacion...

REM Ejecuta el comando git pull
git pull > nul 2>&1

echo   Paso 2 de 2: Instalando archivos necesarios...

REM Ejecuta composer install en modo de produccion
CALL composer install --no-dev > nul 2>&1

echo.
echo ==========================================================
echo   LA APLICACION SE HA ACTUALIZADO CORRECTAMENTE.
REM Obtener la version de Git y guardarla en una variable
FOR /F "delims=" %%i IN ('git describe --tags --always 2^>NUL') DO SET GIT_VERSION=%%i

echo   Version actual: %GIT_VERSION%
echo ==========================================================
echo.

popd
echo   Presiona cualquier tecla para cerrar esta ventana.
pause