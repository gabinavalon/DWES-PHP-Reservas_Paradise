<?php

/**
 * Modelo de usuarios
 *
 * @author Gabriel Navalón
 */
class Usuario {
  
    private $id;
    private $nombre;
    private $apellidos;
    private $telefono;
    private $email;
    private $password;
    private $foto;
    //cookie
    private $cookie_id;
    //reservas realizadas por este usuario
    private $reservas;
    
    
    //GETTEr
    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getApellidos() {
        return $this->apellidos;
    }

    function getTelefono() {
        return $this->telefono;
    }

    function getEmail() {
        return $this->email;
    }

    function getPassword() {
        return $this->password;
    }

    function getFoto() {
        return $this->foto;
    }

    function getCookie_id() {
        return $this->cookie_id;
    }

    function getReservas() {
        return $this->reservas;
    }

    //SETTER
    
    function setId($id): void {
        $this->id = $id;
    }

    function setNombre($nombre): void {
        $this->nombre = $nombre;
    }

    function setApellidos($apellidos): void {
        $this->apellidos = $apellidos;
    }

    function setTelefono($telefono): void {
        $this->telefono = $telefono;
    }

    function setEmail($email): void {
        $this->email = $email;
    }

    function setPassword($password): void {
        $this->password = $password;
    }

    function setFoto($foto): void {
        $this->foto = $foto;
    }

    function setCookie_id($cookie_id): void {
        $this->cookie_id = $cookie_id;
    }

    function setReservas($reservas): void {
        $this->reservas = $reservas;
    }


    
    
}
