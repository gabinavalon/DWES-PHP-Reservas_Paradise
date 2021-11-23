<?php
/**
 * Description of ConexionBD
 *
 * @author Gabriel Navalón
 */
class ConexionBD {
    
    public static function conectar(): mysqli{
        $conn = new mysqli('localhost','root','','reservas');
        if($conn->connect_error){
            die("Error al conectar con MySQL: " . $conn->error);
        }
        return $conn;
    }
}

