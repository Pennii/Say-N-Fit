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
        if (!self::verificarUsuario($this->alias)) {
            $claveHasheada = password_hash($this->clave, PASSWORD_DEFAULT);
            try {
                Conexion::conectar();
                $insertarUsuario = Conexion::getConexion()->prepare("INSERT INTO USUARIO (usuario, nombre, fecha_nacimiento, peso, clave) VALUES (:usu, :nom, :nac, :peso, :clave)");
                $insertarUsuario->bindParam(":usu", $this->alias);
                $insertarUsuario->bindParam(":nom", $this->nombre);
                $insertarUsuario->bindParam(":nac", $this->nacimiento);
                $insertarUsuario->bindParam(":peso", $this->peso);
                $insertarUsuario->bindParam(":clave", $claveHasheada);
                $resultado = $insertarUsuario->execute();
            } catch (Exception $e) {
                echo $e->getMessage();
                $resultado = false;
            }
        }else{
            $resultado = false;
        }
        Conexion::desconectar();
        return $resultado;
    }

    public static function verificarUsuario($usuario)
    {
        Conexion::conectar();
        $consultarUsuario = Conexion::getConexion()->prepare("SELECT * FROM USUARIO WHERE USUARIO = :usuario");
        $consultarUsuario->bindParam(':usuario', $usuario);
        $consultarUsuario->execute();
        Conexion::desconectar();
        return $consultarUsuario->rowCount() > 0;
    }

    public static function logear($usuario, $clave){

    }
}
