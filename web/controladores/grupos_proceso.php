<?php
require_once '../modelos/Grupo.php';
require_once '../modelos/Usuario.php';
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    header('Content-Type: application/json');

    $datos = Grupo::verGrupos();
    echo json_encode(['grupos' => $datos]);
} else {
    $datos = filter_input_array(INPUT_POST);
    $fecha = date('Y-m-d');
    $lider = Usuario::verUsuario($datos['lider']);

    $grupo = new Grupo($datos["nombre"], $datos["clave"], $fecha, $lider["alias"]);
    if ($grupo->crearGrupo()) {
        header('Location: ../vistas/grupos.html');
        setcookie('crearGrupo', false, time() - 1);
    }else{
        header('Location: ../vistas/grupos_configuracion.html');
    }
}
