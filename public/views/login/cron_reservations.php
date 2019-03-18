<?php
/**
 * 
 * Se ejecuta 1 vez al dia
 * 1. Consulta los espacios libre para el dia actual
 * 2. Insertar los espacios libre en una tabla temporal
 * 3. Se llama la funcion asignar_reservas();
 * 4. asignar_reservas(); Hace:
 *      - Busca las reserva activas del DIA vigente
 *      - Asigna o selecciona cada una de las reservas pro jornada (Tarde, dia. mañana)
 *        las funciones: (asignar_dia(), asignar_manana(), asignar_tarde()) buscan un espacio disponible
 *        en la tabla temporar de reservas segun su jornada, si hay disponible actualiza el registro
 *        con la funcion actulizar_registro_reserva()
 *      - actulizar_registro_reserva():Acualiza el registro en la tabla tempora asignado el id del usuario
 *        a un espacio, llama a la funcion actulizar_registro_reserva_asignada()
 *      - actulizar_registro_reserva_asignada(): actualiza en la tabla reserva, pasa de activa a asignado, si hay lugar
 *        disponible en parquedero. llama la funcion asignar_espacio()
 *      - asignar_espacio(): asigna el espacio al usuario valinadon si el espacio etsa disponible.
 *      
 *      Fin del proceso, solo se deberia ejcutar este archivo 1 vez al dia.
 * 
 * 
 * 
 */
date_default_timezone_set('America/Bogota');
$servername = "localhost";
$username = "root";
//$password = "Mysqlparkbd";
$password = "Mysqlparkbd";
$dbname = "bd_park";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// fecha actual
$dia = date("d");
$mes = date("m");
$anio = date("Y");
$sql = "SELECT * FROM `spacelibres` WHERE `dia`=$dia and `mes`=$mes and `anio`=$anio  ORDER BY `spacelibres`.`hora` ASC ";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Consultar espacios disponibles para hoy
    while($row = mysqli_fetch_assoc($result)) {
        echo "id: " . $row["espacioid"]. " - Name: " . $row["nombre"]. " " . $row["dia"]. " " . $row["mes"]. " " . $row["anio"]. "<br>";
        $fecha_contatenada =  $row["anio"]."-". $row["mes"]."-". $row["dia"];
        $joranada;
        if($row["hora"]==='Libre de 14:00 a 18:00'){
            $joranada = 'TARDE';
        }
        if($row["hora"]==='Libre de 8:00 a 12:00'){
            $joranada = 'MANANA';
        }
        if($row["hora"]==='Libre Todo el dia'){
            $joranada = 'DIA';
        };
        $espacioid = $row['espacioid'];
        $nombre = $row['nombre'];
        $idpiso = $row['idpiso'];
        $nombrepiso = $row['nombrepiso'];
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
        echo $sql;
        // Almacenar espacios disponibles para hoy en una tabla temporar se adicionan 3 campos
        // para controlar que joprnada esta siendo ocupada
        if (mysqli_query($conn, $sql)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
    asignar_reservas();
} else {
    echo "0 results";
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
function consultar_si_exite_tb_temp_usuario($id_espacio){
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
mysqli_close($conn);
?>