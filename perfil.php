<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'conexion.php';

if (isset($_SESSION['fila'])) {
    $filaSerializada = $_SESSION['fila'];
    $fila = unserialize($filaSerializada);

    $resultado = pg_query($con, "SELECT * FROM trabajos WHERE id_usuario = " . $fila['id']);

    if ($resultado) {

?>
<!DOCTYPE html>
<html lang="es">

<head class="header">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="perfil.css">
    <title>Perfil</title>
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
        <a href="contacto.php"> 
            Contacto 
        </a>
    </ul>
</div>

<div>
    <h2>Vida laboral de <?= $fila['nombre'] . ' ' . $fila['apellidos'] ?>:</h2>

    <table class="tabla">
        <tr>
            <th>Puesto</th>
            <th>Fecha de Alta</th>
            <th>Fecha de Baja</th>
        </tr>
        <?php while ($row = pg_fetch_assoc($resultado)) : ?>
            <tr>
                <td><?= $row['nombre'] ?></td>
                <td class="fecha-alta"><?= $row['alta_trabajo'] ?></td>
                <?php if($row['baja_trabajo'] == null): ?>
                    <td> Activo </td>
                <?php else : ?>
                    <td class="fecha-baja"><?= $row['baja_trabajo'] ?></td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </table>
    
    <div class="parrafo_boton">
        <form method="POST" action="registro.php?id=<?= $fila["id"] ?>">
        <a href="registro.php?id=<?= $fila["id"] ?>"> <button type="submit" class="boton"> Nuevo registro </button> </a>
        </form>
    </div>
</div>

</body>

</html>

<?php
    } else {
        echo "Error en la consulta: " . pg_last_error($con);
    }
} else {
    echo "No se pudo cargar la información del usuario.";
}
?>
