<?php
require_once '../modelos/Grupo.php';
require_once '../modelos/Usuario.php';

//Si la solicitud se realiza con get se devuelven los grupos que puede ver un usuario como objeto json
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    header('Content-Type: application/json');
    $usuario = $_COOKIE["nombreUsuario"];
    $datos = Grupo::verGruposPorUsuario($usuario);
    echo json_encode(['grupos' => $datos]);
//Si la solicitud fue con post entonces hay que verificar si se esta creando un grupo, modificandolo, o un usuario esta buscando un grupo
} else {
    $datos = filter_input_array(INPUT_POST);
    //Si en los datos se encuentra la opcion crear es porque se esta insertando un grupo nuevo
    if (filter_has_var(INPUT_POST, "crear")) {
        $fecha = date('Y-m-d');
        $lider = Usuario::verUsuario($datos['lider']);

        $grupo = new Grupo($datos["nombre"], $datos["clave"], $fecha, $lider["alias"]);
        if ($grupo->crearGrupo($lider["alias"])) {
            header('Location: ../vistas/grupos.html');
            setcookie('crearGrupo', false, time() - 1);
        } else {
            header('Location: ../vistas/grupos_configuracion.html');
        }
    //Si se encuentra la opcion buscar se inserta el usuario en el grupo
    } else if (filter_has_var(INPUT_POST, "buscar")) {
        Grupo::encontrarGrupo($datos["clave"], $datos["usuario"]);
        header('Location: ../vistas/grupos.html');
    }
}
