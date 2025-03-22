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
    private $lider;

    function __construct($nombre, $clave, $lider)
    {
        $this->nombre = $nombre;
        $this->clave = $clave;
        $this->lider = $lider;
    }

    /**
     * Devuelve la clave de grupo
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * Se crea un grupo pasando como parametro el nombre del usuario que lo crea, para de esta forma
     * insertar al lider de grupo.
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

    /**
     * Verifica si un usuario es lider de grupo
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
        return $eliminado;
    }

    /**
     * Elimina un usuario que desee abandonar el grupo, si el usuario es el lider del grupo
     * asignara al primero que encuentre por orden alfabetico para asignarlo como nuevo lider.
     * Si no hay otro usuario en el grupo entonces se elimina el grupo
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
