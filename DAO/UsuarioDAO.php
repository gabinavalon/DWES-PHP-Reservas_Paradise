<?php

/**
 * Controlador y funcionalidad de usuarios
 *
 * @author Gabriel Navalón Soriano
 */
class UsuarioDAO {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Inserta al usuario en la base de datos
     * @param Usuario $usuario
     * @return boolean true si se inserta el usuario
     */
    public function insert($usuario) {
        //Comprobamos que el parámetro sea de la clase Usuario
        if (!$usuario instanceof Usuario) {
            return false;
        }
        $nombre = $usuario->getNombre();
        $apellidos = $usuario->getApellidos();
        $telefono = $usuario->getTelefono();
        $email = $usuario->getEmail();
        $password = $usuario->getPassword();
        $foto = $usuario->getFoto();
        $cookie_id = sha1(time() + rand());

        $sql = "INSERT INTO usuarios (nombre, apellidos, telefono, email, "
                . "password, foto, cookie_id) VALUES "
                . "(?,?,?,?,?,?,?)";
        // preparamos la consulta
        $stmt = $this->conn->prepare($sql);
        //si la conssulta no se puede preparar da error
        if (!$stmt) {
            die("Error en la SQL: " . $ths->conn->error);
        }
        //Ejecución de la consulta
        $stmt->bind_param('sssssss', $nombre, $apellidos, $telefono, $email, $password, $foto, $cookie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        //Guardo el id que le ha asignado la base de datos en la propiedad id del objeto
        $usuario->setId($this->conn->insert_id);
        return true;
    }
        /**
         * Actualiza los datos del usuario
         * @param Usuario $usuario
         * @return boolean true si actualiza el usuario (solo afecta una fila de la BBDD)
         */
      public function update($usuario) {
        //Comprobamos que el parámetro es de la clase Usuario
        if (!$usuario instanceof Usuario) {
            return false;
        }
        $id= $usuario->getId();
        $nombre = $usuario->getNombre();
        $apellidos = $usuario->getApellidos();
        $telefono = $usuario->getTelefono();
        $email = $usuario->getEmail();
        $password = $usuario->getPassword();
        $foto = $usuario->getFoto();
        $cookie_id = sha1(time() + rand());
        $sql = "UPDATE usuarios SET"
                . " nombre=?, apellidos=?, telefono=?, email=?, password=?, foto=?, cookie_id=? "
                . "WHERE id = ?";
        
        if (!$stmt = $this->conn->prepare($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }
        
        $stmt->bind_param("sssssssi", $nombre, $apellidos, $telefono, $email, $password, $foto, $cookie_id, $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($this->conn->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }
        /**
         * Borra al usuario de la base de datos
         * @param type $usuario
         * @return boolean
         */
        public function delete($usuario) {
        //Comprobamos que el parámetro no es nulo y es de la clase Usuario
        if ($usuario == null || get_class($usuario) != 'Usuario') {
            return false;
        }
        $sql = "DELETE FROM usuarios WHERE id = ?";
        
         if (!$stmt = $this->conn->prepare($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($this->conn->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Busca en la base de datos un usuario con la id indicada
     * @param type $id
     * @return type un objeto usuario
     */
       public function find($id) { //: Usuario especifica el tipo de datos que va a devolver pero no es obligatorio ponerlo
        $sql = "SELECT * FROM usuarios WHERE id=?";
        if (!$stmt = $this->conn->prepare($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_object('Usuario');
       
    }
    
    
    /**
     * Devuelve todos los usuarios de la BD
     * @param type $orden Tipo de orden (ASC o DESC)
     * @param type $campo Campo de la BD por el que se van a ordenar
     * @return array Array de objetos de la clase Usuario
     */
    public function findAll($orden = 'ASC', $campo = 'id') {
        $sql = "SELECT * FROM usuarios ORDER BY $campo $orden";
        if (!$result = $this->conn->query($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }
        $array_obj_usuarios = array();
        while ($usuario = $result->fetch_object('Usuario')) {
            $array_obj_usuarios[] = $usuario;
        }
        return $array_obj_usuarios;
    }
    
    /** 
     * Busca un usuario en la BD con el email indicado
     * @param type $email
     * @return type un objeto tipo Usuario
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email='$email'";
        
         if (!$result = $this->conn->query($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }
        return $result->fetch_object('Usuario');
       /* if (!$stmt = $this->conn->prepare($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_object('Usuario');*/
    }
    
        /**
         * Busca en la base de datos un usario con la cookie indicada
         * @param type $cookie_id
         * @return type objeto tipo Usuario
         */
       public function findByCookie_id($cookie_id) {
        $sql = "SELECT * FROM usuarios WHERE cookie_id=?";
        
        if (!$stmt = $this->conn->prepare($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }
        
        $stmt->bind_param("s", $cookie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_object('Usuario');
    }


}
