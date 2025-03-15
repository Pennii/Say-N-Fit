<?php
require_once '../modelos/Usuario.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    //Si datos no es un array entonces se muestran los datos del usuario que se paso como cadena
    if (!is_array($datos)) {
        $datos = Usuario::verUsuario($datos);
        echo json_encode(["datos" => $datos]);
    } else {
        //Si datos es un array entonces se actualizan los datos del usuario
        $usuario = new Usuario($datos["alias"], $datos["nombre"], $datos["nacimiento"], $datos["peso"], $datos["clave"]);
        $resultado = $usuario->actualizarDatos($datos["original"]);
        if ($resultado) {
            //Se renombra su imagen de usuario
            rename("../iconos_usuarios/".$datos["original"].".jpeg","../iconos_usuarios/".$usuario->getAlias().".jpeg");
            $_SESSION['usuario'] = $usuario->getAlias();
            setcookie("nombreUsuario", $_SESSION['usuario'], time() + 3600 * 12, '/');
        }
        //Se envia por json el resultado de la actualizacion
        echo json_encode(["actualizado" => $resultado]);
    }
}
