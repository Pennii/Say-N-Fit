<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once '../modelos/Usuario.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datos = filter_input_array(INPUT_POST);
    //Si datos no es un array entonces se muestran los datos del usuario que se paso como cadena
    if (!is_array($datos)) {
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);
        $datos = Usuario::verUsuario($datos);
        echo json_encode(["datos" => $datos]);
    } else {
        //Si datos es un array entonces se actualizan los datos del usuario
        $usuario = new Usuario($datos["alias"], $datos["nombre"], $datos["nacimiento"], $datos["peso"], $datos["clave"]);
        $resultado = $usuario->actualizarDatos($datos["original"]);
        if ($resultado) {
            $imagen = $_FILES["imagen"];
            // La variable imagen es un array, si no se introdujo ninguna imagen el valor de nombre estara vacio, de esa forma sabre si hay una imagen o no
            if ($imagen["name"]) {
                //Con esto elimino la imagen actual, de esta forma si el usuario cambia el nombre o la imagen, puedo actualizar su direccion
                unlink("../iconos_usuarios/" . $datos["original"] . ".jpeg");
                // Si hay una imagen se crea en la ruta del usuario
                $ubicacion = "../iconos_usuarios/" . $usuario->getAlias() . ".jpeg";
                move_uploaded_file($imagen["tmp_name"], $ubicacion);
            } else {
                //Se renombra su imagen de usuario por si cambio el nombre de usuario pero no su imagen
                rename("../iconos_usuarios/" . $datos["original"] . ".jpeg", "../iconos_usuarios/" . $usuario->getAlias() . ".jpeg");
            }
            $_SESSION['usuario'] = $usuario->getAlias();
            setcookie("nombreUsuario", $_SESSION['usuario'], time() + 3600 * 12, '/');
            $nuevoAlias = $_SESSION["usuario"];
        }
        //Se envia por json el resultado de la actualizacion
        echo json_encode(["nuevoUsuario" => $nuevoAlias]);
    }
}
