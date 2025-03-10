<?php
require_once '../modelos/Usuario.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    if (!is_array($datos)) {
        $datos = Usuario::verUsuario($datos);
        echo json_encode(["datos" => $datos]);
    } else {
        $usuario = new Usuario($datos["alias"], $datos["nombre"], $datos["nacimiento"], $datos["peso"], $datos["clave"]);
        $resultado = $usuario->actualizarDatos($datos["original"]);
        if ($resultado) {
            rename("../iconos_usuarios/".$datos["original"].".jpeg","../iconos_usuarios/".$usuario->getAlias().".jpeg");
            $_SESSION['usuario'] = $usuario->getAlias();
            setcookie("nombreUsuario", $_SESSION['usuario'], time() + 3600 * 12, '/');
        }
        echo json_encode(["actualizado" => $resultado]);
    }
}
