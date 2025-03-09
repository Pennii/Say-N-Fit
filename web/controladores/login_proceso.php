<?php
require_once '../modelos/Usuario.php';
require_once '../modelos/funciones.php';
session_start();

if (isset($_SESSION['usuario'])) {
    setcookie("nombreUsuario", $_SESSION['usuario'], time() + 3600 * 12, '/');
    header('Location: ../vistas/inicio.html');
}
if (filter_has_var(INPUT_POST, "registrarse")) {
    header("Location: ../vistas/registro.html");
} else if (filter_has_var(INPUT_POST, "ingresar")) {
    $datos = filter_input_array(INPUT_POST);
    $usuario = sanear_texto($datos["nombre"]);
    $clave = sanear_texto($datos["clave"]);
    $correcto = Usuario::logear($usuario, $clave);
    if ($correcto) {
        $_SESSION['usuario'] = $usuario;
        setcookie("nombreUsuario", $_SESSION['usuario'], time() + 3600 * 12, '/');
        header('Location: ../vistas/inicio.html');
    }else{
        setcookie('usuarioIncorrecto', true, time()+1, '/');
        header("Location: ../index.html");
    }
}
