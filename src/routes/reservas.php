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
//traer las reservas asigandas por id usuario
$app->get('/api/reservations/asg/{iduser}', function(Request $request, Response $response){
    $id = $request->getAttribute('iduser');
    $sql = "SELECT * FROM `tb_asignacion_reserva_temp` WHERE `ocupado_m`=$id OR `ocupado_t` =$id OR `ocupado_dia` =$id";
    
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
// Numero de reservas asignadas
$app->get('/api/reservationsnumber', function(Request $request, Response $response){
    $id = $request->getAttribute('iduser');
    $sql = "SELECT COUNT(*) As total FROM `tb_reservas` WHERE `estado` = 'ASIGNADO'
    ";
    
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
    $prioridad = $request->getParam('pri');
    $sql = "INSERT INTO `tb_reservas`
    (`id_reserva`, `fecha_creacion`, `jornada`, `tb_usuario_id_usuario`, `estado`, `prioridad`) 
    VALUES (NULL, CURRENT_TIMESTAMP, '$jornada', $iduser, 'ACTIVA', $prioridad);";
 
    $data = insert($sql);
    asignar_reserva_despues(); 
    
     $data = "[{'mess':'error'}]";
    return $response->withStatus(204)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
});
//Actulizar player ID
$app->post('/api/playerid', function(Request $request, Response $response){
    $playerid = $request->getParam('playerid');
    $iduser = $request->getParam('iduser');
    $result = actualizar_playerId($iduser, $playerid);
    
    if($result === true){
        $arr = array('message' => 'Actualizado');
        return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($arr));

    }else{
        $arr = array('message' => 'Error al actualizar');
        echo $result;
        return $response->withStatus(201)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($arr));
    }
});
 
/**
 * ****** Reservas despues de ejecutador el archivo cron_reservation.php ************
 * Se tiene en cuentas: 
 * 1. Que ocurre cuando se libera un espacio despues de la asigancion automatica del archivo cron_reservation.php
 *      - Se libera un espacio:
 *        Se inserta en la tabla tempora de la reserva
 *      - Buscar reservas activas y asignar los espacios a los usuarios que cumplan las condiciones de jornada, es decir
 *        que úeda utilizarlos.
 *      - Si un usuario libera el espacio que estaba utilizando.
 *      - Notificar usuarios PUSH     
 * 
 */
//asignar reservas ejecutar
$app->get('/api/reservationsafter', function(Request $request, Response $response){
 
    asignar_reserva_despues();
 
    $data = array('message' => 'Asignacion de reservas ejecutado');
    return $response->withStatus(201)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
});
 function asignar_reserva_despues(){
    asignar_reservas();
 }
/***
 * Al momento de que ocurra una liberacion se guarda el registro 
 * en tb_asignacion_reserva_temp desdepues de las 6.15 am
 * solo se registra para el mismo dia
 */
 function registrar_liberacion($d, $m, $a, $jornada, $espacioid){
     
    $iduser =  obtener_id_usuario_por_id_espacio($espacioid);
    if($iduser!==""){
        echo $iduser.'registrar_liberacion <br>';

        acomularMillas($iduser, '100');
    }
    $hora = strtotime(date("H:i:s")); 
    $hoy = strtotime(date("d-m-Y"));
    $fecha_entrada = strtotime($d."-".$m."-".$a);
    $hora_permmitida = strtotime(date("06:15:00"));   
    if($hoy === $fecha_entrada){
        if($hora > $hora_permmitida){
            $servername = "localhost";
            $username = "root";
            //$password = "Mysqlparkbd";
            $password = "Mysqlparkbd";
            $dbname = "bd_park";
            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
    
            $joranada;
            if($jornada==='Libre de 14:00 a 18:00'){
                $joranada = 'TARDE';
            }
            if($jornada==='Libre de 8:00 a 12:00'){
                $joranada = 'MANANA';
            }
            if($jornada==='Libre Todo el dia'){
                $joranada = 'DIA';
            };
    
            $fecha_contatenada = $a."-".$m."-".$d;
            $nombre = obtener_nombreespacio_por_idespacio($espacioid);
            $idpiso = obtener_idpiso_por_idespacio($espacioid);
            $nombrepiso = obtener_numeropiso_por_idpiso($idpiso);
            $sql ="INSERT INTO `tb_asignacion_reserva_temp` 
            (`id_asignacion_temp`,
            `id_espacio`,
            `numero_espacio`,
            `idpiso`,
                `pisonumero`, 
                `fecha`, 
                `jornada`, 
                `ocupado_m`,
                `ocupado_t`, 
                `ocupado_dia`) 
            VALUES 
            (NULL, 
            '$espacioid',
            '$nombre', 
            '$idpiso', 
            '$nombrepiso',
            '$fecha_contatenada', '$joranada', '0', '0', '0');";
            // echo $sql;
            // Almacenar espacios disponibles para hoy en una tabla temporar se adicionan 3 campos
            // para controlar que joprnada esta siendo ocupada
            if (mysqli_query($conn, $sql)) {
            // echo "New record created successfully";
                asignar_reserva_despues();
            } else {
            // echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }// fin control hora
    }  


 }
 /***
  * Si se elimina un aliberacion se debe eliminar de tb_asignacion_reserva_temp
  * si no est ausado ni tenga reserva  
  */
  function eliminar_liberacion(){

  }

function obtener_nombreespacio_por_idespacio($id_espacio){
    $resultado = ""; 
    $sql="SELECT numero FROM `tb_espacio` WHERE id_espacio = $id_espacio";
    //echo $sql . '<br>';
    try{
    // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        foreach($stmt as $row)
        $numero = $row[0];
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        //echo 'echo CF '. $numero . '<br>';
        if($numero){
            $resultado = $numero;
        }else{ $resultado = "vacio";}
    } catch(PDOException $e){
            //echo '{"error": {"text": '.$e->getMessage().'}';
            $resultado = $e->getMessage();
    }
    //echo $resultado;
    return $resultado;
}
function obtener_idpiso_por_idespacio($id_espacio){
    $resultado = ""; 
    $sql="SELECT id_piso FROM `tb_espacio` WHERE id_espacio = $id_espacio";
    //echo $sql . '<br>';
    try{
    // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        foreach($stmt as $row)
        $numero = $row[0];
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        //echo 'echo CF '. $numero . '<br>';
        if($numero){
            $resultado = $numero;
        }else{ $resultado = "vacio";}
    } catch(PDOException $e){
            //echo '{"error": {"text": '.$e->getMessage().'}';
            $resultado = $e->getMessage();
    }
    //echo $resultado;
    return $resultado;
}
function obtener_numeropiso_por_idpiso($idpiso){
    $resultado = ""; 
    $sql="SELECT numero FROM `tb_piso` WHERE id_piso = $idpiso";
    //echo $sql . '<br>';
    try{
    // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        foreach($stmt as $row)
        $numero = $row[0];
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        //echo 'echo CF '. $numero . '<br>';
        if($numero){
            $resultado = $numero;
        }else{ $resultado = "vacio";}
    } catch(PDOException $e){
            //echo '{"error": {"text": '.$e->getMessage().'}';
            $resultado = $e->getMessage();
    }
    //echo $resultado;
    return $resultado;
}
function obtener_id_usuario_por_id_espacio($idespacio){
    $resultado = ""; 
    $sql="SELECT `id_usuario` FROM `tb_espacio` WHERE `id_espacio`=$idespacio";
    //echo $sql . '<br>';
    try{
    // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        foreach($stmt as $row)
        $numero = $row[0];
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        //echo 'echo CF '. $numero . '<br>';
        if($numero){
            $resultado = $numero;
        }else{ $resultado = "vacio";}
    } catch(PDOException $e){
            //echo '{"error": {"text": '.$e->getMessage().'}';
            $resultado = $e->getMessage();
    }
    //echo $resultado;
    return $resultado;
}
 /**
 * Asignar espacios a reserva
 */
function asignar_reservas(){
    $servername = "localhost";
    $username = "root";
    //$password = "Mysqlparkbd";
    $password = "Mysqlparkbd";
    $dbname = "bd_park";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "SELECT * FROM `tb_reservas` WHERE `estado`='ACTIVA' ORDER BY `tb_reservas`.`prioridad` ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if($row["jornada"]==='TODO EL DIA'){
                echo 'Encontro reserva DIA'. "<br>";
                asignar_dia($row["tb_usuario_id_usuario"], $row["id_reserva"]);
                /*** Notiicar reserva al usuario */
                send_push_by_isuser($row["tb_usuario_id_usuario"],  "Tu reserva a sido asignada para todo el dia.");
            }
            if($row["jornada"]==='MAÑANA'){
                echo 'Encontro reserva MAÑANA'. "<br>";
                asignar_manana($row["tb_usuario_id_usuario"], $row["id_reserva"]);
                send_push_by_isuser($row["tb_usuario_id_usuario"],  "Tu reserva a sido asignada para la mañana.");
            }
            if($row["jornada"]==='TARDE'){
                echo 'Encontro reserva TARDE'. "<br>";
                asignar_tarde($row["tb_usuario_id_usuario"], $row["id_reserva"]);
                send_push_by_isuser($row["tb_usuario_id_usuario"],  "Tu reserva a sido asignada para la tarde.");
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
    $dbname = "bd_park";
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
    $dbname = "bd_park";
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
    $dbname = "bd_park";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    $sql = "SELECT * FROM `tb_asignacion_reserva_temp` WHERE  `jornada`='DIA' AND `ocupado_dia`= 0 AND `ocupado_t`= 0 AND `ocupado_m`= 0";
    $result = $conn->query($sql);

    $busco_manana = false;

    if ($result->num_rows > 0) {
        // output data of each row id_espacio
        while($row = $result->fetch_assoc()) {
            echo 'econtro un lugar en reserva temp asignar_dia <br>';
            actulizar_registro_reserva($id_usuario, 'ocupado_dia', $row["id_asignacion_temp"], $id_reserva, $row["id_espacio"]);
            return 0;
            break; 
        }
    } else {
        // echo "0 results";
        /**
         * Buscar si puede asignarle un tarde
         */
        $sql = "SELECT * FROM `tb_asignacion_reserva_temp` WHERE (`jornada`='TARDE' OR `jornada`='DIA')  AND `ocupado_t`= 0 AND `ocupado_dia`= 0";
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
            // Buscar por lo menos una mañana
            asignar_manana($id_usuario, $id_reserva);
            $busco_manana = true;
        }
        //En caso de asignar la tarde no entraria en ell 'else' y por solo tener la tarde asignada
        // se buscara una mañana
        if($busco_manana === false){
            asignar_manana($id_usuario, $id_reserva);
        }
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
    $dbname = "bd_park";
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
    $dbname = "bd_park";
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
    $dbname = "bd_park";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    //$validacion = consultar_si_exite_tb_temp_usuario($id_espacio);
    //echo $validacion.' HV' . '<br>';
    echo 'espacio id, user: '.$id_espacio .''. $id_user. '<br>';
        $sql="INSERT INTO `tb_temp_usuario` (`id_temp`, `fecha`, `estado`, `id_usuario`, `id_espacio`)
        VALUES (NULL, '".date("d/m/Y")."', '1', $id_user, $id_espacio);";
        echo $sql .'<br>';
        if (mysqli_query($conn, $sql)) {
            echo "asignar_espacio" .$id_espacio ." - ". $id_user;
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }        
}
//validar si ya se esta usando el parquedero evistar duplicidad
function consultar_si_exite_tb_temp_usuario_2($id_espacio){
    $servername = "localhost";
    $username = "root";
    //$password = "Mysqlparkbd";
    $password = "Mysqlparkbd";
    $dbname = "bd_park";
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
/**
 * Actulizar PlayerId usuario 
 * Nora: se guardara en el coampo token
 * para no modificar la estrucura de la tabla, el campo no tenia uso.
 */
function actualizar_playerId($id_user, $player_id){
    $servername = "localhost";
    $username = "root";
    //$password = "Mysqlparkbd";
    $password = "Mysqlparkbd";
    $dbname = "bd_park";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "UPDATE `tb_usuario` 
    SET `token` = '".$player_id."' 
    WHERE `tb_usuario`.`id_usuario` = $id_user;";  

    echo $sql;

    if ($conn->query($sql) === TRUE) {
        $resultado =  true;
     } else {
        $resultado =  "Error updating record: " . $conn->error;
    }
    return $resultado;
}

function obtner_token_playerid($iduser){//validar si ya se esta usando el parquedero evistar duplicidad
         
    $resultado = ""; 
    $sql="SELECT `token` FROM `tb_usuario` WHERE `id_usuario` = $iduser";
    //echo $sql."<br>";
    try{
        // Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();
            $stmt = $db->query($sql);
            foreach($stmt as $row)
            $player = $row[0];
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            //echo 'echo CF '. $id_espacio . '<br>';
            if($player){
                $resultado = $player;
            }else{ $resultado = "vacio";}
        } catch(PDOException $e){
                //echo '{"error": {"text": '.$e->getMessage().'}';
                $resultado = $e->getMessage();
        }
       //echo $resultado;
       return $resultado;
}
function send_push_by_isuser($iduser, $msj){
    $playerId = obtner_token_playerid($iduser);
    if($playerId){
        $res = sendMessageByPlayerId($msj, 'fb175834-d0c9-4d86-a697-67eb887ffe7b');
    }else{

    }
}


?>
