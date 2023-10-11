<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'auxiliar.php';
require 'conexion.php';

$clases_label = [];
$clases_input = [];
$errores = ['alta' => [], 'baja' => [], 'trabajo' => []];

$alta   = obtener_post('alta');
$baja   = obtener_post('baja');
$trabajo = obtener_post('trabajo');
$guardar = obtener_post("guardar");


if (isset($_SESSION['fila'])) {
    $filaSerializada = $_SESSION['fila'];
    $fila = unserialize($filaSerializada); }

$id_usuario = obtener_get("id");



if(isset($guardar)) {

    if(isset($alta, $baja, $trabajo)) {
        
            if(!preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/", $alta)) {
                $errores['alta'][] = 'No es una fecha válida.';
            }
            
            if(!preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/", $baja)) {
                $errores['baja'][] = 'No es una fecha válida.';
            }
            
            if(mb_strlen($trabajo) > 25) {
                $errores['trabajo'][] = 'Demasiado largo.';
            }

            if(empty($trabajo)) {
                $errores['trabajo'][] = 'Tienes que introducir el ';
            }

            if($baja < $alta) {
                $errores['baja'][] = 'No es una fecha de baja válida.';
            }

    $vacio = true;
    
    foreach ($errores as $err) {
        if (!empty($err)) {
            $vacio = false;
            break;

        }
        
    }

    if ($vacio) {
        $query = pg_query($con, "INSERT INTO trabajos (nombre, id_usuario, alta_trabajo, baja_trabajo) VALUES ('$trabajo', $id_usuario, '$alta', '$baja')");
        header('Location: perfil.php');
        exit();
    
    } else {
        foreach (['alta', 'baja', 'trabajo'] as $e) {
            if (isset($error[$e])) {
                $clases_input[$e] = $clases_input_error;
                $clases_label[$e] = $clases_label_error;
            }
        }
    }
    

    }
}



?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">  
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="registro.css">
    <title>Nuevo Registro de Trabajo</title>
</head>

<body>
    <div class="inicio">
        
        <ul> 
            <a href="index.php"> 
                Inicio 
            </a>
        </ul>

        <ul> 
            <a href="perfil.php"> 
                Perfil 
            </a>
        </ul>

        <ul> 
            <a> 
                Contacto 
            </a>
        </ul>
        
    </div>
    <div class="formulario">
        <h1>Nuevo Registro:</h1>
        <form method="POST" action="">
        <h2> Introduce el nombre del nuevo registro: </h2>
                <label>
                    <input type="text" name="trabajo" value="<?= $trabajo ?>">
                    <?php foreach ($errores['trabajo'] as $err): ?>
                            <p>¡Error!</span> <?= $err ?></p>
                    <?php endforeach ?>
                </label>
            <h2> Fecha de alta: </h2>
                <label>
                    <input type="date" name="alta" value="<?= $alta ?>">
                    <?php foreach ($errores['alta'] as $err): ?>
                            <p>¡Error!</span> <?= $err ?></p>
                    <?php endforeach ?>
                </label>
            <h2> Fecha de baja: </h2>
                <label>
                    <input type="date" name="baja" value="<?= $baja ?>">
                    <?php foreach ($errores['baja'] as $err): ?>
                            <p>¡Error!</span> <?= $err ?></p>
                    <?php endforeach ?>
                </label>
                <br>
                <label>
                    <input type="submit" value="guardar" name="guardar" class="boton"></input>
                </label>
        </form>
    </div>


    
</body>

</html>
