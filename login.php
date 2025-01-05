<?php

    if (filter_has_var(INPUT_POST,"registrarse")) {
        header("Location: registro.php");
    }else if (filter_has_var(INPUT_POST, "ingresar") ) {
        # code...
    }

