<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $grupo = filter_input(INPUT_GET, "grupo");
} else {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    $grupo = $datos["grupo"];
}
$mensajesEscritos = file_get_contents("../mensajes/$grupo.txt");
if (trim($mensajesEscritos) === "") {
    $mensajesEscritos = "Bienvenido al chat, recuerda comportarte;";
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($datos["nombreUsuario"]) {
        $usuario = $datos["nombreUsuario"];
        $mensaje = htmlspecialchars($datos["mensaje"]);
        $mensaje = $usuario . "╝" . $datos["mensaje"] . "╝" . date('d-m-Y H:i:s') . ";";
    } else {
        exit();
    }
    $chat = $mensajesEscritos . $mensaje;
} else {
    $chat = $mensajesEscritos;
}
file_put_contents("../mensajes/$grupo.txt", $chat);
echo json_encode(["chat" => $chat, "grupo" => $grupo]);
