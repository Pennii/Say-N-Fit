<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once '../modelos/Usuario.php';

$datos = filter_input_array(INPUT_POST);

$usuario = new Usuario($datos["usuario"], $datos["nombre"], $datos["nacimiento"], $datos["peso"], $datos["clave"]);

// Si verUsuario devuelve un false es porque el usuario no existe y podemos insertarlo
if (!Usuario::verUsuario($usuario->getAlias())) {
    if ($usuario->insertarUsuario()) {
        $imagen = $_FILES["imagen"];
        // La variable imagen es un array, si no se introdujo ninguna imagen el valor de nombre estara vacio, de esa forma sabre si hay una imagen o no
        if ($imagen["name"]) {
            // Si hay una imagen se crea en la ruta del usuario
            $ubicacion = "../iconos_usuarios/" . $usuario->getAlias() . ".jpeg";
            move_uploaded_file($imagen["tmp_name"], $ubicacion);
        } else {
            //Si no hay una imagen se copia la predeterminada a la ruta del usuario
            $ubicacion = "../iconos_usuarios/" . $usuario->getAlias() . ".jpeg";
            copy("../iconos_usuarios/imagen_predeterminada.png", $ubicacion);
        }
        session_start();
        $_SESSION['usuario'] = $usuario->getAlias();
        echo json_encode(['ok' => 'ok']);
    }
} else {
    echo json_encode(['error' => 'El usuario ya existe']);
}
