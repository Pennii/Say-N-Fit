<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");
require_once '../modelos/Grupo.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (filter_has_var(INPUT_POST, "eliminar")) {
        $datos = filter_input(INPUT_POST, "eliminar");
        $datos = explode(',', $datos);
        Grupo::eliminarUsuario($datos[0], $datos[1]);
        echo json_encode(["eliminado" => true]);
    } else if (filter_has_var(INPUT_POST, "convertir")) {
        $datos = filter_input(INPUT_POST, "convertir");
        $datos = explode(',', $datos);
        Grupo::convertirLider($datos[0], $datos[1]);
        echo json_encode(["convertido" => true]);
        //Eliminar cookie convertido en el front
    } else {
        $json = file_get_contents("php://input");
        $datos = json_decode($json, true);
        $usuarios = Grupo::listarUsuarios($datos);
        echo json_encode(["usuarios" => $usuarios]);
    }
} else {
    if (filter_input(INPUT_GET, "abandonarGrupo") != null) {
        $grupo = filter_input(INPUT_GET, "abandonarGrupo");
        $usuario = filter_input(INPUT_GET, "nombreUsuario");
        if (!Grupo::abandonarGrupo($usuario, $grupo)) {
            setcookie("errorAbandonar", true, time() + 5, '/');
        }
        echo json_encode(["abandonado" => true]);
    }
}
