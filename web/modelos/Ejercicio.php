<?php
require_once '../modelos/Conexion.php';

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
            if (isset($filtrado)) {
                $query = substr($query, 0, strlen($query) - 4);
            }else{
                $query = substr($query, 0, strlen($query) - 7);
            }
        }
        $query .= " ORDER BY NIVEL ASC";
        $consultarEjercicios = Conexion::getConexion()->query($query);
        Conexion::desconectar();
        return $consultarEjercicios->fetchAll(PDO::FETCH_ASSOC);
    }
}
