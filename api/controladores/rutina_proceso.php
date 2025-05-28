<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once '../modelos/Rutina.php';
require_once '../modelos/funciones.php';

$json = file_get_contents('php://input');
$datos = json_decode($json, true);
$nombreUsuario = $datos["nombreUsuario"] ?? filter_input(INPUT_POST, "nombreUsuario");
// Si se encuentra ejercicios en el post es porque se esta creando una rutina
if (filter_has_var(INPUT_POST, "ejercicios")) {
    $datos = filter_input_array(INPUT_POST);
    $ejercicios = explode(';', $datos["ejercicios"]);
    $codigoActual = Rutina::consultarCodigoActual($nombreUsuario);
    $codigoRutina = $codigoActual ? ++$codigoActual["CODIGO"] : 1;
    $dias = '';
    foreach ($datos['dias'] as $dia) {
        $dias .= obtenerDia($dia) . ";";
    }
    $dias = substr($dias, 0, strlen($dias) - 1);
    $rutina = new Rutina($codigoRutina, $nombreUsuario, $dias, $datos["nombre"]);
    $rutina->crearRutina();
    foreach ($ejercicios as $ejercicio) {
        $rutina->almacenarEjercicio($ejercicio);
    }
    $estado = "ok";
    echo json_encode(["estado" => $estado, "datos" => $datos, $codigoRutina, $dias, $datos["nombre"], $nombreUsuario, "rutina" => $rutina->nombre]);
    // Si no se encuentra ejercicios en el post se esta solicitando informacion de las rutinas
} else if (filter_has_var(INPUT_POST, "actualizarRutina")) {
    $datos = filter_input_array(INPUT_POST);
    $nombreUsuario = $datos["usuario"];
    $dias = '';
    foreach ($datos['dias'] as $dia) {
        $dias .= obtenerDia($dia) . ";";
    }
    $dias = substr($dias, 0, strlen($dias) - 1);
    $rutina = new Rutina($datos["editarRutina"], $nombreUsuario, $dias, $datos["nombre"]);
    $actualizado = $rutina->actualizarRutina($datos["ejerciciosActualizar"]);
    echo json_encode(["actualizado" => $actualizado]);
} else {
    if (isset($datos["nombreUsuario"])) {
        // Si se encuentra rutina en las cookies se esta solicitando los ejercicios de una rutina
        if (isset($datos["rutina"])) {
            $listaEjercicios = Rutina::obtenerEjercicios($nombreUsuario, $datos["rutina"]);
            echo json_encode(["ejercicios" => $listaEjercicios]);
            // Si se encuentra el parametro editarRutina en las cookies se esta editando una rutina, donde se veran sus datos y luego se cambiaran
        } else if (isset($datos["editarRutina"])) {
            if (isset($datos["verRutina"])) {
                $rutina = Rutina::verRutina($datos["editarRutina"], $nombreUsuario);
                $listaEjercicios = Rutina::obtenerEjercicios($nombreUsuario, $datos["editarRutina"]);
                echo json_encode(["datosRutina" => $rutina, "datosEjercicios" => $listaEjercicios]);
            } else if (isset($datos["ejercicio"])) { {
                    // Si se encuentra el parametro ejercicio en los datos se esta eliminando un ejercicio de la rutina
                    $eliminarEjercicio = Rutina::quitarEjercicio($datos["ejercicio"], $nombreUsuario, $datos["editarRutina"]);
                    echo json_encode(["eliminado" => $eliminarEjercicio]);
                }
            }
        } else if (isset($datos["eliminar"])) {
            $eliminarRutina = Rutina::eliminarRutina($nombreUsuario, $datos["eliminar"]);
            echo json_encode(["eliminado" => $eliminarRutina]);
        } else {
            $rutinas = Rutina::obtenerRutinas($nombreUsuario);
            echo json_encode(["rutinas" => $rutinas]);
        }
    }
}
