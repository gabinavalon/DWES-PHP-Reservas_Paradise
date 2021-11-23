<?php

session_start(); //Permite utilizar variables de sesión
require 'util/MensajesFlash.php';
require 'util/Sesion.php';
require 'modelos/Usuario.php';
require 'DAO/UsuarioDAO.php';
require 'modelos/Reserva.php';
require 'DAO/ReservaDAO.php';
require 'util/ConexionBD.php';

$reserva = new Reserva();
$conn = ConexionBD::conectar();
$reDao = new ReservaDAO($conn);
$id_usario = Sesion::obtener();
$fecha = filter_var($_POST['fecha'], FILTER_SANITIZE_SPECIAL_CHARS);
$hora = filter_var($_POST['hora'], FILTER_VALIDATE_INT);

$reserva->setId_usuario($id_usario);
$reserva->setFecha($fecha);
$reserva->setHora($hora);

if ($reDao->insert($reserva)) {

    MensajesFlash::anadir_mensaje("Reserva realizada con éxito");
    header("Location: misreservas.php");
    die();
} else {
    MensajesFlash::anadir_mensaje("No se ha podido realizar la reserva, inténtelo otra vez");
    header("Location: reservas.php");
    die();
}
?>
