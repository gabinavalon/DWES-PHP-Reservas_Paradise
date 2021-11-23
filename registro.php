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

            if (($password) != ($password2)) {
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

        if (empty($_FILES['foto']['name'])) {
            $usuario->setFoto("fotogenerica.png");
        } else {
            //Copiar foto
            //Generamos un nombre para la foto
            $nombre_foto = md5(time() + rand(0, 999999));
            $extension_foto = substr($_FILES['foto']['name'], strrpos($_FILES['foto']['name'], '.') + 1);
            //Limpiamos la extensión de la foto
            $extension_foto = filter_var($extension_foto, FILTER_SANITIZE_SPECIAL_CHARS);
            //Comprobamos que no exista ya una foto con el mismo nombre, si existe calculamos uno nuevo
            while (file_exists("imagenes/$nombre_foto.$extension_foto")) {
                $nombre_foto = md5(time() + rand(0, 999999));
            }
            //Redimensionamos la imagen
            $image = $_FILES['foto']['tmp_name'];
           // $new_height = 150;       
            //$image = imagescale($image, $new_width= -1, $new_height);
            //movemos la foto a la carpeta que queramos guardarla y con el nombre original
            move_uploaded_file($image, "imagenes/$nombre_foto.$extension_foto");
            $usuario->setFoto("$nombre_foto.$extension_foto");
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
