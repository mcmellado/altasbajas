<?php session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="es">

<head class="header">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
    <div class="backGradient" id='header'>
    <div height='auto'>
        <img class='logo_cab' src='imagenes/logo_blanco.png'>
        <h3>GESTIÓN DE BAJAS Y ALTAS</h3>
    </div>
    </div>
</head>


<body>
<form action="" method="POST">

<div class="acceso">
    <p> ACCESO </p>
</div>

<div>
    <input class="login" type="text" name="login" id="login" placeholder="Usuario">
    <input class="password" type="password" name="password_login" id="password_login" placeholder="password">

</div>
<button type="submit" class="boton"> Entrar </button>
</form>
</body>
</html>

<?php

// require 'conexion.php';
require 'auxiliar.php';

$login          = obtener_post('login');
$password_login = obtener_post('password_login');

function comprobar($login, $password_login){
    require 'conexion.php';
    $comprobar_login    = pg_query($con, "SELECT * FROM usuarios WHERE usuario = '$login'");

    if(pg_num_rows($comprobar_login) != 0){

        $datos_usuario = array(
            "usuario"            => $fila['usuario'],
            "password"           => $fila['password'],
            "intentos_fallidos"  => $fila['intentos_fallidos'],
            "bloqueo"            => $fila['bloqueo'],
            "fecha_nac"          => $fila['fecha_nacimiento'],
            "alta"               => $fila['alta'],
            "baja"               => $fila['baja'],
        );

        if($password == $password_login) {
            $_SESSION['datos_usuario'] = $datos_usuario;
            header('Location: index.php');

        } else {
            echo "El usuario o contraseña es incorrecto";
        }

    } else {
        echo "No existen registros";
    }
}

if(isset($login, $password_login)){
    comprobar($login, $password_login);
}

?>

