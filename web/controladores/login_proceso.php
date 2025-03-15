<?php
require_once '../modelos/Usuario.php';
require_once '../modelos/funciones.php';
session_start();
//Si $_SESSION['usuario'] tiene valor entonces el usuario ya ha iniciado sesion y pasa directamente al inicio
if (isset($_SESSION['usuario'])) {
    setcookie("nombreUsuario", $_SESSION['usuario'], time() + 3600 * 12, '/');
    header('Location: ../vistas/inicio.html');
}
//Si se ha elegido registrar a un usuario entonces se redirige al controlador de registro
if (filter_has_var(INPUT_POST, "registrarse")) {
    header("Location: ../vistas/registro.html");
//Si se ha elegido ingresar entonces se inicia la sesion del usuario
} else if (filter_has_var(INPUT_POST, "ingresar")) {
    $datos = filter_input_array(INPUT_POST);
    $usuario = sanear_texto($datos["nombre"]);
    $clave = sanear_texto($datos["clave"]);
    $correcto = Usuario::logear($usuario, $clave);
    //Si el usuario puede logear se crean los datos de la sesion y se redirige al usuario a la pagina de bienvenida
    if ($correcto) {
        $_SESSION['usuario'] = $usuario;
        setcookie("nombreUsuario", $_SESSION['usuario'], time() + 3600 * 12, '/');
        header('Location: ../vistas/inicio.html');
    }else{
        setcookie('usuarioIncorrecto', true, time()+1, '/');
        header("Location: ../index.html");
    }
}
