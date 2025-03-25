<?php
require_once '../modelos/Rutina.php';
require_once '../modelos/funciones.php';
// Si se encuentra ejercicios en el post es porque se esta creando una rutina
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
    // Si no se encuentra ejercicios en el post se esta solicitando informacion de las rutinas
} else if (filter_has_var(INPUT_COOKIE, "nombreUsuario")) {
    // Si se encuentra rutina en las cookies se esta solicitando los ejercicios de una rutina
    if (filter_has_var(INPUT_COOKIE, "rutina")) {
        $listaEjercicios = Rutina::obtenerEjercicios(filter_input(INPUT_COOKIE, "nombreUsuario"), filter_input(INPUT_COOKIE, "rutina"));
        setcookie("rutina", "", time() - 1, "/");
        echo json_encode(["ejercicios" => $listaEjercicios]);
        // Si se encuentra el parametro editarRutina en las cookies se esta editando una rutina, donde se veran sus datos y luego se cambiaran
    } else if (filter_has_var(INPUT_COOKIE, "editarRutina")) {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $rutina = Rutina::verRutina(filter_input(INPUT_COOKIE, "editarRutina"), filter_input(INPUT_COOKIE, "nombreUsuario"));
            $listaEjercicios = Rutina::obtenerEjercicios(filter_input(INPUT_COOKIE, "nombreUsuario"), filter_input(INPUT_COOKIE, "editarRutina"));
            echo json_encode(["datosRutina" => $rutina, "datosEjercicios" => $listaEjercicios]);
        } else if (filter_has_var(INPUT_POST, "actualizarRutina")) {
            $datos = filter_input_array(INPUT_POST);
            $dias = '';
            foreach ($datos['dias'] as $dia) {
                $dias .= obtenerDia($dia) . ";";
            }
            $dias = substr($dias, 0, strlen($dias) - 1);
            $rutina = new Rutina(filter_input(INPUT_COOKIE, "editarRutina"), filter_input(INPUT_COOKIE, "nombreUsuario"), $dias, $datos["nombre"]);
            $rutina->actualizarRutina($datos["ejerciciosActualizar"]);
            setcookie("editarRutina", "", time() - 1, "/");
            header("Location: ../vistas/rutinas.html");
            // Si no se encuentran los parametros anteriores es porque se esta accediendo mediante un fetch
        } else {
            $json = file_get_contents('php://input');
            $datos = json_decode($json, true);
            // Si se encuentra el parametro ejercicio en los datos se esta eliminando un ejercicio de la rutina
            if (isset($datos["ejercicio"])) {
                $eliminarEjercicio = Rutina::quitarEjercicio($datos["ejercicio"], filter_input(INPUT_COOKIE, "nombreUsuario"), filter_input(INPUT_COOKIE, "editarRutina"));
                if ($eliminarEjercicio == -1) {
                    setcookie("editarRutina", "", time() - 1, "/");
                }
                echo json_encode(["eliminado" => $eliminarEjercicio]);
            }
        }
    } else {
        // Si se envia una solicitud por fetch sin editar una rutina es porque se esta eliminando una rutina
        if ($json = file_get_contents('php://input')) {
            $datos = json_decode($json, true);
            $eliminarRutina = Rutina::eliminarRutina(filter_input(INPUT_COOKIE, "nombreUsuario"), $datos["rutinaEliminar"]);
            echo json_encode(["eliminado" => $eliminarRutina]);
        } else {
            $usuario = filter_input(INPUT_COOKIE, 'nombreUsuario');
            $rutinas = Rutina::obtenerRutinas($usuario);
            echo json_encode(["rutinas" => $rutinas]);
        }
    }
} else {
    header("Location: ../index.html");
}
