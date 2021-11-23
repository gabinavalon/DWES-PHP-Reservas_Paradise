<?php
session_start();


require 'util/ConexionBD.php';
require 'modelos/Usuario.php';
require 'DAO/UsuarioDAO.php';
require 'util/MensajesFlash.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
   
    //Comprobamos el token TODO
    if ($_POST['token'] != $_SESSION['token']) {
        header('Location: index.php');
        MensajesFlash::anadir_mensaje("Token incorrecto");
        die();
    }

    $usuario = new Usuario();
    $error = false;
    
    if (empty($_POST['email'])) {
        MensajesFlash::anadir_mensaje("El email es obligatorio.");
        $error = true;
    }else{ //comprobamos que el email no exite en la base de datos
       $usuDAO = new UsuarioDAO(ConexionBD::conectar());
       $usuarios = $usuDAO->findAll();
       
       foreach ($usuarios as $u) {
           $email_usu = $u->getEmail();
           
           if ($email_usu == ($_POST['email']) ) {
                MensajesFlash::anadir_mensaje("Ya existe un usuario con este email");
                $error = true;
           }
       }
       
    }
    
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        MensajesFlash::anadir_mensaje("El email no es correcto");
        $error = true;
    }

    if (empty($_POST['password'])) {
        MensajesFlash::anadir_mensaje("El password es obligatorio.");
        $error = true;
    }
    if (empty($_POST['apellidos'])) {
        MensajesFlash::anadir_mensaje("Introduzca sus apellidos.");
        $error = true;
    }
    if (empty($_POST['telefono'])) {
        MensajesFlash::anadir_mensaje("Introduzca su teléfono.");
        $error = true;
    }
    if (empty($_POST['password'])) {
        MensajesFlash::anadir_mensaje("El password es obligatorio.");
        $error = true;
    }else {
        if (empty($_POST['password2'])) {
            MensajesFlash::anadir_mensaje("Verifique su contraseña.");
            $error = true;
        } else {

            if (($_POST['password']) != ($_POST['password2'])) {
                MensajesFlash::anadir_mensaje("Las contraseñas no coinciden");
                $error = true;
            }
        }
    }
    
    if (empty($_POST['nombre'])) {
        MensajesFlash::anadir_mensaje("Introduzca su nombre.");
        $error = true;
    }
    
    //Validación formato y tamaño de foto
   if (!empty($_FILES['foto']['name'])) {
  
  
    if ($_FILES['foto']['type'] != 'image/png' &&
            $_FILES['foto']['type'] != 'image/gif' &&
            $_FILES['foto']['type'] != 'image/jpeg') {
        MensajesFlash::anadir_mensaje("El archivo seleccionado no es una foto.");
        $error = true;
    }
    if ($_FILES['foto']['size'] > 1000000) {
        MensajesFlash::anadir_mensaje("El archivo seleccionado es demasiado grande. Debe tener un tamaño inferior a 1MB");
        $error = true;
    
        
    }
    }
    

    

    if (!$error) {

        if ($_FILES['foto']['name'] = null) {
            $usuario->setFoto("usuario_login.png");
            } else {
                
                 if ($_FILES['profilePicture']['name'] != null) {
                    $tmpFile = filter_var($_FILES['profilePicture']['tmp_name'], FILTER_SANITIZE_SPECIAL_CHARS);
                    $pictureFileName = filter_var($_FILES['profilePicture']['name'], FILTER_SANITIZE_SPECIAL_CHARS);
                    $pictureExtension = substr($pictureFileName, strrpos($pictureFileName, '.'));
                    $pictureNewName = md5(time()+rand(0,999999)).$pictureExtension;
            //Guardar nombre foto temporal
                $fototemp = filter_var($_FILES['foto']['tmp_name'], FILTER_SANITIZE_SPECIAL_CHARS);
            //Guardar nombre foto
                $nombrefoto = filter_var($_FILES['foto']['name'], FILTER_SANITIZE_SPECIAL_CHARS);
            //Guardar extensión de la foto
                $extension_foto = substr($nombrefoto, strrpos($nombrefoto, '.'));
            //Limpiamos la extensión de la foto
                $extension_foto = filter_var($extension_foto, FILTER_SANITIZE_SPECIAL_CHARS);
            //Comprobamos que no exista ya una foto con el mismo nombre, si existe calculamos uno nuevo
                $nombre_foto_final = md5(time()+rand(0,999999)).$extension_foto;
            
            do {
                $nombre_foto_final = md5(time() + rand(0, 999999)) . $extension_foto;
            } while (file_exists("imagenes/$nombre_foto_final"));  //por si existiera ya un archivo con ese nombre
            
             move_uploaded_file($fototemp, "imagenes/$nombre_foto_final");

            list($width, $height, $type) = getimagesize($fototemp);
            $width = ($width*150)/$height;
            $height = 150;

            if ($type == IMAGETYPE_JPEG) {
                $img = imagecreatefromjpeg($fototemp);
                $imgResized = imagescale($img, $width, $height);
                imagejpeg($imgResized, $nombre_foto_final);
            } elseif ($type == IMAGETYPE_PNG) {
                $img = imagecreatefrompng($fototemp);
                $imgResized = imagescale($img, $width, $height);
                imagepng($imgResized, $nombre_foto_final);
            } elseif ($type == IMAGETYPE_GIF) {
                $img = imagecreatefromgif($fototemp);
                $imgResized = imagescale($img, $width, $height);
                imagejpeg($imgResized, $nombre_foto_final);
            }
            
           

            $usuario->setFoto($nombre_foto_final);
            }
   



//Limpiamos los datos de entrada

        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_SPECIAL_CHARS);
        $apellidos = filter_var($_POST['telefono'], FILTER_SANITIZE_SPECIAL_CHARS);
        $telefono = filter_var($_POST['apellidos'], FILTER_SANITIZE_SPECIAL_CHARS);

        //Insertamos el usuario en la BBDD
        $usuario->setEmail($email);
        $usuario->setNombre($nombre);
        $usuario->setApellidos($apellidos);
        $usuario->setTelefono($telefono);
        $usuario->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT));


        $usuDAO = new UsuarioDAO(ConexionBD::conectar());
        $usuDAO->insert($usuario);
        MensajesFlash::anadir_mensaje("Usuario creado.");
        header('Location: index.php');
        die();
    
        
            }
            
}

}


//Calculamos un token
$token = md5(time() + rand(0, 999));
$_SESSION['token'] = $token;
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php MensajesFlash::imprimir_mensajes() ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= $token ?>">
            <input type="text" name="nombre" placeholder="Nombre" value="<?php if(isset($nombre)){echo $nombre; }?>"><br>
            <input type="text" name="apellidos" placeholder="Apellidos" value="<?php if(isset($apellidos)){echo $apellidos; }?>"><br>
            <input type="text" name="telefono" placeholder="Telefono" value="<?php if(isset($telefono)){echo $telefono; }?>"><br>
            <input type="email" name="email" placeholder="Email" value="<?php if(isset($email)){echo $email; }?>"><br>
            <input type="password" name="password" placeholder="Password"><br>
            <input type="password" name="password2" placeholder="Repita contraseña"><br>
            <input type="file" name="foto" accept="image/*"><br>
            <input type="submit" value="registrar">
            <input type="button" value="volver" onclick="location.href = 'index.php'">
        </form>
    </body>
</html>
