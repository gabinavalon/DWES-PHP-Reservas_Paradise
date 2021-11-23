<?php
session_start(); //Permite utilizar variables de sesión
require 'util/MensajesFlash.php';
require 'util/Sesion.php';
require 'modelos/Usuario.php';
require 'DAO/UsuarioDAO.php';
require 'modelos/Reserva.php';
require 'DAO/ReservaDAO.php';
require 'util/ConexionBD.php';

if (Sesion::existe() == false) {
    header("Location: index.php");
    MensajesFlash::anadir_mensaje("Debes iniciar sesión para hacer una reserva");
    die();
}

//Calculamos un token
    $token = md5(time() + rand(0,999));
    $_SESSION['token']=$token;



    $fecha_final = $_GET['fecha'];
    $hora_final = $_GET['hora'];


?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Confirmación</title>
    </head>
    <body>
        <h1>Confirmación de la reserva</h1>
        <h3>Ha reservado el día : <?= $fecha_final?></h3>
        <h3>A la hora : <?= $hora_final?>:00</h3>
        <h3>¿Desea confirmar esta reserva?</h3>
        <form name="realizarReserva" action="realizarReserva.php" method="POST">
            <input type="hidden" name="token" value="<?= $token ?>">
            <input type="hidden" name="fecha" value="<?= $fecha_final?>">
            <input type="hidden" name="hora" value="<?= $hora_final?>">
            <input type="submit" value="Realizar reserva" />
            
        </form>
        <button onclick="window.location.href = 'reservas.php'">Cancelar reserva</button>
        
      
     
    </body>
</html>
