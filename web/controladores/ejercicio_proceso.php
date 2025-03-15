<?php
require_once '../modelos/Ejercicio.php';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //Almaceno los datos json en una variable y los transformo en un array
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    //Verificando si datos existe puedo saber si hay filtros o no
    if ($datos) {
        $niveles = [$datos["principiante"], $datos["intermedio"], $datos["avanzado"]];
        $lista = Ejercicio::listarEjercicios($niveles);
    }else{
        $lista = Ejercicio::listarEjercicios();
    }
    //Se devuelve un objeto json con la propiedad ejercicios para la lista
    echo json_encode(["ejercicios" => $lista]);
}
