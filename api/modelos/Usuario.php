<?php
require_once __DIR__ . '/../modelos/Conexion.php';

/**
 * Clase que realiza las acciones de los usuarios
 */
class Usuario
{
    private $alias;
    private $nombre;
    private $nacimiento;
    private $peso;
    private $clave;

    /**
     * Devuelve el alias del usuario
     * @return string
     * @throws Exception Si el alias no está definido
     */
    public function getAlias()
    {
        return $this->alias;
    }

    public function __construct($alias, $nombre, $nacimiento, $peso, $clave)
    {
        $this->alias = $alias;
        $this->nombre = $nombre;
        $this->nacimiento = $nacimiento;
        $this->peso = $peso;
        $this->clave = $clave;
    }

    /**
     * Verifico que el usuario no exista en la bd y lo inserto
     * @return bool
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public function insertarUsuario()
    {
        if (!self::verUsuario($this->alias)) {
            //Con esta funcion hasheo la contraseña
            $claveHasheada = password_hash($this->clave, PASSWORD_DEFAULT);
            try {
                Conexion::conectar();
                $insertarUsuario = Conexion::getConexion()->prepare("INSERT INTO USUARIO (alias, nombre, nacimiento, peso, clave) VALUES (:usu, :nom, :nac, :peso, :clave)");
                $insertarUsuario->bindParam(":usu", $this->alias);
                $insertarUsuario->bindParam(":nom", $this->nombre);
                $insertarUsuario->bindParam(":nac", $this->nacimiento);
                $insertarUsuario->bindParam(":peso", $this->peso);
                $insertarUsuario->bindParam(":clave", $claveHasheada);
                $resultado = $insertarUsuario->execute();
            } catch (Exception $e) {
                $resultado = false;
            }
        } else {
            $resultado = false;
        }
        Conexion::desconectar();
        return $resultado;
    }

    /**
     * Devuelve los datos de un usuario en forma de array
     * @param string $usuario Alias del usuario a buscar
     * @return array|null Devuelve un array con los datos del usuario o null si no se encuentra
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function verUsuario($usuario)
    {
        Conexion::conectar();
        $consultarUsuario = Conexion::getConexion()->prepare("SELECT * FROM USUARIO WHERE BINARY ALIAS = :usuario");
        $consultarUsuario->bindParam(':usuario', $usuario);
        $consultarUsuario->execute();
        Conexion::desconectar();
        return $consultarUsuario->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca la clave de un usuario y la compara con la clave sin hashear que se pasa como parametro
     * devolviendo true si coinciden o false si no se encuentra la clave o no coinciden las dos versiones
     * @param string $usuario Alias del usuario a buscar
     * @param string $clave Clave sin hashear del usuario
     * @return bool Devuelve true si la clave coincide, false si no
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public static function logear($usuario, $clave)
    {
        Conexion::conectar();
        $consultarUsuario = Conexion::getConexion()->prepare("SELECT CLAVE FROM USUARIO WHERE BINARY ALIAS = :usuario");
        $consultarUsuario->bindParam(":usuario", $usuario);
        $consultarUsuario->execute();
        $claveHasheada = $consultarUsuario->fetch(PDO::FETCH_ASSOC);
        Conexion::desconectar();
        $salida = $claveHasheada ? password_verify($clave, $claveHasheada["CLAVE"]) : false;
        return $salida;
    }

    /**
     * Verifcio que el usuario este en la bd y actualizo los datos
     * @param string $aliasOriginal Alias original del usuario antes de la actualización
     * @return bool|string Devuelve true si se actualizó correctamente, false si no se encontró el usuario o un mensaje de error si ocurre una excepción
     * @throws Exception Si ocurre un error al conectar a la base de datos
     */
    public function actualizarDatos($aliasOriginal)
    {
        if (self::verUsuario($aliasOriginal)) {
            $claveHasheada = password_hash($this->clave, PASSWORD_DEFAULT);
            try {
                Conexion::conectar();
                $insertarUsuario = Conexion::getConexion()->prepare("UPDATE USUARIO SET ALIAS = :usu, NOMBRE = :nom, NACIMIENTO = :nac, PESO = :peso, CLAVE = :clave WHERE BINARY ALIAS = :usuOriginal");
                $insertarUsuario->bindParam(":usu", $this->alias);
                $insertarUsuario->bindParam(":nom", $this->nombre);
                $insertarUsuario->bindParam(":nac", $this->nacimiento);
                $insertarUsuario->bindParam(":peso", $this->peso);
                $insertarUsuario->bindParam(":clave", $claveHasheada);
                $insertarUsuario->bindParam(":usuOriginal", $aliasOriginal);
                $resultado = $insertarUsuario->execute();
            } catch (Exception $e) {
                $resultado = $e->getMessage();
            }
        } else {
            $resultado = false;
        }
        Conexion::desconectar();
        return $resultado;
    }
}
