<?php

require_once __DIR__ . '/../modelos/Conexion.php';
require_once __DIR__ . '/../modelos/funciones.php';

/**
 * Clase que realiza las acciones de los grupos
 */
class Grupo
{
    private $nombre;
    private $clave;
    private $lider;

    /**
     * Constructor de la clase Grupo
     * @param string $nombre Nombre del grupo
     * @param string $clave Clave del grupo
     * @param string $lider Usuario que lidera el grupo
     * @return void
     */
    function __construct($nombre, $clave, $lider)
    {
        $this->nombre = $nombre;
        $this->clave = $clave;
        $this->lider = $lider;
    }

    /**
     * Devuelve la clave de grupo
     * @return string
     * @throws Exception Si la clave no es una cadena
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * Se crea un grupo pasando como parametro el nombre del usuario que lo crea, para de esta forma
     * insertar al lider de grupo.
     * @param string $usuario Usuario que crea el grupo
     * @return bool Devuelve true si se ha creado el grupo correctamente, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public function crearGrupo($usuario)
    {
        if (!self::verGrupo($this->clave)) {
            try {
                Conexion::conectar();
                $insertarGrupo = Conexion::getConexion()->prepare("INSERT INTO GRUPO VALUES(:clave, :nom, :lid)");
                $insertarGrupo->bindParam(':clave', $this->clave);
                $insertarGrupo->bindParam(":nom", $this->nombre);
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
     * @param string $clave Clave del grupo a consultar
     * @return array|false Devuelve un array con los datos del grupo si existe, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function verGrupo($clave)
    {
        Conexion::conectar();
        $clave = sanear_texto($clave);
        try {
            $verGrupo = Conexion::getConexion()->prepare("SELECT * FROM GRUPO WHERE CLAVE = :cla");
            $verGrupo->bindParam(":cla", $clave);
            $verGrupo->execute();
        } catch (\Throwable $th) {
        }
        Conexion::desconectar();
        return $verGrupo->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Se devuelve un listado de todos los grupos
     * @return array|false Devuelve un array con los datos de todos los grupos si existen, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
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
     * @param string $usuario Usuario del que se quieren ver los grupos
     * @return array|false Devuelve un array con los grupos del usuario si existen, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function verGruposPorUsuario($usuario)
    {
        Conexion::conectar();
        try {
            $verGrupo = Conexion::getConexion()->PREPARE("SELECT G.* FROM GRUPO G INNER JOIN PERTENECE P ON G.CLAVE = P.GRUPO WHERE P.USUARIO = :usu ORDER BY G.NOMBRE");
            $verGrupo->bindParam(":usu", $usuario);
            $verGrupo->execute();
        } catch (\Throwable $th) {
        }
        Conexion::desconectar();
        return $verGrupo->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Agrega al usuario a la tabla de pertenece cuando crea o se une a un grupo
     * @param string $usuario Usuario que se va a agregar al grupo
     * @param string $grupo Clave del grupo al que se va a agregar el usuario
     * @return bool Devuelve true si se ha agregado correctamente, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
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
     * @param string $clave Clave del grupo a buscar
     * @param string $usuario Usuario que se va a agregar al grupo
     * @return bool Devuelve true si se ha encontrado el grupo y se ha agregado el usuario, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
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

    /**
     * Verifica si un usuario es lider de grupo
     * @param string $grupo Clave del grupo a verificar
     * @param string $usuario Usuario a verificar si es lider del grupo
     * @return bool Devuelve true si el usuario es lider del grupo, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function verificarLider($grupo, $usuario)
    {
        Conexion::conectar();
        try {
            $consultarLider = Conexion::getConexion()->prepare("SELECT CLAVE FROM GRUPO WHERE CLAVE = :cla AND LIDER = :lid");
            $consultarLider->bindParam(":cla", $grupo);
            $consultarLider->bindParam(":lid", $usuario);
            $consultarLider->execute();
            $lider = $consultarLider->fetch() != false;
        } catch (\Throwable $th) {
            $lider = false;
        }
        Conexion::desconectar();
        return $lider;
    }

    /**
     * Devolvera una lista de usuarios que pertenecen a un grupo
     * @param string $grupo Clave del grupo del que se quieren listar los usuarios
     * @return array|false Devuelve un array con los usuarios del grupo si existen, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function listarUsuarios($grupo)
    {
        Conexion::conectar();
        try {
            $consultarUsuarios = Conexion::getConexion()->prepare("SELECT USUARIO FROM PERTENECE WHERE GRUPO = :gru");
            $consultarUsuarios->bindParam(":gru", $grupo);
            $consultarUsuarios->execute();
        } catch (\Throwable $th) {
            $error = true;
        }
        $resultado = isset($error) ? false : $consultarUsuarios->fetchAll();
        Conexion::desconectar();
        return $resultado;
    }

    /**
     * Actualiza los datos de un grupo
     * @return bool Devuelve true si se han actualizado los datos correctamente, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
     * 
     */
    public function actualizarDatos()
    {
        if (self::verGrupo($this->clave)) {
            Conexion::conectar();
            try {
                $actualizarGrupo = Conexion::getConexion()->prepare("UPDATE GRUPO SET NOMBRE = :nom, LIDER = :lid WHERE CLAVE = :cla");
                $actualizarGrupo->bindParam(":nom", $this->nombre);
                $actualizarGrupo->bindParam(":lid", $this->lider);
                $actualizarGrupo->bindParam(":cla", $this->clave);
                $actualizarGrupo->execute();
            } catch (\Throwable $th) {
                $error = true;
            }
        } else {
            $error = true;
        }

        $resultado = !isset($error);
        Conexion::desconectar();
        return $resultado;
    }

    /**
     * Elimina un usuario del grupo
     * @param string $usuario Usuario que se va a eliminar del grupo
     * @param string $grupo Clave del grupo del que se va a eliminar el usuario
     * @return bool Devuelve true si se ha eliminado correctamente, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function eliminarUsuario($usuario, $grupo)
    {
        Conexion::conectar();
        try {
            $eliminarUsuario = Conexion::getConexion()->prepare("DELETE FROM PERTENECE WHERE USUARIO = :usu AND GRUPO = :gru");
            $eliminarUsuario->bindParam(":usu", $usuario);
            $eliminarUsuario->bindParam(":gru", $grupo);
            $eliminarUsuario->execute();
            $eliminado = true;
        } catch (\Throwable $th) {
            $eliminado = false;
        }
        return $eliminado;
    }

    /**
     * Cambia el lider de un grupo por otro
     * @param string $usuario Usuario que se convertira en lider del grupo
     * @param string $grupo Clave del grupo al que se le cambiara el lider
     * @return bool Devuelve true si se ha cambiado el lider correctamente, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function convertirLider($usuario, $grupo)
    {
        Conexion::conectar();
        try {
            $convertir = Conexion::getConexion()->prepare("UPDATE GRUPO SET LIDER = :lid WHERE CLAVE = :cla");
            $convertir->bindParam(":lid", $usuario);
            $convertir->bindParam(":cla", $grupo);
            $convertir->execute();
            $convertido = true;
        } catch (\Throwable $th) {
            $convertido = false;
        }
        Conexion::desconectar();
        return $convertido;
    }

    /**
     * Elimina un grupo
     * @param string $grupo Clave del grupo a eliminar
     * @return bool Devuelve true si se ha eliminado correctamente, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function eliminarGrupo($grupo)
    {
        Conexion::conectar();
        try {
            $eliminarGrupo = Conexion::getConexion()->prepare("DELETE FROM GRUPO WHERE CLAVE = :gru");
            $eliminarGrupo->bindParam(":gru", $grupo);
            $eliminarGrupo->execute();
            $eliminado = true;
        } catch (\Throwable $th) {
            $eliminado = false;
        }
        Conexion::desconectar();
        if ($eliminado) {
            $archivo = __DIR__ . "/../mensajes/$grupo.txt";
            if (file_exists($archivo)) {
                unlink($archivo);
            }
        }
        return $eliminado;
    }

    /**
     * Elimina un usuario dell grupo, si el usuario es el lider del grupo
     * asignara al primero que encuentre por orden alfabetico para asignarlo como nuevo lider.
     * Si no hay otro usuario en el grupo entonces se elimina el grupo
     * @param string $usuario Usuario que se va a eliminar del grupo
     * @param string $grupo Clave del grupo del que se va a eliminar el usuario
     * @return bool Devuelve true si se ha abandonado el grupo correctamente, false en caso contrario
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function abandonarGrupo($usuario, $grupo)
    {
        if (self::verificarLider($grupo, $usuario)) {
            try {
                Conexion::conectar();
                $consultarNuevoLider = Conexion::getConexion()->prepare("SELECT USUARIO FROM PERTENECE WHERE GRUPO = :gru AND USUARIO != :usu LIMIT 1");
                $consultarNuevoLider->bindParam(":gru", $grupo);
                $consultarNuevoLider->bindParam(":usu", $usuario);
                $consultarNuevoLider->execute();
                $nuevoLider = $consultarNuevoLider->fetch(PDO::FETCH_ASSOC);
            } catch (\Throwable $th) {
                $nuevoLider = false;
            }
            if ($nuevoLider != false) {
                self::convertirLider($nuevoLider["USUARIO"], $grupo);
            } else {
                self::eliminarGrupo($grupo);
            }
        }
        $abandonado = self::eliminarUsuario($usuario, $grupo);
        Conexion::desconectar();
        return $abandonado;
    }
}
