<?php

/**
 * Description of ReservaDAO
 *
 * @author Gabriel Navalón 
 */
class ReservaDAO {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    /**
     * Inserta una reserva en la base de datos
     * @param Reserva $reserva
     * @return boolean true si se ha insertado y false si no se inserta
     */
    public function insert($reserva) {
        if (!$reserva instanceof Reserva) {
            return false;
        }

        $id_usuario = $reserva->getId_usuario();
        $fecha = $reserva->getFecha();
        $hora = $reserva->getHora();

        $sql = "INSERT INTO RESERVAS (id_usuario, fecha, hora) VALUES (?,?,?)";

        $stmt = $this->conn->prepare($sql); // preparamos la consulta
        if (!$stmt) { // si no se puede preparar, error
            die("Error en la SQL: " . $this->conn->error);
        }

        $stmt->bind_param('isi', $id_usuario, $fecha, $hora);
        $stmt->execute();
        $result = $stmt->get_result();

        //Guardo el id que le ha asignado la base de datos en la propiedad id del objeto
        $reserva->setId($this->conn->insert_id);
        return true;
    }
    /**
     * Actualiza los datos de una reserva en la base de datos
     * @param Reserva $reserva
     * @return boolean true si se actualiza false si no
     */
    public function update($reserva) {
        //Comprobamos que el parámetro es de la clase Usuario
        if (!$reserva instanceof Reserva) {
            return false;
        }
        $fecha = $reserva->getFecha();
        $hora = $reserva->getHora();
        $id = $reserva->getId();
        
        $sql = "UPDATE reservas SET"
                . " fecha=?, hora=? WHERE id = ? ";
        
        if (!$stmt = $this->conn->prepare($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }

        $stmt->bind_param("sii", $fecha, $hora, $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($this->conn->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Elimina una reserva de la base de datos
     * @param type $reserva
     * @return boolean true si se elimina la reserva false si no
     */
    public function delete($reserva) {
        //Comprobamos que el parámetro no es nulo y es de la clase Usuario
        if ($reserva == null || get_class($reserva) != 'Reserva') {
            return false;
        }
        $sql = "DELETE FROM reservas WHERE id = " . $reserva->getId();
        if (!$result = $this->conn->query($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }
        if ($this->conn->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Busca en la base de datos una reserva en base a su id
     * @param type $id
     * @return type Reserva
     */
    public function find($id) {
        $sql = "SELECT * FROM reservas WHERE id=$id";
        if (!$result = $this->conn->query($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }
        return $result->fetch_object('Reserva');
    }
    /**
     * Busca las reservas que ha realizado un usuario
     * @param type $id
     * @return type array de reservas
     */
     public function findbyUser($id) { 
        $sql = "SELECT * FROM reservas WHERE id_usuario=$id";
        
        if (!$result = $this->conn->query($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }
        $array_obj_reservas = array();
        while ($reserva = $result->fetch_object('Reserva')) {
            $array_obj_reservas[] = $reserva;
        }
        return $array_obj_reservas;
     }
     /**
      * Busca todas las reservas existentes en la base de datos
      * @param type $orden
      * @param type $campo
      * @return type array de reservas
      */
      public function findAll($orden = 'ASC', $campo = 'id') {
        $sql = "SELECT *,date_format(fecha,'%e/%c/%Y') as fecha FROM reserva ORDER BY $campo $orden";
        if (!$result = $this->conn->query($sql)) {
            die("Error en la SQL: " . $this->conn->error);
        }
        
        $array_obj_reservas = array();
        while ($reserva = $result->fetch_object('Reserva')) {
            $array_obj_reservas[] = $reserva;
        }
        return $array_obj_reservas;
    }
    /**
     * Busca en la base de datos una reserva para una fecha dada
     * @param type $fecha
     * @return array de Reservas
     */
    public function findbyDate($fecha){
        
         $sql = "SELECT * FROM reservas WHERE fecha='$fecha'";
         if (!$result = $this->conn->query($sql)) {
            die("Error en la SQL: " . $this->conn->error);
            }
             $array_obj_reservas = array();
             
         while ($reserva = $result->fetch_object('Reserva')) {
            $array_obj_reservas[] = $reserva;
        }
        return $array_obj_reservas;
    }
}
