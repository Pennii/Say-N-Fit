<?php
require_once 'modelos/registro_modelo.php';
class registro_controlador
{
    private $modelo;

    public function __construct() {
        $this->modelo = new registro_modelo();
    }
}
