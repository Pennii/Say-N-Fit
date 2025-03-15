<?php
//Se eliminan los datos de sesion y se redirige al usuario a la pagina de inicio
session_start();
session_destroy();
setcookie("nombreUsuario", "", time() - 1, '/');
header("Location: ../index.html");