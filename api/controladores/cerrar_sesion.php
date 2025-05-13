<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
//Se eliminan los datos de sesion y se redirige al usuario a la pagina de inicio
session_start();
session_destroy();

http_response_code(200);
echo json_encode(["ok" => true]);
