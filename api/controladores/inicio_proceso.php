<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once '../modelos/Rutina.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    $usuario = $datos["nombreUsuario"];
    // Se devuelven las rutinas para poder verlas en la pagina de inicio
    $rutinas = Rutina::obtenerRutinasPorDia($usuario, $datos['dia']);
    echo json_encode(["rutinas" => $rutinas]);
}
