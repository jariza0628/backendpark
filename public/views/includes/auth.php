<?php
include_once('conn.php');

$pass = md5('123456');
$sql = "SELECT * FROM `tb_usuario` WHERE `email`='jariza' and `clave`= '".$pass."'";
echo $sql;
$result = $conexion->query($sql);


if ($result->num_rows > 0) {     
 }
 $row = $result->fetch_array(MYSQLI_ASSOC);
 echo $row['clave'];
 if (($pass == $row['clave'])) { 
 
    $_SESSION['loggedin'] = true;
    //$_SESSION['username'] = $username;
    $_SESSION['start'] = time();
    $_SESSION['expire'] = $_SESSION['start'] + (5 * 60);

    //echo "Bienvenido! " . $_SESSION['username'];
    echo "<br><br><a href=panel-control.php>Panel de Control</a>"; 

 } else { 
   echo "Username o Password estan incorrectos.";

   echo "<br><a href='login.html'>Volver a Intentarlo</a>";
 }

?>