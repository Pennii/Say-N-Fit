<?php
$conexion = new PDO('mysql:host=mysql_server;dbname=SAY_N_FIT','root','clave');

$usuario = $conexion->query("select * from usuario");

var_dump($usuario->fetch(PDO::FETCH_ASSOC));
