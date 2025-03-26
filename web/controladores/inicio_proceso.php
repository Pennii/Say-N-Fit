<?php   
require_once '../modelos/Rutina.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    $usuario = filter_input(INPUT_COOKIE, 'nombreUsuario');
    $rutinas = Rutina::obtenerRutinasPorDia($usuario, $datos['dia']);
    echo json_encode(["rutinas" => $rutinas]);
}