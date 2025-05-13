<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once '../modelos/Usuario.php';
require_once '../modelos/funciones.php';
session_start();
if (filter_has_var(INPUT_POST, "nombre")) {
    $datos = filter_input_array(INPUT_POST);
    $usuario = sanear_texto($datos["nombre"]);
    $clave = sanear_texto($datos["clave"]);
    $correcto = Usuario::logear($usuario, $clave);
    //Si el usuario puede logear se crean los datos de la sesion y se redirige al usuario a la pagina de bienvenida
    if ($correcto) {
        $_SESSION['usuario'] = $usuario;
        echo json_encode(["usuario" => $usuario]);
    } else {
        echo json_encode(["error" => "Usuario o clave incorrectos"]);
    }
} else {
    echo json_encode(["error" => "No se ha enviado el formulario"]);
}
