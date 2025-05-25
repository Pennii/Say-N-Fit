<?php
require_once __DIR__ . '/../modelos/Ejercicio.php';

// Prueba 1: Guardar ejercicio
$ejercicio = [
    "nombre" => "Push-up",
    "nivel" => "Principiante",
    "descripcion" => "Flexiones básicas",
    "musculos" => "Pecho, Tríceps"
];

$result = Ejercicio::guardarEjercicio($ejercicio);
if ($result) {
    echo "Prueba guardarEjercicio PASÓ\n";
} else {
    echo "Prueba guardarEjercicio FALLÓ\n";
}

// Prueba 2: Obtener ejercicio
$resultado = Ejercicio::obtenerEjercicio("Push-up");
if (is_array($resultado) && $resultado["nombre"] === "Push-up") {
    echo "Prueba obtenerEjercicio PASÓ\n";
} else {
    echo "Prueba obtenerEjercicio FALLÓ\n";
}

// Prueba 3: Eliminar ejercicio
$result = Ejercicio::eliminarEjercicio("Push-up");
if ($result) {
    echo "Prueba eliminarEjercicio PASÓ\n";
} else {
    echo "Prueba eliminarEjercicio FALLÓ\n";
}
