<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

date_default_timezone_set('America/Bogota');

//traer todas las reservas
$app->get('/api/reservations', function(Request $request, Response $response){
    $sql = "SELECT * FROM `tb_reservas`";
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
//traer las reservas por id usuario
$app->get('/api/reservations/{iduser}', function(Request $request, Response $response){
    $id = $request->getAttribute('iduser');
    $sql = "SELECT * FROM `tb_reservas` WHERE `tb_usuario_id_usuario` = $id AND (`estado`='ACTIVA' OR `estado`='ASIGNADO')";
    
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
//eliminar reservas
$app->delete('/api/reservations/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "UPDATE `tb_reservas` SET `estado` = 'ELIMINADA' WHERE `tb_reservas`.`id_reserva` = $id;";
    $result = update($sql);
    //Codicion
            return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($result));
   

});
//crear reservas
$app->post('/api/reservations', function(Request $request, Response $response){
    $jornada = $request->getParam('jornada');
    $iduser = $request->getParam('iduser');

    $sql = "INSERT INTO `tb_reservas` 
    (`id_reserva`, `fecha_creacion`, `jornada`, `tb_usuario_id_usuario`, `estado`) 
    VALUES (NULL, CURRENT_TIMESTAMP, '$jornada', $iduser, 'ACTIVA');";
 
    $data = insert($sql);
    return $response->withStatus(201)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
});
/**
 * ****** Reservas despues de ejecutador el archivo cron_reservation.php ************
 * Se tiene en cuentas: 
 * 1. Que ocurre cuando se liver un espacio despues de la asigancion automatica del archivo cron_reservation.php
 *      - Se libera un espacio:
 *        Se inserta en la tabla tempora de la reserva
 *      - Buscar reservas activas y asignar los espacios a los usuarios que cumplan las condiciones de jornada, es decir
 *        que úeda utilizarlos.
 *      - Si un usuario libera el espacio que estaba utilizando.
 *      - Notificar usuarios PUSH     
 * 
 */
//asignar reservas ejecutar
$app->post('/api/reservationsafter', function(Request $request, Response $response){
 

    asignar_reserva_despues();
 
    $data = array('message' => 'Asignacion de reservas ejecutado');
    return $response->withStatus(201)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
});
 function asignar_reserva_despues(){
    asignar_reservas();
 }

 /**
 * Asignar espacios a reserva
 */
function asignar_reservas(){
    $servername = "localhost";
    $username = "root";
    //$password = "Mysqlparkbd";
    $password = "Mysqlparkbd";
    $dbname = "BD_PARK";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    $sql = "SELECT * FROM `tb_reservas` WHERE `estado`='ACTIVA'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if($row["jornada"]==='TODO EL DIA'){
                echo 'Encontro reserva DIA'. "<br>";
                asignar_dia($row["tb_usuario_id_usuario"], $row["id_reserva"]);
            }
            if($row["jornada"]==='MAÑANA'){
                echo 'Encontro reserva MAÑANA'. "<br>";
                asignar_manana($row["tb_usuario_id_usuario"], $row["id_reserva"]);
            }
            if($row["jornada"]==='TARDE'){
                echo 'Encontro reserva TARDE'. "<br>";
                asignar_tarde($row["tb_usuario_id_usuario"], $row["id_reserva"]);
            }
        
        }
    } else {
        echo "0 results";
    }
}
/** 
 * Buscar un espacio en la mañana y asigna de encontralo
 * se tienen en cuenta los que son todo el dia
 * NOTA: La prioridad siempre sera el orde de asgnacion
 */
function asignar_manana($id_usuario, $id_reserva){
    $servername = "localhost";
    $username = "root";
    //$password = "Mysqlparkbd";
    $password = "Mysqlparkbd";
    $dbname = "BD_PARK";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    $sql = "SELECT * FROM `tb_asignacion_reserva_temp` WHERE (`jornada`='MANANA' OR `jornada`='DIA') AND `ocupado_m`= 0 AND `ocupado_dia`= 0";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo 'econtro un lugar en reserva temp asignar_tarde <br>';
            actulizar_registro_reserva($id_usuario, 'ocupado_m', $row["id_asignacion_temp"], $id_reserva, $row["id_espacio"]);
            return 0;
            break; 
        }
    } else {
        echo "0 results";
    }

}
/**
 * Buscar un espacio en la tarde y asigna de encontralo
 * se tienen en cuenta los que son todo el dia
 */
function asignar_tarde($id_usuario, $id_reserva){
    $servername = "localhost";
    $username = "root";
    //$password = "Mysqlparkbd";
    $password = "Mysqlparkbd";
    $dbname = "BD_PARK";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    $sql = "SELECT * FROM `tb_asignacion_reserva_temp` WHERE (`jornada`='TARDE' OR `jornada`='DIA') AND `ocupado_t`= 0 AND `ocupado_dia`= 0";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo 'econtro un lugar en reserva temp asignar_tarde <br>';
            actulizar_registro_reserva($id_usuario, 'ocupado_t', $row["id_asignacion_temp"], $id_reserva, $row["id_espacio"]);
            return 0;
            break; 
        }
    } else {
        echo "0 results";
    }
}
/**
 * Buscar un espacio en el dia y asigna de encontralo
 */
function asignar_dia($id_usuario, $id_reserva){
    $servername = "localhost";
    $username = "root";
    //$password = "Mysqlparkbd";
    $password = "Mysqlparkbd";
    $dbname = "BD_PARK";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    $sql = "SELECT * FROM `tb_asignacion_reserva_temp` WHERE  `jornada`='DIA' AND `ocupado_dia`= 0 AND `ocupado_t`= 0 AND `ocupado_m`= 0";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row id_espacio
        while($row = $result->fetch_assoc()) {
            echo 'econtro un lugar en reserva temp asignar_dia <br>';
            actulizar_registro_reserva($id_usuario, 'ocupado_dia', $row["id_asignacion_temp"], $id_reserva, $row["id_espacio"]);
            return 0;
            break; 
        }
    } else {
        echo "0 results";
    }
    
}
/**
 * Acualiza el registro en la tabla tempora asignado el id del usuario
 * a un espacio
 */
function actulizar_registro_reserva($id_user, $campo, $id_tb_temp, $id_reserva, $id_espacio){
    echo '$id_espacio', $id_espacio;
    $servername = "localhost";
    $username = "root";
    //$password = "Mysqlparkbd";
    $password = "Mysqlparkbd";
    $dbname = "BD_PARK";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
   
    $sql = "UPDATE `tb_asignacion_reserva_temp` 
    SET $campo = $id_user 
    WHERE `tb_asignacion_reserva_temp`.`id_asignacion_temp` = $id_tb_temp;";
     echo $sql . "<br>";

    if ($conn->query($sql) === TRUE) {
        echo "UPDATEs ";
        actulizar_registro_reserva_asignada($id_reserva, $id_user, $id_espacio);
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

/**
 * Acualiza en la tabla reserva un registro asignado esto ocurre simpre que la asignacion
 * encuentre un lugar libre para que la reserva sea signada
 */
function actulizar_registro_reserva_asignada($id_reserva, $id_user, $id_espacio){
    $servername = "localhost";
    $username = "root";
    //$password = "Mysqlparkbd";
    $password = "Mysqlparkbd";
    $dbname = "BD_PARK";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $sql = "UPDATE `tb_reservas` SET `estado` = 'ASIGNADO' WHERE `tb_reservas`.`id_reserva` = $id_reserva";
     echo $sql . "<br>";
     
    if ($conn->query($sql) === TRUE) {
        echo "Record updated ASIGNADO <br>";
        asignar_espacio($id_espacio, $id_user);
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

function asignar_espacio($id_espacio, $id_user){
    echo 'Asiar espacio entro <br>';
    $servername = "localhost";
    $username = "root";
    //$password = "Mysqlparkbd";
    $password = "Mysqlparkbd";
    $dbname = "BD_PARK";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection

    $validacion = consultar_si_exite_tb_temp_usuario_2($id_espacio);
    echo $validacion.' HV' . '<br>';
    echo 'espacio id, user: '.$id_espacio .''. $id_user. '<br>';
    if($validacion=="vacio"){
        $sql="INSERT INTO `tb_temp_usuario` (`id_temp`, `fecha`, `estado`, `id_usuario`, `id_espacio`)
        VALUES (NULL, '".date("d/m/Y")."', '1', $id_user, $id_espacio);";
        echo $sql .'<br>';
        if (mysqli_query($conn, $sql)) {
            echo "asignar_espacio" .$id_espacio ." - ". $id_user;
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }        
    }

}
//validar si ya se esta usando el parquedero evistar duplicidad
function consultar_si_exite_tb_temp_usuario_2($id_espacio){
    $servername = "localhost";
    $username = "root";
    //$password = "Mysqlparkbd";
    $password = "Mysqlparkbd";
    $dbname = "BD_PARK";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
         
    $resultado = ""; 
     //echo $sql."<br>";
    $sql = "SELECT * FROM `tb_temp_usuario` WHERE `id_espacio`= ".$id_espacio."";
    echo $sql . "<br>";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        $resultado = "ocupado";
    } else {
        //echo "0 results";
        $resultado = "vacio";
    }
    return $resultado;
}
?>