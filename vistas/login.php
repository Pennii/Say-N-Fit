<?php
$conexion = new PDO('mysql:host=mysql_server;dbname=say_n_fit','root','clave');

$usuario = $conexion->query("select * from usuario");

var_dump($usuario->fetch(PDO::FETCH_ASSOC));
