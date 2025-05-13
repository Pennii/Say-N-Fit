<?php
//Funcion que sanea el texto de variables que iran a la bd
function sanear_texto($texto){
    $texto = trim($texto);
    $texto = strip_tags($texto);
    $texto = htmlspecialchars($texto);
    return $texto;
}

function obtenerDia($dia)
{
    switch ($dia) {
        case 'domingo':
            $numero = 0;
            break;
        case 'lunes':
            $numero = 1;
            break;
        case 'martes':
            $numero = 2;
            break;
        case 'miercoles':
            $numero = 3;
            break;
        case 'jueves':
            $numero = 4;
            break;
        case 'viernes':
            $numero = 5;
            break;
        default:
            $numero = 6;
            break;
    }
    return $numero;
}