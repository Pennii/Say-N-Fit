<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once '../modelos/Grupo.php';
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Se solicita el contenido actual del chat y se verifica si el usuario esta dentro del grupo por si fue eliminado mientras tiene la ventana abierta
    $grupo = filter_input(INPUT_GET, "grupo");
    $nombreUsuario = filter_input(INPUT_GET, "nombreUsuario");
    $encontrado = false;
    $usuarios = Grupo::listarUsuarios($grupo);
    foreach ($usuarios as $usuario) {
        if (in_array($nombreUsuario, $usuario)) {
            $encontrado = true;
        }
    }
    if (!$encontrado) {
        //Si no se encuentra entonces no se sigue la ejecucion del archivo
        echo json_encode(["noEncontrado" => true]);
        exit();
    }
} else {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    $grupo = $datos["grupo"];
}
//Si el archivo de texto esta vacio entonces se muestra el mensaje predeterminado
$mensajesEscritos = file_get_contents("../mensajes/$grupo.txt");
if (trim($mensajesEscritos) === "") {
    $mensajesEscritos = "Bienvenido al chat, recuerda comportarte;";
}

//Se recibe el mensaje que envia el usuario
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
//Se sobreescribe el archivo de texto y se devuelven los datos del chat
file_put_contents("../mensajes/$grupo.txt", $chat);
echo json_encode(["chat" => $chat, "grupo" => $grupo]);
