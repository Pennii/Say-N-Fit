<?php
require_once __DIR__ . '/../modelos/Conexion.php';

/**
 * Clase que realizara las funciones que impliquen a los ejercicios
 */
class Ejercicio
{
    private $nombre;
    private $descripcion;
    private $nivel;
    private $musculos;

    public function __construct($nombre, $descripcion, $nivel, $musculos)
    {
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->nivel = $nivel;
        $this->musculos = $musculos;
    }

    /**
     * Lista los ejercicios y agrega los filtros necesarios
     */
    public static function listarEjercicios(?array $filtros = null)
    {
        Conexion::conectar();
        $query = "SELECT * FROM EJERCICIO";
        if ($filtros) {
            $query .= " WHERE ";
            foreach ($filtros as $nivel) {
                if ($nivel != null) {
                    $query .= "NIVEL = '$nivel' OR ";
                    $filtrado = true;
                }
            }
            //Se eliminan las sentencias que no sean necesarias y quedan al final de la consulta
            if (isset($filtrado)) {
                $query = substr($query, 0, strlen($query) - 4);
            } else {
                $query = substr($query, 0, strlen($query) - 7);
            }
        }
        $query .= " ORDER BY NIVEL ASC";
        $consultarEjercicios = Conexion::getConexion()->query($query);
        Conexion::desconectar();
        return $consultarEjercicios->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve el ejercicio solicitado por nombre
     */
    public static function obtenerEjercicio($nombre)
    {
        Conexion::conectar();
        $query = "SELECT * FROM EJERCICIO WHERE nombre = '$nombre'";
        $consultarEjercicio = Conexion::getConexion()->query($query);
        Conexion::desconectar();
        return $consultarEjercicio->fetch(PDO::FETCH_ASSOC);
    }

    public static function guardarEjercicio($ejercicio, $editando = false, $original = '')
    {
        if ($editando) {
            $ejercicioExistente = self::obtenerEjercicio($original);
            $query = "UPDATE EJERCICIO SET NOMBRE = :nom, NIVEL = :niv, DESCRIPCION = :descr, MUSCULOS = :mus WHERE NOMBRE = '$original'";
        } else {
            $ejercicioExistente = self::obtenerEjercicio($ejercicio["nombre"]);
            $query = "INSERT INTO EJERCICIO (NOMBRE, NIVEL, DESCRIPCION, MUSCULOS) VALUES(:nom, :niv, :descr, :mus)";
        }
        if (($editando && $ejercicioExistente) || (!$editando && !$ejercicioExistente)) {
            try {
                Conexion::conectar();
                $guardarEjercicio = Conexion::getConexion()->prepare($query);
                $guardarEjercicio->bindParam(":nom", $ejercicio["nombre"]);
                $guardarEjercicio->bindParam(":niv", $ejercicio["nivel"]);
                $guardarEjercicio->bindParam(":descr", $ejercicio["descripcion"]);
                $guardarEjercicio->bindParam(":mus", $ejercicio["musculos"]);
                $guardarEjercicio->execute();
                $guardado = true;
            } catch (\Throwable $th) {
                $guardado = false;
                var_dump($th->getMessage());
            }
        } else {
            $guardado = false;
        }
        Conexion::desconectar();
        return $guardado;
    }

    public static function eliminarEjercicio($ejercicio)
    {
        Conexion::conectar();
        try {
            $query = "DELETE FROM EJERCICIO WHERE nombre = '$ejercicio'";
            $eliminarEjercicio = Conexion::getConexion()->query($query);
            $eliminado = true;
        } catch (\Throwable $th) {
            $eliminado = false;
        }
        Conexion::desconectar();
        return $eliminado;
    }
}
