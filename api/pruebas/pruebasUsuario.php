<?php
require_once __DIR__ . '/../modelos/Usuario.php';


// Variables de prueba
$alias = 'prueba_usuario';
$nombre = 'Usuario Prueba';
$nacimiento = '1990-01-01';
$peso = 75;
$clave = '123456';

// 1. Insertar nuevo usuario
$usuario = new Usuario($alias, $nombre, $nacimiento, $peso, $clave);
$resultado = $usuario->insertarUsuario();
echo "1. Insertar usuario: " . ($resultado ? "PASÓ" : "FALLÓ (¿ya existe?)") . "\n";

// 2. Ver usuario
$datos = Usuario::verUsuario($alias);
echo "2. Ver usuario: " . ($datos ? "PASÓ" : "FALLÓ") . "\n";
var_dump($datos);

// 3. Logear correctamente
$loginCorrecto = Usuario::logear($alias, $clave);
echo "3. Logear con clave correcta: " . ($loginCorrecto ? "PASÓ" : "FALLÓ") . "\n";

// 4. Logear con clave incorrecta
$loginIncorrecto = Usuario::logear($alias, 'clave_incorrecta');
echo "4. Logear con clave incorrecta: " . (!$loginIncorrecto ? "PASÓ" : "FALLÓ") . "\n";

// 5. Actualizar datos del usuario
$nuevoNombre = 'Usuario Actualizado';
$nuevoPeso = 80;
$nuevaClave = 'nueva123';

$usuarioActualizado = new Usuario($alias, $nuevoNombre, $nacimiento, $nuevoPeso, $nuevaClave);
$actualizacion = $usuarioActualizado->actualizarDatos($alias);
echo "5. Actualizar datos: " . ($actualizacion === true ? "PASÓ" : "FALLÓ: $actualizacion") . "\n";

// 6. Verificar cambios en BD
$datosActualizados = Usuario::verUsuario($alias);
echo "6. Ver usuario actualizado:\n";
var_dump($datosActualizados);

// 7. Login con nueva clave
$loginNuevo = Usuario::logear($alias, $nuevaClave);
echo "7. Logear con nueva clave: " . ($loginNuevo ? "PASÓ" : "FALLÓ") . "\n";

// Limpieza final (opcional)
