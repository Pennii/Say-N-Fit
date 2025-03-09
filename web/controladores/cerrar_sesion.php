<?php
session_start();
session_destroy();
setcookie("nombreUsuario", "", time() - 1, '/');
header("Location: ../index.html");