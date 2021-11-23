<?php
session_start();

require 'util/ConexionBD.php';
require 'modelos/Usuario.php';
require 'DAO/UsuarioDAO.php';
require 'util/Sesion.php';
require 'util/MensajesFlash.php';

//Obtendo el usuario, si no existe vuelvo a index con un parámetro de error
$usuDAO = new UsuarioDAO(ConexionBD::conectar());

//Limpiamos los datos de entrada

$email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);


if (!$usuario = $usuDAO->findByEmail($email)) {    
    //Usuario no encontrado
    MensajesFlash::anadir_mensaje("Email o password incorrecto.");
    header('Location: index.php');
    die();
}
//Compruebo la contraseña, si no existe vuelvo a index con un parámetro de error
if (!$_POST['password']== $usuario->getPassword()) {



   
//if (!password_verify($_POST['password'], $usuario->getPassword())) {
    //password incorrecto
    MensajesFlash::anadir_mensaje("Usuario o password incorrectos.");
    header('Location: index.php');
    die();
}
//Usuario y password correctos, redirijo al listado de anuncios
Sesion::iniciar($usuario->getId());
// Si el login es correcto, creamos una cookie
$usuario->setCookie_id(sha1(time()+rand()));
$usuDAO->update($usuario);

setcookie('uid', $usuario->getCookie_id() , time()+60*60*24*7);
header("Location: reservas.php");
die();
