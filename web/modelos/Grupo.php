<?php

require_once '../modelos/Conexion.php';
require_once '../modelos/funciones.php';
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

    public function crearGrupo()
    {
        if (!self::verGrupo($this->nombre)) {
            try {
                Conexion::conectar();
                $insertarGrupo = Conexion::getConexion()->prepare("INSERT INTO GRUPO VALUES(:nom, :clave, :fec, :lid)");
                $insertarGrupo->bindParam(":nom", $this->nombre);
                $insertarGrupo->bindParam(':clave', $this->clave);
                $insertarGrupo->bindParam(':fec', $this->creacion);
                $insertarGrupo->bindParam(':lid', $this->lider);
                $insertarGrupo->execute();
                $insertado = true;
            } catch (\Throwable $th) {
                $insertado = false;
            }
        } else {
            $insertado = false;
        }
        return $insertado;
    }

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
}
