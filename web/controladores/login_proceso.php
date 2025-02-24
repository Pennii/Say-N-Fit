<?php
require_once '../modelos/Usuario.php';
session_start();

if (isset($_SESSION['usuario'])) {
    setcookie("nombreUsuario", $_SESSION['usuario'], time()+3600*12, '/');
    header('Location: ../vistas/inicio.html');
}
if (filter_has_var(INPUT_POST,"registrarse")) {
    header("Location: ../vistas/registro.html");
}else if (filter_has_var(INPUT_POST, "ingresar") ) {
    
}