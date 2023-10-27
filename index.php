<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['datos_usuario'])) {
    $datos_usuario = $_SESSION['datos_usuario'];
}

$diasFestivosCadiz = [
    '2023-01-01', // Año Nuevo
    '2023-01-06', // Día de Reyes
    '2023-02-28', // Día de Andalucía
    '2023-04-14', // Viernes Santo
    '2023-05-01', // Día del Trabajador
    '2023-06-24', // Día de San Juan
    '2023-08-15', // Asunción de la Virgen
    '2023-09-08', // Día de la Virgen de la Palma
    '2023-10-12', // Día de la Hispanidad
    '2023-11-01', // Día de Todos los Santos
    '2023-12-06', // Día de la Constitución
    '2023-12-08', // Inmaculada Concepción
    '2023-12-25'  // Navidad
];

$fechaAlta = DateTime::createFromFormat('d/m/Y', $datos_usuario['alta']);
$fechaBaja = DateTime::createFromFormat('d/m/Y', $datos_usuario['baja']);
if(!$fechaBaja) {
    $fechaBaja = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
}

$fechaNacimiento = DateTime::createFromFormat('d/m/Y', $datos_usuario['fecha_nac']);


$diasCotizados = 0;
$interval = new DateInterval('P1D');
$period = new DatePeriod($fechaAlta, $interval, $fechaBaja);

foreach ($period as $date) {
    if ($date->format('N') <= 5) {
        if (!in_array($date->format('Y-m-d'), $diasFestivosCadiz)) {
            $diasCotizados++;
        }
    }
}

$aniosCotizados = floor($diasCotizados / 365);
$mesesCotizados = floor(($diasCotizados % 365) / 30);
$diasCotizados = $diasCotizados % 30;

$edadJubilacionAnios = 65;
$edadJubilacionMeses = 10;

$fechaActual = new DateTime();

$fechaJubilacion = clone $fechaNacimiento;
$fechaJubilacion->modify("+$edadJubilacionAnios years +$edadJubilacionMeses months");


$interval = $fechaActual->diff($fechaJubilacion);

$anios = $interval->y;
$meses = $interval->m;
$dias = $interval->d;

if (!$fechaAlta || !$fechaBaja || !$fechaNacimiento) {
    exit("Error al procesar las fechas");
}

$_SESSION['datos_usuario'] = $datos_usuario;

?>

<!DOCTYPE html>
<html lang="es">

<head class="header">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Bienvenido</title>
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

    <div class="parrafo_inicio">
        <?php if (isset($datos_usuario) && is_array($datos_usuario)) : ?>
            <h1> ¡Bienvenido, <?= $datos_usuario['usuario'] ?>! </h1>
            <h2> Has cotizado <?= $aniosCotizados ?> años, <?= $mesesCotizados ?> meses y <?= $diasCotizados ?> días </h2>
            <p>Este cómputo de periodos se ha realizado considerando que las correspondientes cotizaciones han sido ingresadas. </p>
            <div class="linea-horizontal"></div>
            <h2> Te quedan <?= $anios ?> años, <?= $meses ?> meses y <?= $dias ?> días para tu jubilación ordinaria. </h2>
        <?php else : ?>
            <p>No se pudo cargar la información del usuario.</p>
        <?php endif; ?>
    </div>
</body>

</html>