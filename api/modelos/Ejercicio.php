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

    /**
     * Constructor de la clase Ejercicio
     * @param string $nombre Nombre del ejercicio
     * @param string $descripcion Descripcion del ejercicio
     * @param string $nivel Nivel de dificultad del ejercicio
     * @param string $musculos Musculos que se trabajan con el ejercicio
     * @return void
     */
    public function __construct($nombre, $descripcion, $nivel, $musculos)
    {
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->nivel = $nivel;
        $this->musculos = $musculos;
    }

    /**
     * Lista los ejercicios y agrega los filtros necesarios
     * @param array|null $filtros Filtros a aplicar en la consulta
     * @return array Lista de ejercicios
     * @throws Exception Si ocurre un error al conectar a la base de datos
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
     * @param string $nombre Nombre del ejercicio a buscar
     * @return array|null Datos del ejercicio o null si no se encuentra
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function obtenerEjercicio($nombre)
    {
        Conexion::conectar();
        $query = "SELECT * FROM EJERCICIO WHERE nombre = '$nombre'";
        $consultarEjercicio = Conexion::getConexion()->query($query);
        Conexion::desconectar();
        return $consultarEjercicio->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Inserta o actualiza un ejercicio segun los parametros que se pasen
     * @param array $ejercicio Datos del ejercicio a guardar
     * @param bool $editando Indica si se esta editando un ejercicio existente
     * @param string $original Nombre del ejercicio original si se esta editando
     * @return bool Indica si se guardo correctamente el ejercicio
     */
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

    /**
     * Elimina un ejercicio
     * @param string $ejercicio Nombre del ejercicio a eliminar
     * @return bool Indica si se elimino correctamente el ejercicio
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function eliminarEjercicio($ejercicio)
    {
        Conexion::conectar();
        try {
            $query = "DELETE FROM EJERCICIO WHERE nombre = '$ejercicio'";
            Conexion::getConexion()->query($query);
            $eliminado = true;
        } catch (\Throwable $th) {
            $eliminado = false;
        }
        Conexion::desconectar();
        return $eliminado;
    }
}
