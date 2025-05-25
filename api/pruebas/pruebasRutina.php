<?php
require_once __DIR__ . '/../modelos/Conexion.php';
require_once __DIR__ . '/../modelos/Rutina.php';

// Variables de prueba
$usuario = 'usuarioNormal';
$codigo = 1;
$nombre = 'Rutina de prueba';
$dias = 'Lunes,Miércoles';

// 1. Crear nueva rutina
$rutina = new Rutina($codigo, $usuario, $dias, $nombre);
$resultado = $rutina->crearRutina();
echo "1. Crear rutina: " . ($resultado ? "PASÓ" : "FALLÓ") . "\n";

// 2. Ver rutina
$datos = Rutina::verRutina($codigo, $usuario);
echo "2. Ver rutina: " . ($datos ? "PASÓ" : "FALLÓ") . "\n";

// 3. Consultar código actual
$codigoActual = Rutina::consultarCodigoActual($usuario);
echo "3. Consultar código actual: codigo = " . $codigoActual['CODIGO'] . ($codigoActual && $codigoActual['CODIGO'] == $codigo ? " PASÓ" : " FALLÓ") . "\n";

// 4. Asignar ejercicio
$ejercicio = 'Plancha';
$asignado = $rutina->almacenarEjercicio($ejercicio);
echo "4. Almacenar ejercicio: " . ($asignado ? "PASÓ" : "FALLÓ") . "\n";

// 5. Obtener ejercicios
$ejercicios = Rutina::obtenerEjercicios($usuario, $codigo);
echo "5. Obtener ejercicios: " . ($ejercicios ? "PASÓ" : "FALLÓ") . "\n";

// 6. Obtener rutinas por usuario
$todas = Rutina::obtenerRutinas($usuario);
echo "6. Obtener todas las rutinas: " . ($todas ? "PASÓ" : "FALLÓ") . "\n";

// 7. Actualizar rutina
$ejerciciosActualizar = [
    ["nombre" => $ejercicio, "series" => 4, "repeticiones" => 12]
];
$rutina->nombre = "Rutina actualizada";
$rutina->dias = "Martes,Jueves";
$actualizado = $rutina->actualizarRutina($ejerciciosActualizar);
echo "7. Actualizar rutina: " . ($actualizado === true ? "PASÓ" : "FALLÓ: $actualizado") . "\n";

// 8. Obtener por día
$porDia = Rutina::obtenerRutinasPorDia($usuario, "Martes");
echo "8. Obtener rutinas por día: " . ($porDia ? "PASÓ" : "FALLÓ") . "\n";

// 9. Quitar ejercicio
$quitar = Rutina::quitarEjercicio($ejercicio, $usuario, $codigo);
echo "9. Quitar ejercicio: " . ($quitar !== 0 ? "PASÓ ($quitar)" : "FALLÓ") . "\n";

// 10. Eliminar rutina directamente (por si quedó)
$eliminada = Rutina::eliminarRutina($usuario, $codigo);
echo "10. Eliminar rutina: " . ($eliminada ? "PASÓ" : "FALLÓ") . "\n";
