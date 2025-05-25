<?php
require_once __DIR__ . '/../modelos/Grupo.php';

// Datos de prueba
$nombreGrupo = "GrupoTest";
$claveGrupo = "clave123";
$liderGrupo = "admin";
$usuarioNormal = "usuarioNormal";

// Crear objeto Grupo
$grupo = new Grupo($nombreGrupo, $claveGrupo, $liderGrupo);

// 1. Prueba: Crear grupo
echo "Prueba crearGrupo: ";
if ($grupo->crearGrupo($liderGrupo)) {
    echo "PASÓ\n";
} else {
    echo "FALLÓ\n";
}

// 2. Prueba: Ver grupo
echo "Prueba verGrupo: ";
$datos = Grupo::verGrupo($claveGrupo);
if ($datos && $datos['clave'] === $claveGrupo && $datos['nombre'] === $nombreGrupo) {
    echo "PASÓ\n";
} else {
    echo "FALLÓ\n";
}

// 3. Prueba: Ver grupos por usuario (lider debe tener el grupo)
echo "Prueba verGruposPorUsuario (lider): ";
$gruposUsuario = Grupo::verGruposPorUsuario($liderGrupo);
$existe = false;
foreach ($gruposUsuario as $g) {
    if ($g['clave'] === $claveGrupo) {
        $existe = true;
        break;
    }
}
echo $existe ? "PASÓ\n" : "FALLÓ\n";

// 4. Prueba: Encontrar grupo e incorporar usuario normal
echo "Prueba encontrarGrupo: ";
if (Grupo::encontrarGrupo($claveGrupo, $usuarioNormal)) {
    echo "PASÓ\n";
} else {
    echo "FALLÓ\n";
}

// 5. Prueba: Verificar líder (el lider debe ser reconocido)
echo "Prueba verificarLider: ";
if (Grupo::verificarLider($claveGrupo, $liderGrupo)) {
    echo "PASÓ\n";
} else {
    echo "FALLÓ\n";
}

// 6. Prueba: Listar usuarios del grupo (usuario normal debe aparecer, lider no)
echo "Prueba listarUsuarios: ";
$usuarios = Grupo::listarUsuarios($claveGrupo);
$usuariosNombres = array_column($usuarios, 'USUARIO');
if (in_array($usuarioNormal, $usuariosNombres)) {
    echo "PASÓ\n";
} else {
    echo "FALLÓ\n";
}

// 7. Prueba: Abandonar grupo (usuario normal abandona, debe pasar)
echo "Prueba abandonarGrupo (usuario normal): ";
if (Grupo::abandonarGrupo($usuarioNormal, $claveGrupo)) {
    echo "PASÓ\n";
} else {
    echo "FALLÓ\n";
}

// 8. Prueba: Abandonar grupo (lider abandona, grupo debe ser eliminado o cambiar líder)
echo "Prueba abandonarGrupo (lider): ";
if (Grupo::abandonarGrupo($liderGrupo, $claveGrupo)) {
    echo "PASÓ\n";
} else {
    echo "FALLÓ\n";
}

echo "Pruebas finalizadas.\n";
