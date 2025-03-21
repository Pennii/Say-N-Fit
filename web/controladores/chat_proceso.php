<?php
$grupo = filter_input(INPUT_COOKIE, "grupo");
$mensajesEscritos = file_get_contents("../mensajes/$grupo.txt");
if (trim($mensajesEscritos) === "") {
    $mensajesEscritos = "Bienvenido al chat, recuerda comportarte;";
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    if (filter_has_var(INPUT_COOKIE, "nombreUsuario")) {
        $usuario = filter_input(INPUT_COOKIE, "nombreUsuario");
        $mensaje = htmlspecialchars($datos["mensaje"]);
        $mensaje = $usuario . "╝" . $datos["mensaje"] . "╝" . date('d-m-Y H:i:s') . ";";
    }else{
        header("Location: index.html");
    }
    $chat = $mensajesEscritos . $mensaje;
} else {
    $chat = $mensajesEscritos;
}
file_put_contents("../mensajes/$grupo.txt", $chat);
echo json_encode(["chat" => $chat]);