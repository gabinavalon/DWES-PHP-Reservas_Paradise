<?php
session_start(); //Permite utilizar variables de sesión
require 'util/MensajesFlash.php';
require 'util/Sesion.php';
require 'modelos/Usuario.php';
require 'DAO/UsuarioDAO.php';
require 'modelos/Reserva.php';
require 'DAO/ReservaDAO.php';
require 'util/ConexionBD.php';


$conn = ConexionBD::conectar();
$reDao = new ReservaDAO($conn);
$id_usuario = Sesion::obtener();
$reservas_usuario = $reDao->findbyUser($id_usuario);


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
        <title>Mis reservas</title>
        <style type="text/css">
            header{
                overflow: auto;
            }
            #usuario,#login{
                width: 300px;
                float: right;
            }
            #login input{
                margin-top: 3px;
            }
            #titulo{
                margin-right: 300px;
                text-align: center;
                font-size: 1.5em;
            }
            .boton_formulario{
                border:1px solid black;
                box-sizing: border-box;
                display: inline-block;
                padding: 3px;
                background-color: #eee;
                text-decoration: none;
                color:black;

            }
            .fotos_articulo{
                height: 100px;
                background-size: contain;
                background-position: center;
                background-repeat:no-repeat;
            }
            .articulo_listado{
                float: left;
                min-height: 100px;
                border: 1px solid black;
                margin: 5px;
                padding: 5px;
                position: relative;
                width: 150px;
            }
            .papelera{
                height: 20px;
                opacity: 0.5;
            }
            .papelera:hover{
                opacity: 1;
            }
            .borrar_reserva{
                position: absolute;
                bottom:5px;
                right:5px;
                width: 20px;
                height: 20px;
            }
            main{
                overflow: auto;
            }
            .precio_articulo{

                font-weight: bold;
                color:#f00;
                width: 100px;
                padding: 3px;
                text-align: center;
                margin: auto;
                font-family: verdana;
            }
            .contactar{
                font-size: 1em;
                font-weight: bold;
                color:#00f;
                border: 1px solid black;
                width: 120px;
                padding: 3px;
                border-radius: 50px;
                text-align: center;
                margin: 5px auto;   
                font-family: verdana;
            }
            .contactar:hover{
                background-color: #aaa;
                cursor: pointer;
                transition: 0.5s all;
                width: 130px;
            }
            menu{
                overflow: auto;
                border-bottom: 1px solid black;
                border-top: 1px solid black;
                margin: 0px 5px;
                padding: 0px;
            }
            ul#menu_usuario{
                margin: 0px;
                padding: 0px;
            }
            ul#menu_usuario li{
                margin: 0px;
                padding: 5px;
                list-style-type: none;
                float: left;
                border: 1px solid white;
                cursor: pointer;
                background-color: #eee;
            }
            ul#menu_usuario li:hover{
                background-color: #aaa;
            }
            ul#menu_usuario li a{
                text-decoration: none;
                color:black;
                cursor: pointer;
            }
             #foto_usuario{
                height: 80px;
                width: 80px;
                background-size: cover;
                background-position: center;
                border-radius: 40px;
                box-shadow: 0px 0px 5px 0px #aaa;
                margin: 5px;
            }
            #datos_usuario{
                top:40px;
                left: 100px;
            }
            
            #formulario_cambiar_foto{
                display: none;
            }

        </style>

        
    </head>
    <body>
        <?php foreach ($reservas_usuario as $r): ?>
                <div class="articulo_listado">
                    <div class="titulo_articulo"><?= date("d-m-Y", strtotime($r->getFecha())) ?></div>
                    
                    <div class="precio_articulo"><?= $r->getHora() ?> :00</div>
                    <div class="contactar">Modificar</div>
                    
                    
                    <?php if ($r->getId_usuario() == Sesion::obtener()): ?>
                        <div class="borrar_reserva"><a href="borrar.php?id=<?= $r->getId() ?>"><img src="iconos/trash.svg" class="papelera"></a></div>
                            <?php endif; ?>

                </div>
            <?php endforeach; ?>
        <br>    
        <?php MensajesFlash::imprimir_mensajes(); ?>
        <a href="reservas.php">Hacer otra reserva</a>
    </body>
</html>
