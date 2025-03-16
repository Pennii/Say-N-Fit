<?php
require_once '../modelos/Grupo.php';
require_once '../modelos/Usuario.php';

//Si la solicitud se realiza con get se devuelven los grupos que puede ver un usuario como objeto json
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    header('Content-Type: application/json');
    $usuario = $_COOKIE["nombreUsuario"];
    $datos = Grupo::verGruposPorUsuario($usuario);
    echo json_encode(['grupos' => $datos]);
    //Si la solicitud fue con post entonces hay que verificar si se esta creando un grupo, modificandolo, o un usuario esta buscando un grupo
} else {
    $datos = filter_input_array(INPUT_POST);
    //Si en los datos se encuentra la opcion crear es porque se esta insertando un grupo nuevo
    if (filter_has_var(INPUT_POST, "crear")) {
        $lider = Usuario::verUsuario($datos['lider']);

        $grupo = new Grupo($datos["nombre"], $datos["clave"], $lider["alias"]);
        if ($lider["alias"] && $grupo->crearGrupo($lider["alias"])) {
            $imagen = $_FILES["imagen"];
            // La variable imagen es un array, si no se introdujo ninguna imagen el valor de nombre estara vacio, de esa forma sabre si hay una imagen o no
            if ($imagen["name"]) {
                // Si hay una imagen se crea en la ruta del usuario
                $ubicacion = "../iconos_grupos/" . $grupo->getClave() . ".jpeg";
                move_uploaded_file($imagen["tmp_name"], $ubicacion);
            } else {
                //Si no hay una imagen se copia la predeterminada a la ruta del usuario
                $ubicacion = "../iconos_grupos/" . $grupo->getClave() . ".jpeg";
                copy("../iconos_grupos/imagen_predeterminada.png", $ubicacion);
            }
            setcookie("crearGrupo", false, time() - 1, '/');
            header('Location: ../vistas/grupos.html');
        } else {
            setcookie("grupoExistente", true, time() + 20, "/");
            header('Location: ../vistas/grupos_configuracion.html');
        }
        //Si se encuentra la opcion buscar se inserta el usuario en el grupo
    } else if (filter_has_var(INPUT_POST, "buscar")) {
        Grupo::encontrarGrupo($datos["clave"], $datos["usuario"]);
        header('Location: ../vistas/grupos.html');
    //Si se encuentra confirmarCambios entonces actualizamos los datos de un grupo
    } else if (filter_has_var(INPUT_POST, "confirmarCambios")) {
        $grupo = new Grupo($datos["nombre"], $datos["clave"], $datos["lider"]);
        $grupo->actualizarDatos();
        header('Location: ../vistas/grupos.html');
    } else {
        $json =  file_get_contents('php://input');
        $datos = json_decode($json, true);
        //Si solo se pasa un dato es porque se quiere editar un grupo, si se pasan mas es para confirmar si el usuario que ve el grupo es lider o no
        if (count($datos) > 1) {
            $lider = Grupo::verificarLider($datos["grupo"], $datos["usuario"]);
            echo json_encode(["lider" => $lider]);
        } else {
            $grupo = Grupo::verGrupo($datos["grupo"]);
            $usuarios = Grupo::listarUsuarios($datos["grupo"]);
            echo json_encode(["grupo" => $grupo, "usuarios" => $usuarios]);
        }
    }
}
