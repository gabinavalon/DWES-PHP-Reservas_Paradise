<?php 
session_start(); //Permite utilizar variables de sesión
require 'util/ConexionBD.php';
require 'util/MensajesFlash.php';
require 'util/Sesion.php';
require 'DAO/UsuarioDAO.php';

$conn = ConexionBD::conectar();
//cREAMOS UN TOKEN DE SESION
$_SESSION['token']= md5(time()+rand(0,999));
$token = $_SESSION['token'];

if(isset($_COOKIE['uid']) && Sesion::existe()==false ){ //Si existe la cookie lo identificamos
    $uid = filter_var($_COOKIE['uid'], FILTER_SANITIZE_SPECIAL_CHARS);
    $usuarioDAO = new UsuarioDAO(ConexionBD::conectar());
    $usuario = $usuarioDAO->findByCookie_id($uid);
    if($usuario != false)   //Si existe un usuario con la cookie iniciamos sesión
    {
        Sesion::iniciar($usuario->getId());
        header('Location: misreservas.php');
        die();
    }
}
 
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="estiloLogin.css">
        <style>
            input.pw {
                -webkit-text-security: disc;
                }
        </style>
        
    </head>
    <body>
        <div class="wrapper fadeInDown">
  <div id="formContent">
    <!-- Tabs Titles -->
    <h2 class="active"> Login </h2>
    <h2 class="inactive underlineHover"><a class="underlineHover" href="registro.php">Registro</a></h2>

    <!-- Icon -->
    <div class="fadeIn first">
        <img src="imagenes/usuario_login.png" id="icon" alt="User Icon" style="width:  100px"/>
    </div>

    <!-- Login Form -->
    <form id="login" action="login.php" method="post">
      <input type="text" id="login" class="fadeIn second" name="email" placeholder="e-mail">
      <input type="text" id="password" class="fadeIn third, pw" name="password" placeholder="password">
      <input type="submit" class="fadeIn fourth" value="Login">
    </form>

    <!-- Remind Passowrd -->
    <div id="formFooter">
      <a class="underlineHover" href="#">¿Olvidaste tu contraseña?</a>
    </div>
<?php MensajesFlash::imprimir_mensajes(); ?>
  </div>
</div>
    </body>
</html>
