<?php

function sanear_texto($texto){
    $texto = trim($texto);
    $texto = strip_tags($texto);
    $texto = htmlspecialchars($texto);
    return $texto;
}