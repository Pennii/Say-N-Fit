<?php
/**
 * Clase que realizara las tareas de conexion con la base de datos
 */
abstract class  Conexion{
    private static ?PDO $conexion = null;

    /**
     * Devuelve la conexion
     */
    public static function getConexion() {
        return self::$conexion;
    }

    /**
     * Si no se esta conectado se realiza la conexion a la bd
     */
    public static function conectar(){
        if (self::$conexion == null) {
        self::$conexion = new PDO('mysql:host=mysql_server;dbname=say_n_fit','root','clave');
        }
        return self::$conexion != null;
    }

    /**
     * Si ya se realizo la conexion a la bd se desconecta
     */
    public static function desconectar(){
        self::$conexion = null;
        return self::$conexion == null;
    }
}