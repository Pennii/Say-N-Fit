@echo off
where docker >nul 2>nul
if errorlevel 1 (
    echo Docker no esta instalado. Por favor instalalo desde:
    echo https://docs.docker.com/get-docker/
    pause
    exit /b
)

docker compose version >nul 2>nul
if errorlevel 1 (
    echo Docker Compose no esta disponible. Asegurate de tener una version reciente de Docker.
    echo Mas info: https://docs.docker.com/compose/
    pause
    exit /b
)

echo Docker esta instalado. Iniciando despliegue...
docker compose up -d
echo Aplicacion desplegada en https://localhost:443
pause
