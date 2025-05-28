<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once '../modelos/Ejercicio.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    //Se obtienen los datos de un ejercicio
    if (isset($datos["buscarEjercicio"])) {
        $ejercicio = $datos["buscarEjercicio"];
        $datosEjercicio = Ejercicio::obtenerEjercicio($ejercicio);
        echo json_encode($datosEjercicio);
        //Se crea un ejercicio
    } else if (isset($datos["guardarEjercicio"])) {
        $ejercicio = $datos["guardarEjercicio"];
        $guardado = Ejercicio::guardarEjercicio($ejercicio);
        echo json_encode($guardado);
        //Se actualiza un ejercicio
    } else if (isset($datos["editarEjercicio"])) {
        $ejercicio = $datos["editarEjercicio"];
        $editado = Ejercicio::guardarEjercicio($ejercicio, true, $datos["nombreEjercicio"]);
        echo json_encode($editado);
        //Se elimina un ejercicio
    } else if (isset($datos["eliminarEjercicio"])) {
        $ejercicio = $datos["eliminarEjercicio"];
        $eliminado = Ejercicio::eliminarEjercicio($ejercicio);
        echo json_encode($eliminado);
    }
}
