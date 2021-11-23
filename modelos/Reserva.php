<?php

/**
 * Modelo de reservas
 *
 * @author Gabriel Navalón
 */
class Reserva {
    
    private $id;
    private $id_usuario;
    private $fecha;
    private $hora;
    
    //Propiedad para acceder a los datos del usuario al que pertenece la reserva
    private $usuario;
    
    //Getter
    function getId() {
        return $this->id;
    }

    function getId_usuario() {
        return $this->id_usuario;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getHora() {
        return $this->hora;
    }

    function getUsuario() {
        if (!isset($this->usuario)) {
            $usuarioDAO = new UsuarioDAO(ConexionBD::conectar());
            $this->usuario = $usuarioDAO->find($this->getId_usuario());
        }
        return $this->usuario;
    }

    //Setter
    function setId($id): void {
        $this->id = $id;
    }

    function setId_usuario($id_usuario): void {
        $this->id_usuario = $id_usuario;
    }

    function setFecha($fecha): void {
        $this->fecha = $fecha;
    }

    function setHora($hora): void {
        $this->hora = $hora;
    }

    function setUsuario($usuario): void {
        $this->usuario = $usuario;
    }



}
