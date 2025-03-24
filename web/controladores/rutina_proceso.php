<?php
require_once '../modelos/Rutina.php';
require_once '../modelos/funciones.php';
if (filter_has_var(INPUT_POST, "ejercicios")) {
    $datos = filter_input_array(INPUT_POST);
    $ejercicios = explode(';', $datos["ejercicios"]);
    $usuario = filter_input(INPUT_COOKIE, 'nombreUsuario');
    $codigoActual = Rutina::consultarCodigoActual($usuario);
    $codigoRutina = $codigoActual ? ++$codigoActual["CODIGO"] : 1;
    $dias = '';
    foreach ($datos['dias'] as $dia) {
        $dias .= obtenerDia($dia) . ";";
    }
    $dias = substr($dias, 0, strlen($dias) - 1);
    $rutina = new Rutina($codigoRutina, $usuario, $dias, $datos["nombre"]);
    $rutina->crearRutina();
    foreach ($ejercicios as $ejercicio) {
        $rutina->almacenarEjercicio($ejercicio);
    }
    header("Location: ../vistas/rutinas.html");
} else if (filter_has_var(INPUT_COOKIE, "nombreUsuario")) {
    if (filter_has_var(INPUT_COOKIE, "rutina")) {
        $listaEjercicios = Rutina::obtenerEjercicios(filter_input(INPUT_COOKIE, "nombreUsuario"), filter_input(INPUT_COOKIE, "rutina"));
        setcookie("rutina", "", time() - 1, "/");
        echo json_encode(["ejercicios" => $listaEjercicios]);
    } else if (filter_has_var(INPUT_COOKIE, "editarRutina")) {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $rutina = Rutina::verRutina(filter_input(INPUT_COOKIE, "editarRutina"), filter_input(INPUT_COOKIE, "nombreUsuario"));
            $listaEjercicios = Rutina::obtenerEjercicios(filter_input(INPUT_COOKIE, "nombreUsuario"), filter_input(INPUT_COOKIE, "editarRutina"));
            setcookie("editarRutina", "", time() - 1, "/");
            echo json_encode(["datosRutina" => $rutina, "datosEjercicios" => $listaEjercicios]);
        } else {
            $json = file_get_contents('php://input');
            $datos = json_decode($json, true);
            $eliminarEjercicio = Rutina::quitarEjercicio($datos["ejercicio"], filter_input(INPUT_COOKIE, "nombreUsuario"), filter_input(INPUT_COOKIE, "editarRutina"));
            setcookie("editarRutina", "", time() - 1, "/");
            echo json_encode(["eliminado" => $eliminarEjercicio]);
        }
    } else {
        $usuario = filter_input(INPUT_COOKIE, 'nombreUsuario');
        $rutinas = Rutina::obtenerRutinas($usuario);
        echo json_encode(["rutinas" => $rutinas]);
    }
} else {
    header("Location: ../index.html");
}
