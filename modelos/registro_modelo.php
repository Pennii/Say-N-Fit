<?php
class registro_modelo {
    private $conexion;

    public function __construct() {
        $this->conexion = new PDO("mysql:host=localhost;dbname=say_n_fit", "root", "");
    }

    public function verificarUsuario($usuario) {
        $consultarUsuario = $this->conexion->prepare("SELECT * FROM USUARIO WHERE USUARIO = :usuario");
        $consultarUsuario->bindParam(':usuario', $usuario);
        $consultarUsuario->execute();
        return $consultarUsuario->rowCount() > 0;
    }

    public function insertarUsuario($datos) {
        try {
            $this->conexion->beginTransaction();
            $insertarUsuario = $this->conexion->prepare("INSERT INTO USUARIO (usuario, nombre, nacimiento, peso, clave) VALUES (:usu, :nom, :nac, :peso, :clave)");
            $insertarUsuario->bindParam(":usu", $datos["usuario"]);
            $insertarUsuario->bindParam(":nom", $datos["nombre"]);
            $insertarUsuario->bindParam(":nac", $datos["nacimiento"]);
            $insertarUsuario->bindParam(":peso", $datos["peso"]);
            $insertarUsuario->bindParam(":clave", $datos["clave"]);
            $resultado = $insertarUsuario->execute();
            $this->conexion->commit();
        } catch (Exception $e) {
            $this->conexion->rollBack();
        }
        return $resultado;
    }
}
