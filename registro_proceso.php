<?php
//Conectamos con la base de datos y asignamos los valores del formulario de registro a un array
$conexion = new PDO("mysql:host=localhost;dbname=say n fit", "root", "");
$datos = filter_input_array(INPUT_POST);

//Codificamos la clave del usuario
$datos["clave"] = hash("sha256", $datos["clave"]);
$usuario = $datos["usuario"];
//Verificamos que el usuario no exista en la base de datos
try {
    $verificarUsuario = $conexion->query("SELECT * FROM USUARIO WHERE USUARIO = '$usuario'");
} catch (\Throwable $th) {
    echo $th->getMessage();
}

//Si el usuario no existe lo creamos, si existe mostramos un mensaje de error
if (!$verificarUsuario->rowCount()) {
    try {
        $conexion->beginTransaction();
        $insertarUsuario = $conexion->prepare("INSERT INTO USUARIO VALUES(:usu, :nom, :nac, :peso, :clave)");
        $insertarUsuario->bindParam(":usu", $datos["usuario"]);
        $insertarUsuario->bindParam(":nom", $datos["nombre"]);
        $insertarUsuario->bindParam(":nac", $datos["nacimiento"]);
        $insertarUsuario->bindParam(":peso", $datos["peso"]);
        $insertarUsuario->bindParam(":clave", $datos["clave"]);
        $insertarUsuario->execute();
        $conexion->commit();
        $imagen = $_FILES["imagen"];
        if ($imagen) {
            $tipo = $imagen["type"];
            switch ($tipo) {
                case 'image/jpg':
                    $extension = "jpg";
                    break;
                case 'image/png':
                    $extension = "png";
                    break;
                case 'image/jpeg':
                    $extension = "jpeg";
                    break;
            }
            if (isset($extension)) {
                $ubicacion = "iconos_usuarios/$usuario.$extension";
                move_uploaded_file($imagen["tmp_name"], $ubicacion);
            }
        }
    } catch (\Throwable $th) {
        $conexion->rollBack();
    }
}else{
    setcookie("usuarioExistente", "El usuario ya existe", time()+2);
    header("Location: vistas/registro.html");
}