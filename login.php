<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'conexion.php';
require 'auxiliar.php';

echo "<br><br><br><br><br><br><br>";

$login = obtener_post('login');
$password_login = obtener_post('password_login');
$mensajeError = '';
$intentos_adicionales = 3;
$tiempoRestante = 0;
$texto = "";

if ($tiempoRestante <= 0) {
    $tiempoRestante = 0;
}

function comprobar($login, $password_login, $con) {
    $comprobar_login = pg_query($con, "SELECT * FROM usuarios WHERE usuario = '$login'");
    $fila = pg_fetch_assoc($comprobar_login);

    if (pg_num_rows($comprobar_login) != 0) {
        $datos_usuario = array(
            "usuario"            => $fila['usuario'],
            "password"           => $fila['password'],
            "intentos_fallidos"  => $fila['intentos_fallidos'],
            "bloqueo"            => $fila['bloqueo'],
            "tiempo"             => $fila['tiempo']
        );

        if ($datos_usuario["bloqueo"] && strtotime($datos_usuario["tiempo"]) > time()) {
            $tiempoRestante = strtotime($datos_usuario["tiempo"]) - time();
            $mensajeError = "Usuario bloqueado. Inténtalo de nuevo en " . gmdate("i:s", $tiempoRestante) . " minutos.";
            $texto = " ";
        } else {
            if ($password_login == $datos_usuario["password"]) {
                $_SESSION['datos_usuario'] = $datos_usuario;
                header('Location: index.php');
            } else {
                pg_query($con, "UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE usuario = '$login'");
                $intentos_fallidos = $datos_usuario["intentos_fallidos"] + 1;
                if ($intentos_fallidos >= 3) {
                    $tiempoBloqueo = strtotime("+5 minutes");
                    pg_query($con, "UPDATE usuarios SET bloqueo = true, tiempo = '" . date('Y-m-d H:i:s', $tiempoBloqueo) . "', intentos_fallidos = 0 WHERE usuario = '$login'");
                    $tiempoRestante = 300;
                    $mensajeError = "Usuario bloqueado. Inténtalo de nuevo en " . gmdate("i:s", $tiempoRestante) . " ";
                    $texto = "Tiempo restante: ";
                } else {
                    $mensajeError = "    El usuario o contraseña es incorrecto";
                }
            }
        }
    } else {
        $mensajeError = "No existen registros";
    }

    return $mensajeError;
    var_dump($texto);
}

if (isset($login, $password_login)) {
    $mensajeError = comprobar($login, $password_login, $con);
}
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
            <div class="container">
                <div class="div1" id="mensajeError"><?php echo substr($mensajeError, 0, 42); ?></div>
                <?php if($texto != " "): ?>
                    <div id="texto"> <?php echo $texto; ?> </div>
                <?php endif; ?>
                <div id="temporizador" class="div2"> 
            </div>
        </div>
        <button type="submit" class="boton"> Entrar </button>

        <script type="text/javascript">
            function actualizarTemporizador(tiempoRestante) {
                var minutos = Math.floor(tiempoRestante / 60);
                var segundos = tiempoRestante % 60;
                document.getElementById("temporizador").innerText = minutos + ":" + (segundos < 10 ? "0" : "") + segundos;
                
                setInterval(function() {
                    tiempoRestante--;
                    if (tiempoRestante >= 0) {
                        minutos = Math.floor(tiempoRestante / 60);
                        segundos = tiempoRestante % 60;
                        document.getElementById("temporizador").innerText = minutos + ":" + (segundos < 10 ? "0" : "") + segundos;
                    }
                }, 1000);
            }

            var mensajeError = "<?php echo $mensajeError; ?>";
            let tiempoRestante = parseInt(mensajeError.match(/\d+/)[0]);
            tiempoRestante = tiempoRestante * 60;

            if (tiempoRestante > 0) {
                actualizarTemporizador(tiempoRestante);
            }
        </script>
    </form>
</body>

</html> 