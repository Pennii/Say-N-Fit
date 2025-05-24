<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once '../modelos/Ejercicio.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    if (isset($datos["buscarEjercicio"])) {
        $ejercicio = $datos["buscarEjercicio"];
        $datosEjercicio = Ejercicio::obtenerEjercicio($ejercicio);
        echo json_encode($datosEjercicio);
    } else if (isset($datos["guardarEjercicio"])) {
        $ejercicio = $datos["guardarEjercicio"];
        $guardado = Ejercicio::guardarEjercicio($ejercicio);
        echo json_encode($guardado);
    } else if (isset($datos["editarEjercicio"])) {
        $ejercicio = $datos["editarEjercicio"];
        $editado = Ejercicio::guardarEjercicio($ejercicio, true, $datos["nombreEjercicio"]);
        echo json_encode($editado);
    } else if (isset($datos["eliminarEjercicio"])) {
        $ejercicio = $datos["eliminarEjercicio"];
        $eliminado = Ejercicio::eliminarEjercicio($ejercicio);
        echo json_encode($eliminado);
    }
}
