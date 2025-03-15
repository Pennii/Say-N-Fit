<?php

require_once '../modelos/Conexion.php';
require_once '../modelos/funciones.php';

/**
 * Clase que realiza las acciones de los grupos
 */
class Grupo
{

    private $nombre;
    private $clave;
    private $creacion;
    private $lider;

    function __construct($nombre, $clave, $creacion, $lider)
    {
        $this->nombre = $nombre;
        $this->clave = $clave;
        $this->creacion = $creacion;
        $this->lider = $lider;
    }

    /**
     * Se crea un grupo pasando como parametro el nombre del usuario que lo crea, para de esta forma
     * insertar al lider de grupo.
     */
    public function crearGrupo($usuario)
    {
        if (!self::verGrupo($this->nombre)) {
            try {
                Conexion::conectar();
                $insertarGrupo = Conexion::getConexion()->prepare("INSERT INTO GRUPO VALUES(:clave, :nom, :fec, :lid)");
                $insertarGrupo->bindParam(':clave', $this->clave);
                $insertarGrupo->bindParam(":nom", $this->nombre);
                $insertarGrupo->bindParam(':fec', $this->creacion);
                $insertarGrupo->bindParam(':lid', $this->lider);
                $insertarGrupo->execute();
                $insertado = true;
                self::agregarUsuario($usuario, $this->clave);
            } catch (\Throwable $th) {
                $insertado = false;
            }
            Conexion::desconectar();
        } else {
            $insertado = false;
        }
        return $insertado;
    }

    /**
     * Se devuelven los datos de un grupo en forma de array.
     */
    public static function verGrupo($nombre)
    {
        Conexion::conectar();
        $nombre = sanear_texto($nombre);
        try {
            $verGrupo = Conexion::getConexion()->prepare("SELECT * FROM GRUPO WHERE BINARY NOMBRE = :nom");
            $verGrupo->bindParam(":nom", $nombre);
            $verGrupo->execute();
        } catch (\Throwable $th) {
        }
        Conexion::desconectar();
        return $verGrupo->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Se devuelve un listado de todos los grupos
     */
    public static function verGrupos()
    {
        Conexion::conectar();
        try {
            $verGrupo = Conexion::getConexion()->query("SELECT * FROM GRUPO");
        } catch (\Throwable $th) {
        }
        Conexion::desconectar();
        return $verGrupo->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve los grupos por usuario para poder listarlos en la pagina del usuario
     */
    public static function verGruposPorUsuario($usuario)
    {
        Conexion::conectar();
        try {
            $verGrupo = Conexion::getConexion()->PREPARE("SELECT G.* FROM GRUPO G INNER JOIN PERTENECE P ON G.CLAVE = P.GRUPO WHERE P.USUARIO = :usu");
            $verGrupo->bindParam(":usu", $usuario);
            $verGrupo->execute();
        } catch (\Throwable $th) {
        }
        Conexion::desconectar();
        return $verGrupo->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Agrega al usuario a la tabla de pertenece cuando crea o se une a un grupo
     */
    private static function agregarUsuario($usuario, $grupo)
    {
        Conexion::conectar();
        try {
            $agregarUsuario = Conexion::getConexion()->prepare("INSERT INTO PERTENECE VALUES (:usu, :gru)");
            $agregarUsuario->bindParam(":usu", $usuario);
            $agregarUsuario->bindParam(":gru", $grupo);
            $agregarUsuario->execute();
            $agregado = true;
        } catch (\Throwable $th) {
            $agregado = false;
        }
        Conexion::desconectar();
        return $agregado;
    }

    /**
     * Si se encuentra un grupo con esa clave se agregara al usuario al mismo
     */
    public static function encontrarGrupo($clave, $usuario)
    {
        Conexion::conectar();
        try {
            $encontrarGrupo = Conexion::getConexion()->prepare("SELECT * FROM GRUPO WHERE CLAVE = :cla");
            $encontrarGrupo->bindParam(":cla", $clave);
            $encontrarGrupo->execute();
            $datos = $encontrarGrupo->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            $encontrado = false;
        }
        $encontrado = $datos != false;
        if ($encontrado) {
            self::agregarUsuario($usuario, $datos["clave"]);
        }
        Conexion::desconectar();
        return $encontrado;
    }
}
