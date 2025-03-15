<?php
//Funcion que sanea el texto de variables que iran a la bd
function sanear_texto($texto){
    $texto = trim($texto);
    $texto = strip_tags($texto);
    $texto = htmlspecialchars($texto);
    return $texto;
}