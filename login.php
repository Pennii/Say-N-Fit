<?php
if (filter_input(INPUT_SERVER, "REQUEST_METHOD")==="POST") {
    if (filter_has_var(INPUT_POST,"registrarse")) {
        header("Location: /registro.php");
    }else{
        
    }
}
