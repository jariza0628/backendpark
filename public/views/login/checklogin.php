<?php
session_start();

$host_db = 'localhost';
$user_db = 'root';
$pass_db = '';
$db_name = 'bd_park';
$tbl_name = 'tb_usuario';

$conexion = new mysqli($host_db, $user_db, $pass_db, $db_name);

if ($conexion->connect_error) {
 die("La conexion falló: " . $conexion->connect_error);
}
$username = preg_replace('([^A-Za-z0-9])', '', $_POST['username']);
$password = md5(preg_replace('([^A-Za-z0-9])', '', $_POST['password']));
 
$sql = "SELECT * FROM $tbl_name WHERE email = '$username' and rol = 1";
//echo $username."<br>";
//echo preg_replace('([^A-Za-z0-9])', '', $_POST['password'])."<br>";
$result = $conexion->query($sql);


if ($result->num_rows > 0) {     
 }
 $row = $result->fetch_array(MYSQLI_ASSOC);
 
 if ($password == $row['clave']) { 
 
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;
    $_SESSION['start'] = time();
    $_SESSION['expire'] = $_SESSION['start'] + (50 * 60);

    echo "Cargando...! " . $_SESSION['username'];
   header('Location: ../panel.php');

 } else { 
   echo "Username o Password estan incorrectos.";

   echo "<br><a href='login.php'>Volver a Intentarlo</a>";
 }
 mysqli_close($conexion); 
 ?>
