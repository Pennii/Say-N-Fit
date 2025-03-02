<?php
abstract class  Conexion{
    private static ?PDO $conexion = null;

    public static function getConexion() {
        return self::$conexion;
    }

    public static function conectar(){
        if (self::$conexion == null) {
        self::$conexion = new PDO('mysql:host=mysql_server;dbname=say_n_fit','root','clave');
        }
        return self::$conexion != null;
    }

    public static function desconectar(){
        self::$conexion = null;
        return self::$conexion == null;
    }
}