<?php
require_once '../modelos/Conexion.php';

class Usuario
{
    private $alias;
    private $nombre;
    private $nacimiento;
    private $peso;
    private $clave;

    public function getAlias(){
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

    public function insertarUsuario()
    {
        if (!self::verUsuario($this->alias)) {
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
        }else{
            $resultado = false;
        }
        Conexion::desconectar();
        return $resultado;
    }

    public static function verUsuario($usuario)
    {
        Conexion::conectar();
        $consultarUsuario = Conexion::getConexion()->prepare("SELECT * FROM USUARIO WHERE BINARY ALIAS = :usuario");
        $consultarUsuario->bindParam(':usuario', $usuario);
        $consultarUsuario->execute();
        Conexion::desconectar();
        return $consultarUsuario->fetch(PDO::FETCH_ASSOC);
    }

    public static function logear($usuario, $clave){
        Conexion::conectar();
        $consultarUsuario = Conexion::getConexion()->prepare("SELECT CLAVE FROM USUARIO WHERE BINARY ALIAS = :usuario");
        $consultarUsuario->bindParam(":usuario", $usuario);
        $consultarUsuario->execute();
        $claveHasheada = $consultarUsuario->fetch(PDO::FETCH_ASSOC);
        Conexion::desconectar();
        $salida = $claveHasheada ? password_verify($clave, $claveHasheada["CLAVE"]) : false;
        return $salida;
    }

    public function actualizarDatos($aliasOriginal){
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
        }else{
            $resultado = false;
        }
        Conexion::desconectar();
        return $resultado;
    }
}
