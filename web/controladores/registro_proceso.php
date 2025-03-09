<?php
require_once '../modelos/Usuario.php';

$datos = filter_input_array(INPUT_POST);

$usuario = new Usuario($datos["usuario"], $datos["nombre"], $datos["nacimiento"], $datos["peso"], $datos["clave"]);

if (!Usuario::verUsuario($usuario->getAlias())) {
    if ($usuario->insertarUsuario()) {
        $imagen = $_FILES["imagen"];
        if ($imagen) {
            $tipo = $imagen["type"];
            switch ($tipo) {
                case 'image/jpg':
                    $extension = "jpg";
                    break;
                case 'image/png':
                    $extension = "png";
                    break;
                case 'image/jpeg':
                    $extension = "jpeg";
                    break;
            }
            if (isset($extension)) {
                $ubicacion = "../iconos_usuarios/" . $usuario->getAlias() . ".$extension";
                move_uploaded_file($imagen["tmp_name"], $ubicacion);
            }
        }
        session_start();
        $_SESSION['usuario'] = $usuario->getAlias();
        header("Location: ./login_proceso.php");
    }

} else {
    setcookie("usuarioExistente", "El usuario ya existe", time() + 20, '/');
    header("Location: ../vistas/registro.html");
}
