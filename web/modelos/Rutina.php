<?php

require_once '../modelos/Conexion.php';
require_once '../modelos/funciones.php';
class Rutina
{

    private $codigo;
    private $usuario;
    private $nombre;
    private $dias;

    public function __construct($codigo, $usuario, $dias, $nombre = '')
    {
        $this->codigo = $codigo;
        $this->usuario = $usuario;
        $this->nombre = $nombre;
        $this->dias = $dias;
    }

    /**
     * Devuelve los datos de una rutina
     */
    public static function verRutina($codigo, $usuario)
    {
        Conexion::conectar();
        try {
            $consultarRutina = Conexion::getConexion()->prepare("SELECT * FROM RUTINA WHERE CODIGO = :cod AND USUARIO = :usu");
            $consultarRutina->bindParam(":cod", $codigo);
            $consultarRutina->bindParam(":usu", $usuario);
            $consultarRutina->execute();
            $rutina = $consultarRutina->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            $rutina = false;
        }
        Conexion::desconectar();
        return $rutina;
    }

    /**
     * Devuelve el codigo actual de las rutinas
     */
    public static function consultarCodigoActual($usuario)
    {
        Conexion::conectar();
        try {
            $consultarCodigo = Conexion::getConexion()->prepare("SELECT CODIGO FROM RUTINA WHERE USUARIO = :usu ORDER BY 1 DESC LIMIT 1");
            $consultarCodigo->bindParam(":usu", $usuario);
            $consultarCodigo->execute();
            $codigo = $consultarCodigo->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            $codigo = false;
        }
        Conexion::desconectar();
        return $codigo;
    }

    /**
     * Añade una rutina para el usuario
     */
    public function crearRutina()
    {
        if (!self::verRutina($this->codigo, $this->usuario)) {
            Conexion::conectar();
            try {
                $crearRutina = Conexion::getConexion()->prepare("INSERT INTO RUTINA VALUES(:cod, :usu, :nom, :dias)");
                $crearRutina->bindParam(":cod", $this->codigo);
                $crearRutina->bindParam(":usu", $this->usuario);
                $crearRutina->bindParam(":nom", $this->nombre);
                $crearRutina->bindParam(":dias", $this->dias);
                $crearRutina->execute();
                $agregado = true;
            } catch (\Throwable $th) {
                $agregado = false;
            }
            Conexion::desconectar();
        } else {
            $agregado = false;
        }
        return $agregado;
    }

    /**
     * Asigna ejercicios a una rutina
     */
    public function almacenarEjercicio($ejercicio)
    {
        Conexion::conectar();
        try {
            $almacenarEjercicio = Conexion::getConexion()->prepare("INSERT INTO ALMACENA VALUES(:cod, :usu, :ejer)");
            $almacenarEjercicio->bindParam(":cod", $this->codigo);
            $almacenarEjercicio->bindParam(":usu", $this->usuario);
            $almacenarEjercicio->bindParam(":ejer", $ejercicio);
            $almacenarEjercicio->execute();
            $almacenado = true;
        } catch (\Throwable $th) {
            $almacenado = false;
        }
        Conexion::desconectar();
        return $almacenado;
    }

    /**
     * Devuelve las rutinas de un usuario
     */
    public static function obtenerRutinas($usuario)
    {
        Conexion::conectar();
        try {
            $obtenerRutinas = Conexion::getConexion()->prepare("SELECT * FROM RUTINA WHERE USUARIO = :usu");
            $obtenerRutinas->bindParam(":usu", $usuario);
            $obtenerRutinas->execute();
            $resultado = $obtenerRutinas->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            $resultado = false;
        }
        Conexion::desconectar();
        return $resultado;
    }
}
