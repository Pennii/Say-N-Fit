<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");
require_once '../modelos/Grupo.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Si se encuentra eliminar en el post se esta eliminando un usuario del grupo
    if (filter_has_var(INPUT_POST, "eliminar")) {
        $datos = filter_input(INPUT_POST, "eliminar");
        $datos = explode(',', $datos);
        Grupo::eliminarUsuario($datos[0], $datos[1]);
        echo json_encode(["eliminado" => true]);
        // Si se encuentra convertir, se esta cambiando el lider de grupo
    } else if (filter_has_var(INPUT_POST, "convertir")) {
        $datos = filter_input(INPUT_POST, "convertir");
        $datos = explode(',', $datos);
        Grupo::convertirLider($datos[0], $datos[1]);
        echo json_encode(["convertido" => true]);
    } else {
        // Si no hay ninguno de esos entonces se pide la lista de usuarios de un grupo
        $json = file_get_contents("php://input");
        $datos = json_decode($json, true);
        $usuario = $datos["usuario"];
        $usuarios = Grupo::listarUsuarios($datos["grupo"]);
        $lider = Grupo::verificarLider($datos["grupo"], $usuario);
        echo json_encode(["usuarios" => $usuarios, "lider" => $lider]);
    }
} else {
    //La opcion en la que se abandona un grupo desde el panel de grupos
    if (filter_input(INPUT_GET, "abandonarGrupo") != null) {
        $grupo = filter_input(INPUT_GET, "abandonarGrupo");
        $usuario = filter_input(INPUT_GET, "nombreUsuario");
        if (!Grupo::abandonarGrupo($usuario, $grupo)) {
            setcookie("errorAbandonar", true, time() + 5, '/');
        }
        echo json_encode(["abandonado" => true]);
    }
}
