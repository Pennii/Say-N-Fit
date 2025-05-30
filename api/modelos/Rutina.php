<?php

require_once __DIR__ . '/../modelos/Conexion.php';
require_once __DIR__ . '/../modelos/funciones.php';
class Rutina
{

    private $codigo;
    private $usuario;
    public $nombre;
    public $dias;

    /**
     * Constructor de la clase Rutina
     * @param string $codigo Codigo de la rutina
     * @param string $usuario Usuario al que pertenece la rutina
     * @param string $dias Dias de la semana en los que se realiza la rutina
     * @param string $nombre Nombre de la rutina (opcional)
     * @return void
     */
    public function __construct($codigo, $usuario, $dias, $nombre = '')
    {
        $this->codigo = $codigo;
        $this->usuario = $usuario;
        $this->nombre = $nombre == '' ? "Rutina $codigo" : $nombre;
        $this->dias = $dias;
    }

    /**
     * Devuelve los datos de una rutina
     * @param string $codigo Codigo de la rutina
     * @param string $usuario Usuario al que pertenece la rutina
     * @return array|false Datos de la rutina o false si no existe
     * @throws Exception Si ocurre un error al conectar a la base de datos
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
     * @param string $usuario Usuario al que pertenece la rutina
     * @return array|false Codigo actual de la rutina o false si no existe
     * @throws Exception Si ocurre un error al conectar a la base de datos
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
     * @return bool Indica si se ha creado la rutina correctamente
     * @throws Exception Si ocurre un error al conectar a la base de datos
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
                var_dump($th->getMessage());
            }
            Conexion::desconectar();
        } else {
            $agregado = false;
        }
        return $agregado;
    }

    /**
     * Asigna ejercicios a una rutina
     * @param string $ejercicio Nombre del ejercicio a almacenar
     * @return bool Indica si se ha almacenado el ejercicio correctamente
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public function almacenarEjercicio($ejercicio)
    {
        Conexion::conectar();
        try {
            $almacenarEjercicio = Conexion::getConexion()->prepare("INSERT INTO ALMACENA(CODIGO_RUTINA, USUARIO, EJERCICIO) VALUES(:cod, :usu, :ejer)");
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
     * @param string $usuario Usuario al que pertenecen las rutinas
     * @return array|false Rutinas del usuario o false si no existen
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function obtenerRutinas($usuario)
    {
        Conexion::conectar();
        try {
            $obtenerRutinas = Conexion::getConexion()->prepare("SELECT * FROM RUTINA WHERE USUARIO = :usu ORDER BY 3");
            $obtenerRutinas->bindParam(":usu", $usuario);
            $obtenerRutinas->execute();
            $resultado = $obtenerRutinas->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            $resultado = false;
        }
        Conexion::desconectar();
        return $resultado;
    }

    /**
     * Devuelve los ejercicios de una rutina
     * @param string $usuario Usuario al que pertenece la rutina
     * @param string $rutina Codigo de la rutina
     * @return array|false Ejercicios de la rutina o false si no existen
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function obtenerEjercicios($usuario, $rutina)
    {
        Conexion::conectar();
        try {
            $obtenerEjercicios = Conexion::getConexion()->prepare("SELECT E.NOMBRE, A.SERIES, A.REPETICIONES FROM EJERCICIO E INNER JOIN ALMACENA A ON E.NOMBRE = A.EJERCICIO WHERE A.CODIGO_RUTINA = :rut AND A.USUARIO = :usu");
            $obtenerEjercicios->bindParam(":rut", $rutina);
            $obtenerEjercicios->bindParam(":usu", $usuario);
            $obtenerEjercicios->execute();
            $resultado = $obtenerEjercicios->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            $resultado = false;
        }
        Conexion::desconectar();
        return $resultado;
    }

    /**
     * Quita un ejercicio de una rutina y elimina la rutina si ya no quedan ejercicios
     * @param string $ejercicio Nombre del ejercicio a quitar
     * @param string $usuario Usuario al que pertenece la rutina
     * @param string $rutina Codigo de la rutina
     * @return int Indica si se ha quitado el ejercicio correctamente (1), si se ha eliminado la rutina (-1) o si ha fallado (0)
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function quitarEjercicio($ejercicio, $usuario, $rutina)
    {
        Conexion::conectar();
        try {
            $quitarEjercicio = Conexion::getConexion()->prepare("DELETE FROM ALMACENA WHERE EJERCICIO = :ejer AND USUARIO = :usu AND CODIGO_RUTINA = :rut");
            $quitarEjercicio->bindParam(":ejer", $ejercicio);
            $quitarEjercicio->bindParam(":usu", $usuario);
            $quitarEjercicio->bindParam(":rut", $rutina);
            $quitarEjercicio->execute();
            $eliminado = 1;
        } catch (\Throwable $th) {
            $eliminado = 0;
        }
        if ($eliminado && !self::obtenerEjercicios($usuario, $rutina)) {
            self::eliminarRutina($usuario, $rutina);
            $eliminado = -1;
        }
        Conexion::desconectar();
        return $eliminado;
    }

    /**
     * Elimina una rutina
     * @param string $usuario Usuario al que pertenece la rutina
     * @param string $rutina Codigo de la rutina a eliminar
     * @return bool Indica si se ha eliminado la rutina correctamente
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function eliminarRutina($usuario, $rutina)
    {
        Conexion::conectar();
        try {
            $eliminarRutina = Conexion::getConexion()->prepare("DELETE FROM RUTINA WHERE CODIGO = :rut AND USUARIO = :usu");
            $eliminarRutina->bindParam(":rut", $rutina);
            $eliminarRutina->bindParam(":usu", $usuario);
            $eliminarRutina->execute();
            $eliminado = true;
        } catch (\Throwable $th) {
            $eliminado = false;
        }
        Conexion::desconectar();
        return $eliminado;
    }

    /**
     * Actualiza una rutina, cambiando sus datos y los ejercicios
     * @param array $ejercicios Array de ejercicios con sus series y repeticiones
     * @return bool|string Indica si se ha actualizado la rutina correctamente o el mensaje de error si ha fallado
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public function actualizarRutina($ejercicios)
    {
        Conexion::conectar();
        try {
            Conexion::getConexion()->beginTransaction();
            $actualizarRutina = Conexion::getConexion()->prepare("UPDATE RUTINA SET NOMBRE = :nom, DIAS = :dias WHERE CODIGO = :cod AND USUARIO = :usu");
            $actualizarRutina->bindParam(":nom", $this->nombre);
            $actualizarRutina->bindParam(":dias", $this->dias);
            $actualizarRutina->bindParam(":cod", $this->codigo);
            $actualizarRutina->bindParam(":usu", $this->usuario);
            $actualizarRutina->execute();
            $actualizarEjercicios = Conexion::getConexion()->prepare("UPDATE ALMACENA SET SERIES = :ser, REPETICIONES = :rep WHERE CODIGO_RUTINA = :cod AND USUARIO = :usu AND EJERCICIO = :ejer");
            foreach ($ejercicios as $ejercicio) {
                $actualizarEjercicios->bindParam(":ser", $ejercicio["series"]);
                $actualizarEjercicios->bindParam(":rep", $ejercicio["repeticiones"]);
                $actualizarEjercicios->bindParam(":cod", $this->codigo);
                $actualizarEjercicios->bindParam(":usu", $this->usuario);
                $actualizarEjercicios->bindParam(":ejer", $ejercicio["nombre"]);
                $actualizarEjercicios->execute();
            }
            $actualizado = true;
            Conexion::getConexion()->commit();
        } catch (\Throwable $th) {
            Conexion::getConexion()->rollBack();
            $actualizado = $th->getMessage();
        }
        Conexion::desconectar();
        return $actualizado;
    }

    /**
     * Devuelve las rutinas de un usuario para un dia 
     * @param string $usuario Usuario al que pertenecen las rutinas
     * @param string $dia Dia de la semana para el que se quieren las rutinas
     * @return array|false Rutinas del usuario para el dia especificado o false si no existen
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function obtenerRutinasPorDia($usuario, $dia)
    {
        $dia = "%$dia%";
        Conexion::conectar();
        try {
            $obtenerRutinas = Conexion::getConexion()->prepare("SELECT * FROM RUTINA WHERE USUARIO = :usu AND DIAS LIKE :dia ORDER BY 3");
            $obtenerRutinas->bindParam(":usu", $usuario);
            $obtenerRutinas->bindParam(":dia", $dia);
            $obtenerRutinas->execute();
            $resultado = $obtenerRutinas->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            $resultado = false;
        }
        Conexion::desconectar();
        return $resultado;
    }
}
