<?php
session_start(); //Permite utilizar variables de sesión
require 'util/MensajesFlash.php';
require 'util/Sesion.php';
require 'modelos/Usuario.php';
require 'DAO/UsuarioDAO.php';
require 'modelos/Reserva.php';
require 'DAO/ReservaDAO.php';
require 'util/ConexionBD.php';




if (Sesion::existe() == false) {
    header("Location: index.php");
    MensajesFlash::anadir_mensaje("Debes iniciar sesión para hacer una reserva");
    die();
}

$hayFecha = "false";
$conn = ConexionBD::conectar();
$reDao = new ReservaDAO($conn);

if (isset($_GET["fechaElegida"])) {
    $fechaElegida = filter_var($_GET['fechaElegida'], FILTER_SANITIZE_SPECIAL_CHARS);
    $fechaElegida = $_GET["fechaElegida"];
    $hayFecha = "true";




    $fechaSQL = date('Y-m-d', strtotime($fechaElegida));
    

    $reservas = $reDao->findbyDate($fechaSQL);
    
    $existeHoras = false;
    
    if ($reservas) {
        foreach ($reservas as $r) {

        for ($h = 9; $h < 16; $h++) {
            if ($r->getHora() == $h) {
                $horasOcupadas[] = $h;
                $existeHoras = true;
            }
        }
    }
    }else{
        $horasOcupadas = [0];
    }
 

   

    // Ahora hay que ver que al mandar el post compruebe:
    // Que no se ha mandado una fecha anterior o no válida
    // Que no se ha mandado una hora ocupada o no válida
} 
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
        //Validamos el token
        if ($_POST['token'] != $_SESSION['token']) {
        header('Location: index.php');
        MensajesFlash::anadir_mensaje("Token incorrecto");
        die();
    }
        
        
        //Variables
        $id_usario = Sesion::obtener(); 
        $error = false;
        
        $fecha_actual = strtotime(date("d-m-Y",time()));
       
	//Validaciones
        //La fecha se ha elegido
        if (empty($_POST['fecha'])) {
        MensajesFlash::anadir_mensaje("Debe seleccionar una fecha");
        $error = true;
        }
        //La fecha no es anterior o igual a la actual
        if($_POST['fecha'] <= $fecha_actual){
	$error = true;
        MensajesFlash::anadir_mensaje("Las reservas deben ser con un día de antelación");
	}
        
        //Se ha seleccionado una hora
        if (empty($_POST['hora'])) {
        MensajesFlash::anadir_mensaje("Debe seleccionar una hora para reservar");
        $error = true;
        }
        //La hora no es una que está reservada
        //Si hay alguna hora ocupada en ese dia, se comprueba que la elegida no es igual a una ocupada
        if ($existeHoras) {
            foreach ($reservas as $r) {
                if ($r->getHora()==$hora) {
                    $error = true;
                    MensajesFlash::anadir_mensaje("Esta hora ya está reservada");
                }
            }
        }
         
        
        if (!$error) {
            $fecha = filter_var($_POST['fecha'], FILTER_SANITIZE_SPECIAL_CHARS);
            $hora = filter_var($_POST['hora'], FILTER_VALIDATE_INT);
            
            /*$reserva = new Reserva();
            
            
            $reserva->setId_usario($id_usario);
            $reserva->setFecha($fecha);
            $reserva->setHora($hora);
            
            $reDao->insert($reserva);*/
            
            
            header("Location: confirmacion.php?fecha=$fecha&hora=$hora");
            die();
            
            
        }
  
     
      
        
       
        
        
    }
    
    //Calculamos un token
    $token = md5(time() + rand(0,999));
    $_SESSION['token']=$token;


?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Haz tu reserva</title>

        <script>
            window.onload = function () {
                var fecha = new Date(); //Fecha actual
                var mes = fecha.getMonth() + 1; //obteniendo mes
                var dia = fecha.getDate() + 1; //obteniendo dia siguiente 
                var ano = fecha.getFullYear(); //obteniendo año
                if (dia < 10)
                    dia = '0' + dia; //agrega cero si el menor de 10
                if (mes < 10)
                    mes = '0' + mes //agrega cero si el menor de 10
                document.getElementById('fechaActual').min = ano + "-" + mes + "-" + dia;
                // Se debe reservar con un dia de antelación
            }
        </script>
    </head>
    <body>
        <h1>Haz tu reserva</h1>
        <form name="reservar" method="POST">
            <input type="hidden" name="token" value="<?= $token ?>">
            <input type="date" name="fecha" id="fechaActual"  value="<?php
            if (isset($fechaElegida)) {
                echo $fechaElegida;
            }
            ?>" >
            <br><br>
            <select id="seleccionHoras" style="display: none" name="hora">
                <?php for ($i = 9; $i < 16; $i++) {
                    ?>

                    <option value="<?= $i ?>" style="background-color: greenyellow" class="hora" id="<?= $i ?>"><?= $i ?>:00</option>

                <?php }
                ?>

            </select>
            <br><br> 
            <input type="submit" value="Reservar" name="submit" />
            <input type="button" value="Reset"  id="reset"/>
            <?php MensajesFlash::imprimir_mensajes(); ?>
        </form>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script type="text/javascript">

            $("#fechaActual").change(function () {


                var fechaElegida = document.getElementById('fechaActual').value;
                window.location.href = "reservas.php?fechaElegida=" + fechaElegida;
            });
            
            $("#reset").click(function () {
                window.location.href = "reservas.php";
            });

        </script>
        <script type="text/javascript">
            var hayFecha = <?= $hayFecha ?>;
            var classes = document.getElementsByClassName("hora"); // Do not use a period here!
            var horas = Array.prototype.map.call(classes, function (el) {
                return el.value;
            });
            if (hayFecha) {
<?php if (isset($horasOcupadas)) { ?>

                        var horasOcupadas = <?php echo json_encode($horasOcupadas); ?>;

                        for (var i = 0; i < horas.length; i++) {
                            for (var j = 0; j < horas.length; j++) {
                                if (horas[i] == horasOcupadas[j]) {


                                    document.getElementById(horasOcupadas[j]).style.backgroundColor = 'red';
                                    document.getElementById(horasOcupadas[j]).disabled = true;
                                }
                            }
                        }


                        $("#seleccionHoras").fadeIn("slow");

<?php } ?>

            }
            

       
        </script>
</html>
