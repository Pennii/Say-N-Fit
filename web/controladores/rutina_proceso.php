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
    $usuario = filter_input(INPUT_COOKIE, 'nombreUsuario');
    $rutinas = Rutina::obtenerRutinas($usuario);
    echo json_encode(["rutinas" => $rutinas]);
} else {
    header("Location: ../index.html");
}
