<?php
$conexion = new PDO("mysql:host=localhost;dbname=say n fit", "root", "");
$imagen = $_FILES["archivo"];
$tipo = $imagen["type"];
$usuario = "matias";
switch ($tipo) {
    case 'image/jpg':
        $extension = "jpg";
        break;
    case 'image/png':
        $extension = "png";
        break;
    case 'image/jpeg':
        $extension = "jpeg";
        break;
}
if (isset($extension)) {
    $ubicacion = "iconos_usuarios/$usuario.$extension";
    move_uploaded_file($imagen["tmp_name"], $ubicacion);
}

// $conexion->exec("INSERT INTO USUARIO VALUES('EJEMPLO', 'MATIAS', 22, 74, '123', '".$imagen."')");
// $resultado = $conexion->query("SELECT * FROM USUARIO");

// $fila = $resultado->fetch(PDO::FETCH_ASSOC);
// echo $fila["imagen"];