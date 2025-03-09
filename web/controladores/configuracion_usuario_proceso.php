<?php
require_once '../modelos/Usuario.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $nombre = json_decode($json, true);
    $datos = Usuario::verUsuario($nombre);
    echo json_encode(["datos" => $datos]);
}