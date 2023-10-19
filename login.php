<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'conexion.php'; 
require 'auxiliar.php';

$login = pg_escape_string($con, obtener_post('login'));
$password_login = obtener_post('password_login');

function comprobar($login, $password_login, $con) {
    $comprobar_login = pg_query($con, "SELECT * FROM usuarios WHERE usuario = '$login'");
    
    if (!$comprobar_login) {
        error_log("Error en la consulta: " . pg_last_error($con));
        return "Error en la base de datos. Por favor, inténtalo de nuevo más tarde.";
    }

    if(pg_num_rows($comprobar_login) != 0) {
        $fila = pg_fetch_assoc($comprobar_login);
        $intentos_fallidos = $fila['intentos_fallidos'];
        $bloqueo = $fila['bloqueado'];

        $datos_usuario = array(
            "usuario"            => $fila['usuario'],
            "password"           => $fila['password'],
            "intentos_fallidos"  => $fila['intentos_fallidos'],
            "bloqueo"            => $fila['bloqueado'],
            "fecha_nac"          => $fila['fecha_nacimiento'],
            "alta"               => $fila['alta'],
            "baja"               => $fila['baja'],
        );

        if ($bloqueo) {
            $tiempo = strtotime($fila['tiempo']);
            $tiempo_actual = strtotime('now');
            $diferencia_tiempo = $tiempo_actual - $tiempo;
            if($diferencia_tiempo >= 900) {
                pg_query($con, "UPDATE usuarios SET bloqueado = FALSE, intentos_fallidos = 0, tiempo = NULL WHERE usuario = '$login'");
            } else {
                $error = "El usuario $login está bloqueado durante 15 minutos.";
                return $error;
            }
        }

        if (password_verify($password_login, $fila['password'])) {
            pg_query($con, "UPDATE usuarios SET intentos_fallidos = 0, tiempo = NULL WHERE usuario = '$login'");
            $_SESSION['datos_usuario'] = $datos_usuario;
            header('Location: index.php');
            exit();
        } else {
            $intentos_fallidos++;
            pg_query($con, "UPDATE usuarios SET intentos_fallidos = $intentos_fallidos, tiempo = NOW() WHERE usuario = '$login'");
            if($intentos_fallidos >= 3) {
                pg_query($con, "UPDATE usuarios SET bloqueado = TRUE WHERE usuario = '$login'");
            }
            return "El usuario o la contraseña son incorrectos.";
        }
    } else {
        return "El usuario no existe.";
    }
}

if(isset($login, $password_login)) {
    $error = comprobar($login, $password_login, $con);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
</head>

<body>
    <div class="backGradient" id='header'>
        <div height='auto'>
            <img class='logo_cab' src='imagenes/logo_blanco.png' alt="Logo">
            <h3>GESTIÓN DE BAJAS Y ALTAS</h3>
        </div>
    </div>
    <div class="acceso">
        <p> ACCESO </p>
    </div>
    <form action="" method="POST">
        <div>
            <input class="login" type="text" name="login" id="login" placeholder="Usuario" required>
            <input class="password" type="password" name="password_login" id="password_login" placeholder="Contraseña" required>
        </div>
        <?php if(isset($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>
        <button type="submit" class="boton"> Entrar </button>
    </form>
</body>

</html>
