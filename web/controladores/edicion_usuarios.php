<?php
require_once '../modelos/Grupo.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (filter_has_var(INPUT_POST, "eliminar")) {
        $datos = filter_input(INPUT_POST, "eliminar");
        $datos = explode(',', $datos);
        Grupo::eliminarUsuario($datos[0], $datos[1]);
        header("Location: ../vistas/editar_usuarios.html");
    }else if(filter_has_var(INPUT_POST, "convertir")){
        $datos = filter_input(INPUT_POST, "convertir");
        $datos = explode(',', $datos);
        Grupo::convertirLider($datos[0], $datos[1]);
        setcookie("editarUsuarios", false, time() - 1, '/');
        header("Location: ../vistas/editar_usuarios.html");
    } else {
        $json = file_get_contents("php://input");
        $datos = json_decode($json, true);
        $usuarios = Grupo::listarUsuarios($datos);
        echo json_encode(["usuarios" => $usuarios]);
    }
}
