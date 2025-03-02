<?php
require_once '../modelos/Ejercicio.php';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    if ($datos) {
        $niveles = [$datos["principiante"], $datos["intermedio"], $datos["avanzado"]];
        $lista = Ejercicio::listarEjercicios($niveles);
    }else{
        $lista = Ejercicio::listarEjercicios();
    }
    echo json_encode(["ejercicios" => $lista]);
}
